import sys, time, ConfigParser, os, logging, logging.handlers, re
from bind import BindUtil

LEVELS = {'debug' : logging.DEBUG,
          'info' : logging.INFO,
          'warning' : logging.WARNING,
          'error' : logging.ERROR,
          'critical' : logging.CRITICAL}

class GeneralConf:
    pass

class BindConf:
    pass

class DbConf:
    pass

class ZoneDb:
    
    def __init__(self, file):
        self.file = file
        
        if not os.path.exists(file):
            file_handle = open(file, 'w')
            file_handle.close()
    
    def gen_line(self, id, name):
        return '%s:%s\n' % (id, name)
    
    def zones(self):
        
        class Zone:
            pass
        
        file = open(self.file, 'r')
        
        zones = list()
        for line in file.readlines():
            parts = line.split(':')
            zone = Zone()
            zone.id = int(parts[0])
            zone.name = parts[1][:-1] # discard newline at end
            zones.append(zone)
        
        file.close()
        return zones
    
    def lines(self):
        if os.path.exists(self.file):
            file = open(self.file, 'r')
            lines = file.readlines()
            file.close()
        else:
            lines = list()
        return lines

    def add(self, id, name):
        lines = self.lines()
        
        # find and remove old line if any (based on id), then add new one
        for line in lines:
            if (line.find('%s:' % id) != -1):
                lines.remove(line)
        lines.append(self.gen_line(id, name))
        
        file = open(self.file, 'w')
        file.writelines(lines)
    
    def rename(self, id, name):
        # add will replace any existing with matching id 
        self.add(id, name)
    
    def remove(self, id, name):
        lines = self.lines()
        
        # find, then remove the line by reference
        for line in lines:
            if (line.find('%s:' % id) != -1):
                lines.remove(line)
        
        file = open(self.file, 'w')
        file.writelines(lines)

class Service(object):
    conf = GeneralConf()
    bind_conf = BindConf()
    db_conf = DbConf()
    bind = BindUtil()
    log_file = None
    log_level = None
    
    def __init__(self):
        pass
    
    def setup_logging(self):
        
        # get level number from text name
        level_num = LEVELS.get(self.log_level, logging.INFO)
        
        # create logger
        self.log = logging.getLogger()
        self.log.setLevel(level_num)
        
        # create formatter, add formatter to ch, add ch to logger
        formatter = logging.Formatter("%(asctime)s - %(levelname)s - %(message)s")
        
        if self.log_file:
            # add log rotator so we don't use too much space
            print "logging to file: %s" % self.log_file
            fh = logging.handlers.RotatingFileHandler(
                self.log_file, maxBytes=1048576, backupCount=10)
            fh.setLevel(level_num)
            fh.setFormatter(formatter)
            self.log.addHandler(fh)
        else:
            # console logger
            ch = logging.StreamHandler()
            ch.setLevel(level_num)
            ch.setFormatter(formatter)
            self.log.addHandler(ch)
    
    def start(self):
        
        print "MyBind service starting"
        
        # setup logging (could be to file)
        self.setup_logging()
        
        self.log.debug("reading config...")
        config = ConfigParser.ConfigParser()
        config.read("mybindsvc/mybindsvc.ini")
        self.read_config(config)
        
        try:
            self._run()
            
        except KeyboardInterrupt:
            self.log.info("keyboard interrupt")
            self.exit()
    
    def read_config(self, config):
        
        # from [general] section
        self.conf.slave_host = config.get("general", "slave_host")
        self.conf.slave_port = config.getint("general", "slave_port")
        self.conf.rpc_key = config.get("general", "rpc_key")
        
        # from [bind] section
        self.bind_conf.conf_dir = config.get("bind", "conf_dir")
    
    def exit(self):
        self.log.info("exiting...")
        sys.exit()
    
    def _run(self):
        pass
    
    def handle_rndc_cmd(self, (proc, stdout, stderr)):
        if stdout:
            self.log.info('rndc: %s' % stdout[:-1])
        
        if stderr:
            raise Exception(stderr[:-1])
    
    def handle_rndc_validate_cmd(self, (proc, stdout, stderr)):
        if stdout:
            # validate does not send to stderr, so detect in stdout...
            m = re.search('.*:\d+: (.*)', stdout)
            if m:
                # throw interesting messages if any
                raise Exception(m.group(1))
            
            # otherwise, assume no error
            self.log.info('rndc: %s' % stdout[:-1])
        
        if stderr:
            raise Exception(stderr[:-1])
    
    def reconfig_bind(self):
        self.handle_rndc_cmd(self.bind.reconfig_bind())
    
    def reload_bind(self, zone_name):
        zone_path = self.get_zone_filepath(zone_name)
        self.handle_rndc_validate_cmd(self.bind.validate(zone_name, zone_path))
        self.handle_rndc_cmd(self.bind.reload_bind(zone_name))

