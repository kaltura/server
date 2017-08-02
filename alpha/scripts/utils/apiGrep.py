#!/usr/bin/python
from optparse import OptionParser
import gzip
import sys
import os

def isLineLogStart(curLine):
    if len(curLine) < 20:
        return False
    if (curLine[4] == '-' and curLine[7] == '-' and curLine[10] == ' ' and
            curLine[13] == ':' and curLine[16] == ':'):
        return True
    return False

def parseCmdLine():
	parser = OptionParser(usage='%prog [OPTION]... PATTERN [FILE]...', add_help_option=False)
	parser.add_option("--help", help="display this help and exit", action="help")
	parser.add_option("-h", "--no-filename",
					  action="store_true", dest="noFilename", default=False,
					  help="suppress the file name prefix on output")
	parser.add_option("-H", "--with-filename",
					  action="store_true", dest="withFilename", default=False,
					  help="print the file name for each match")
	return parser.parse_args()

# parse the command line
(options, args) = parseCmdLine()
if len(args) < 1:
	baseName = os.path.basename(__file__)
	print 'Usage: python %s [OPTION]... PATTERN [FILE]...' % baseName
	print 'Try `python %s --help` for more information.' % baseName
	sys.exit(1)

pattern = args[0]
fileNames = args[1:]
if len(fileNames) == 0:
	fileNames = ['-']

if options.withFilename:
	outputFileName = True
elif options.noFilename:
	outputFileName = False
else:
	outputFileName = len(fileNames) > 1

prefix = ''
for fileName in fileNames:
	# open the file
	if fileName == '-':
		inputFile = sys.stdin
	elif fileName.endswith('.gz'):
		inputFile = gzip.GzipFile(fileName, 'r')
	else:
		inputFile = file(fileName, 'r')

	# get the prefix
	if outputFileName:
		if fileName == '-':
			prefix = '(standard input):'
		else:
			prefix = '%s:' % fileName

	# process the file
	output = False
	for curLine in inputFile:
		logStart = isLineLogStart(curLine)
		if output:
			if not logStart:
				print prefix + curLine.rstrip()
				continue
			output = False

		if logStart and pattern in curLine:
			print prefix + curLine.rstrip()
			output = True
