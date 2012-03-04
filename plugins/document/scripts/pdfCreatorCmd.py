import win32print
import os.path
import time
import sys

if len(sys.argv) < 4:
    print 'wrong usage of this script. usage: %s {inputFile} {outFile} [--readOnly]' % os.path.dirname(__file__);
    sys.exit(1)

# build the command line
if sys.argv[-1] == '--readonly':
    readOnly = True
    inputFile = sys.argv[-3]
    outputFile = sys.argv[-2]
    commandParams = sys.argv[1:-3]
else:
    readOnly = False
    inputFile = sys.argv[-2]
    outputFile = sys.argv[-1]
    commandParams = sys.argv[1:-2]

commandParams.append('/NoStart')

inputFileExt = os.path.splitext(inputFile)[1].lower()
if readOnly and inputFileExt == '.pdf':
    commandParams.append('/IF"%s"' % inputFile)
    commandParams.append('/OF"%s.pdf"' % outputFile)
else:
    commandParams.append('/PF"%s"' % inputFile)

command = ' '.join(commandParams)

# make sure the default printer is set appropriately
PDF_CREATOR_PRINTER = 'PDFCreator'
if win32print.GetDefaultPrinter() != PDF_CREATOR_PRINTER:
    print 'setting default printer to %s' % PDF_CREATOR_PRINTER
    win32print.SetDefaultPrinter(PDF_CREATOR_PRINTER)

# execute the command
print '\ncommand: %s' % command
os.system(command)

# wait until the printer queue becomes empty
printer = win32print.OpenPrinter('PDFCreator')

while True:
    if len(win32print.EnumJobs(printer, 0, 1, 2)) == 0:
        break
    time.sleep(.5)
