from optparse import OptionParser
import socket
import sys

COLUMN_NAMES = [
    ('a', 'Action'),
    ('c', 'Cached'),
    ('l', 'ClientTag'),
    ('d', 'Database'),
    ('r', 'ErrorCode'),
    ('e', 'EventType'),
    #('x', 'ExecutionTime'),
    ('i', 'IpAddress'),
    ('k', 'KsType'),
    ('m', 'Multirequest'),
    ('p', 'PartnerId'),
    ('q', 'QueryType'),
    ('s', 'Server'),
    ('t', 'Table'),
    ('u', 'UniqueId'),
]

def parseAddress(addressStr):
    address, port = addressStr.split(':')
    return (address, int(port))

class NoEpilogFormattingOptionParser(OptionParser):
        def format_epilog(self, formatter):
            return self.epilog

if __name__ == '__main__':
    columnNamesDesc = '\n'.join(map(lambda x: '   %s\t%s' % x, COLUMN_NAMES))
    # parse the command line
    parser = NoEpilogFormattingOptionParser()
    parser.add_option("-t", "--tcp-address", dest="tcpAddress",default="127.0.0.1:6005",
                      help="the TCP address to listen on", metavar="ADDR")
    parser.add_option("-g", "--group-by", dest="groupBy",default="-",
                      help="fields to group the data by", metavar="FIELDS")
    parser.add_option("-s", "--select", dest="select",default="",
                      help="fields to select for each group", metavar="FIELDS")
    parser.add_option("-f", "--filter", dest="filter",default="",
                      help="filter definition - format is <field1><operator1><value1>, <field2><operator2><value2>,...", metavar="FILTER")
    parser.epilog = """
Field identifiers:
%s

Event types:
   start \tAPI call start
   end   \tAPI call end
   db    \tDB read/write
   sphinx\tSphinx read/write
   
Filter operators:
   =\tEquals
   ~\tContains
   !=\tNot equals
   !~\tNot contains

Templates:
  API:
    Top APIs by count
\tpython apimonClient.py -g pa -f e=start | sort -gr | head

    Top uncached APIs by count
\tpython apimonClient.py -g pa -f e=start,c=False | sort -gr | head

    Top uncached APIs by ip address and count
\tpython apimonClient.py -g pia -f e=start,c=False | sort -gr | head

    Top uncached APIs by execution time
\tpython apimonClient.py -g pa -f e=end | sort -grk2 | head

  API errors:
    Top API errors by count
\tpython apimonClient.py -g pr -f e=end,r\!=None | sort -gr | head

    API errors by partner
\tpython apimonClient.py -g pra -f e=end,r\!=None,p=1234 | sort -gr

  Database:
    Top DB actions by execution time
\tpython apimonClient.py -g pqdt -f e=db | sort -grk2 | head

    Top DB read actions by execution time
\tpython apimonClient.py -g pqdt -f e=db,q=SELECT | sort -grk2 | head

    Top DB write actions by execution time
\tpython apimonClient.py -g pqdt -f e=db,q\!=SELECT | sort -grk2 | head

  Sphinx:
    Top sphinx actions by execution time
\tpython apimonClient.py -g pqdt -f e=sphinx | sort -grk2 | head

    Top sphinx read actions by execution time
\tpython apimonClient.py -g pqdt -f e=sphinx,q=SELECT | sort -grk2 | head

    Top sphinx write actions by execution time
\tpython apimonClient.py -g pqdt -f e=sphinx,q\!=SELECT | sort -grk2 | head

""" % columnNamesDesc
    (options, args) = parser.parse_args()

    # connect to the server
    sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    sock.connect(parseAddress(options.tcpAddress))

    # send the command
    sock.send('%s/%s/%s\n' % (options.filter, options.groupBy, options.select))

    # read the output
    while 1:
        data = sock.recv(1024)
        if data == "":
            break
        sys.stdout.write(data)
    sock.close()