class MasterService(Service):
    
    def __init__(self):
        pass
    
    def read_config(self, config):
        super(MasterService, self).read_config(config)
        
        # from [general] section
        self.conf.master_zone_db = config.get("general", "master_zone_db")
        
        # from [db] section
        self.db_conf.engine = config.get("db", "engine")
        if self.db_conf.engine == 'sqlite3':
            self.db_conf.file = config.get("db", "file")
            
        elif self.db_conf.engine == 'mysql':
            self.db_conf.host = config.get("db", "host")
            self.db_conf.user = config.get("db", "user")
            self.db_conf.password = config.get("db", "password")
            self.db_conf.name = config.get("db", "name")
            
        else:
            raise Exception("Unrecognised db engine: %s" % self.db_conf.engine)
        
        # from [bind] section
        self.bind_conf.master_zones_dir = config.get("bind", "master_zones_dir")
        self.bind_conf.master_conf = config.get("bind", "master_conf")
        self.bind_conf.transfer = config.get("bind", "transfer")
        
        self.zone_db = ZoneDb(self.conf.master_zone_db)

    def set_deleted(self, c, id, deleted):
        c.execute(
            "update mybindweb_dnszone set deleted = %s "
            "where id = %s" % (self.pstyle, self.pstyle), (deleted, id,)
        )
    
    def set_sync(self, c, zone, state, cmd = None, msg = None):
        
        # retain original cmd if not specified
        if not cmd:
            cmd = zone.sync_cmd
        
        # when state is ok, cmd *must* be ok
        if state == 'OK':
            cmd = 'OK'
        
        self.log.debug('state: %s, cmd: %s' % (state, cmd))
        
        c.execute(
            "update mybindweb_dnszone set "
            "sync_state = %s, sync_cmd = %s, sync_msg = %s "
            "where id = %s" %
            (self.pstyle, self.pstyle, self.pstyle, self.pstyle),
            (state, cmd, str(msg), zone.id)
        )

    def get_zone_filepath(self, name):
        return '%s/%s/db.%s' % (
            self.bind_conf.conf_dir,
            self.bind_conf.master_zones_dir,
            name
        )
    
    def write_zone_file(self, zone):
        text = self.bind.generate_zone_file(zone)
        path = self.get_zone_filepath(zone.name)
        
        self.log.debug('writing: %s' % path)
        
        file = open(path, 'w')
        file.write(text)
        file.close()

    def update_zone_conf_list(self):
        zones = self.zone_db.zones()
        
        zones_dir = '%s/%s' % (
            self.bind_conf.conf_dir,
            self.bind_conf.master_zones_dir)
        
        text = self.bind.generate_master_conf(
            zones, zones_dir, self.bind_conf.transfer)
        path = '%s/%s' % (self.bind_conf.conf_dir, self.bind_conf.master_conf)
        
        self.log.debug('writing: %s' % path)
        
        file = open(path, 'w')
        file.write(text)
        file.close()

    def create_zone(self, c, zone):
        self.log.debug('creating...')
        
        # write the actual zone file
        self.write_zone_file(zone)
        
        # add to local zone db/cache
        self.zone_db.add(zone.id, zone.name)
        
        # refresh the conf list of zones
        self.update_zone_conf_list()
        
        # tell the slave to add to the zone conf list
        self.slave.add(zone.id, zone.name, self.conf.rpc_key)
        
        # zones are re-created after being deleted, so always un-delete
        self.set_deleted(c, zone.id, False)
        
        # finally, reconfig bind to load the new zone and push to slave
        self.log.debug('running rndc reconfig')
        self.reconfig_bind()

    def update_zone(self, c, zone):
        self.log.debug('updating...')
        
        # write the actual zone file
        self.write_zone_file(zone)
        
        # rename in local zone db/cache
        self.zone_db.rename(zone.id, zone.name)
        
        # refresh the conf list of zones
        self.update_zone_conf_list()
        
        if zone.renamed:
            self.slave.rename(zone.id, zone.name, self.conf.rpc_key)
            
            # reconfig bind, since zone has been renamed
            self.log.debug('running rndc reconfig')
            self.reconfig_bind()
        else:
            # if not renamed, just reload single zone
            self.log.debug('running rndc reload')
            self.reload_bind(zone.name)

    def delete_zone(self, c, zone):
        self.log.debug('deleting...')
        
        # add to local zone db/cache
        self.zone_db.remove(zone.id, zone.name)
        
        # refresh the conf list of zones
        self.update_zone_conf_list()
        
        # delete the actual zone file
        path = self.get_zone_filepath(zone.name)
        if os.path.exists(path):
            self.log.debug('delete: %s' % path)
            os.remove(path)
        
        # tell the slave to remove from zone conf list
        self.slave.remove(zone.id, zone.name, self.conf.rpc_key)
        
        self.set_deleted(c, zone.id, True)
        
        # finally, reload bind (also pushes zone to slave)
        self.log.debug('running rndc reconfig')
        self.reconfig_bind()

    def _run(self):
        
        self.log.info("starting master...")
        
        if self.db_conf.engine == 'sqlite3':
            self.pstyle = '?'
            self.log.debug("connecting to sqlite3 file: %s" % self.db_conf.file)
            import sqlite3
            conn = sqlite3.connect(self.db_conf.file)
        
        elif self.db_conf.engine == 'mysql':
            self.pstyle = '%s'
            self.log.debug("connecting to mysql host: %s" % self.db_conf.host)
            import MySQLdb
            conn = MySQLdb.connect(
                host = self.db_conf.host,
                user = self.db_conf.user,
                passwd = self.db_conf.password,
                db = self.db_conf.name)
            
        else:
            raise Exception("Unrecognised db engine: %s" % self.db_conf.engine)
        
        c = conn.cursor()
        
        import xmlrpclib
        
        url = 'http://%s:%i' % (
            self.conf.slave_host, self.conf.slave_port)
        
        self.log.debug('using slave url: %s' % url)
        self.slave = xmlrpclib.ServerProxy(url)
        
        class Zone:
            def __init__(self, row):
                # unpack sql tuple
                (
                    self.id,
                    self.name,
                    self.sync_cmd,
                    self.sync_state,
                    self.renamed,
                    self.default_ttl,
                    self.serial
                ) = row
        
        class Record:
            def __init__(self, row):
                (
                    self.name,
                    self.ttl,
                    self.type,
                    self.aux,
                    self.data
                ) = row
        
        while (True):
            
            # select out of sync zone ids
            c.execute(
                "select id "
                "from mybindweb_dnszone "
                "where sync_state != 'OK'"
            )
            
            # iterate by id, since details can change while we're iterating
            for id_row in c:
                (id,) = id_row
            
                c.execute(
                    "select id, name, sync_cmd, sync_state, "
                    "renamed, default_ttl, serial "
                    "from mybindweb_dnszone "
                    "where id = " + str(id)
                )
                
                # HACK: how do we select single rows?
                z = None
                for row in c:
                    z = Zone(row)
                
                if not z:
                    raise Exception('zone not found with id: ' + id)
                
                try:
                    self.log.info('syncing zone: %s' % z.name)
                    
                    # first, lock so user can't cause problems by changing the
                    # zone mid-sync
                    self.set_sync(c, z, 'SA')
                    conn.commit()
                    
                    # warning: nested sql - get records for zone
                    c.execute("select name, ttl, type, aux, data "
                              "from mybindweb_dnsrecord "
                              "where zone_id = %s" % self.pstyle, (z.id,))
                    
                    z.records = list()
                    for rec_row in c:
                        z.records.append(Record(rec_row))
                    
                    if z.sync_cmd == 'CP':
                        self.create_zone(c, z)
                    
                    elif z.sync_cmd == 'UP':
                        self.update_zone(c, z)
                    
                    elif z.sync_cmd == 'DP':
                        self.delete_zone(c, z)
                        
                    else:
                        raise Exception('unknwon sync_cmd: ' + z.sync_cmd)
                    
                    # if sync was ok, set status
                    self.log.info('sync ok')
                    self.set_sync(c, z, 'OK')
                    conn.commit()
                    
                    # on each zone sync, sleep
                    time.sleep(2)
                    
                except Exception as ex:
                    self.log.error(ex)
                    
                    # make sure we retain the sync state for when we retry
                    self.set_sync(c, z, 'SE', msg=ex)
                    conn.commit()
                    
                    # if errors are occurring, slow down
                    time.sleep(5)
        
            # on each db poll, sleep 10
            time.sleep(10)
        
        conn.close()

