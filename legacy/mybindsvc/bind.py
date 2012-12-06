import os

class BindUtil:
    
    def generate_zone_file(self, zone):
        
        zone_format = (
            '$TTL %s\n'
            '@ IN SOA ns1.mybind.com. hostmaster.mybind.com. '
            '(%s 3600 600 1209600 3600)\n'
            '@ NS ns1.mybind.com.\n'
            '@ NS ns2.mybind.com.\n'
            '%s\n')
        
        rec_lines = list()
        for rec in zone.records:
            rec_lines.append(self.get_rec_line(rec))
        
        return zone_format % (zone.default_ttl, zone.serial, '\n'.join(rec_lines))
    
    def get_rec_line(self, rec):
        parts = list()
        parts.append(rec.name)
        
        if rec.ttl:
            parts.append(rec.ttl)
        
        parts.append(rec.type)
        
        if rec.aux:
            parts.append(rec.aux)
        
        parts.append(rec.data)
        
        return ' '.join(parts)
    
    def run_cmd(self, cmd):
        import subprocess
        proc = subprocess.Popen(
            cmd,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE)
        
        # captur piped stdout and stderr data
        (stdout, stderr) = proc.communicate()
        
        # return proc (which has return code) and std data
        return (proc, stdout, stderr)
    
    def reload_bind(self, zone_name):
        return self.run_cmd(['/usr/sbin/rndc', 'reload', zone_name])
    
    def reconfig_bind(self):
        return self.run_cmd(['/usr/sbin/rndc', 'reconfig'])
    
    def validate(self, zone_name, zone_path):
        return self.run_cmd(['/usr/sbin/named-checkzone', zone_name, zone_path])
    
    def generate_master_conf(self, zones, zones_dir, allow_transfer):
        
        zone_format = (
            'zone "%s" IN {\n'
            '   type master;\n'
            '   file "%s/db.%s";\n'
            '   allow-transfer { %s };\n'
            '};\n\n'
        )
        
        text = str()
        for zone in zones:
            text += zone_format % (
                zone.name, zones_dir,
                zone.name, allow_transfer
            )
        
        return text
    
    def generate_slave_conf(self, zones, zones_dir, masters):
        
        zone_format = (
            'zone "%s" IN {\n'
            '   type slave;\n'
            '   file "%s/db.%s";\n'
            '   masters { %s };\n'
            '};\n\n'
        )
        
        text = str()
        for zone in zones:
            text += zone_format % (
                zone.name, zones_dir,
                zone.name, masters
            )
        
        return text
