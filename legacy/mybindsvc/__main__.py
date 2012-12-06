import sys, getopt, services

def main(argv):
    if len(argv) == 0:
        print "error: no args specified"
        usage()
        sys.exit(2)
    
    try:
        opts, args = getopt.getopt(
            argv, "hm:d", ["help", "log-file=", "log-level="])
    except getopt.GetoptError:
        print "error: invalid arguments"
        usage()
        sys.exit(2)
    
    service = None
    debug = False
    daemon = True
    log_level = None
    log_file = None
    
    for opt, arg in opts:
        if opt in ("-h", "--help"):
            usage()
            sys.exit()
        
        elif opt in ("-m"):
            if arg == "master":
                service = services.MasterService()
            elif arg == "slave":
                service = services.SlaveService()
            else:
                print "error: unknown mode: %s" % arg
                usage()
                sys.exit(2)
        
        elif opt in ("-d"):
            daemon = True
            print "error: daemon mode not yet implemeneted"
            sys.exit(2)
        
        elif opt in ("--log-level"):
            log_level = arg
        
        elif opt in ("--log-file"):
            log_file = arg
    
    service.log_level = log_level
    service.log_file = log_file
    
    service.start()

def usage():
    print ("usage: %s [-h|-d|-m <mode>] "
           "[--help|--log-level debug|info|warning|error|critical|"
           "--log-file <file>]") % sys.argv[0]

if __name__ == "__main__":
    main(sys.argv[1:])