class SlaveService(Service):
    
    def __init__(self):
        pass
    
    def read_config(self, config):
        super(SlaveService, self).read_config(config)
        
        # from [general] section
        self.conf.slave_zone_db = config.get("general", "slave_zone_db")
        
        # from [bind] section
        self.bind_conf.slave_zones_dir = config.get("bind", "slave_zones_dir")
        self.bind_conf.slave_conf = config.get("bind", "slave_conf")
        self.bind_conf.masters = config.get("bind", "masters")
        
        self.zone_db = ZoneDb(self.conf.slave_zone_db)

    def update_zone_conf_list(self):
        zones = self.zone_db.zones()
        zones_dir = '%s/%s' % (
            self.bind_conf.conf_dir,
            self.bind_conf.slave_zones_dir)
        
        text = self.bind.generate_slave_conf(
            zones, zones_dir, self.bind_conf.masters)
        path = '%s/%s' % (self.bind_conf.conf_dir, self.bind_conf.slave_conf)
        
        self.log.debug('writing: %s' % path)
            
        file = open(path, 'w')
        file.write(text)
        file.close()
    
    def _run(self):
        self.log.info("starting slave...")
        
        from SimpleXMLRPCServer import SimpleXMLRPCServer
        from SimpleXMLRPCServer import SimpleXMLRPCRequestHandler
        
        # Restrict to a particular path.
        class RequestHandler(SimpleXMLRPCRequestHandler):
            rpc_paths = ('/RPC2',)
        
        # Create server
        server = SimpleXMLRPCServer(
            (self.conf.slave_host, self.conf.slave_port),
            requestHandler=RequestHandler,
            allow_none=True)
        
        server.register_introspection_functions()
        
        def add(id, name, key):
            assert(key == self.conf.rpc_key)
            try:
                self.log.debug('add: %s' % name)
                self.zone_db.add(id, name)
                self.update_zone_conf_list()
                self.log.debug('running rndc reconfig')
                self.reconfig_bind()
                
            except Exception as ex:
                self.log.error(ex)
                raise
        
        def rename(id, name, key):
            assert(key == self.conf.rpc_key)
            try:
                self.log.debug('rename: %s' % name)
                self.zone_db.rename(id, name)
                self.update_zone_conf_list()
                self.log.debug('running rndc reconfig')
                self.reconfig_bind()
                
            except Exception as ex:
                self.log.error(ex)
                raise
        
        def remove(id, name, key):
            assert(key == self.conf.rpc_key)
            try:
                self.log.debug('remove: %s' % name)
                self.zone_db.remove(id, name)
                self.update_zone_conf_list()
                self.log.debug('running rndc reconfig')
                self.reconfig_bind()
                
            except Exception as ex:
                self.log.error(ex)
                raise
        
        server.register_function(add, 'add')
        server.register_function(rename, 'rename')
        server.register_function(remove, 'remove')
        
        # Run the server's main loop
        server.serve_forever()
