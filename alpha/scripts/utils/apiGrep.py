#!/usr/bin/python
from optparse import OptionParser
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
	parser.add_option("--label", dest="stdinLabel", default="(standard input)", metavar="LABEL", 
					  help="use LABEL as the standard input file name prefix")
	return parser.parse_args()

def shellQuote(s):
    return "'" + s.replace("'", "'\\''") + "'"

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
	if fileName.endswith('.gz'):
		# using zcat | python is faster than using python's gzip module
		params = [__file__, '--label=' + fileName]
		if outputFileName:
			params.append('-H')
		params.append(pattern)
		params = ' '.join(map(shellQuote, params))
		cmdLine = "gzip -cd %s | python %s" % (shellQuote(fileName), params)
		if os.system(cmdLine) != 0:
			break
		continue

	if fileName == '-':
		inputFile = sys.stdin
	else:
		inputFile = file(fileName, 'r')

	# get the prefix
	if outputFileName:
		if fileName == '-':
			prefix = options.stdinLabel + ':'
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
