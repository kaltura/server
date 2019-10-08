import sys, getopt, pycaption, io

def main(argv):
   inputfile = ''
   inputType = ''
   outputType = ''

   try:
      opts, args = getopt.getopt(argv,"h:i:f:t:")
   except getopt.GetoptError:
      print( 'test.py -i <inputfile> -f <intputType> -t <outputType>')
      sys.exit(2)
   for opt, arg in opts:
      if opt == '-h':
         print( 'test.py -i <inputfile> -f <intputType> -t <outputType>')
         sys.exit()
      elif opt in ("-i", "--ifile"):
         inputfile=arg
      elif opt in ("-f", "--sfile"):
         inputType=arg
      elif opt in ("-t", "--tfile"):
         outputType=arg

   if inputType == outputType:
        print( 'Error: input type and output type are same format')
        sys.exit(1)

   with io.open(inputfile) as f:
        str1 = f.read()
   inputValue = inputType.lower()

   if inputValue == 'scc':
        c = pycaption.SCCReader().read(str1)
   elif inputValue == 'srt':
        c = pycaption.SRTReader().read(str1)
   elif inputValue == 'dfxp':
        c = pycaption.DFXPReader().read(str1)
   elif inputValue == 'webvtt':
        c = pycaption.WebVTTReader().read(str1)
   else:
       print('Error: invalid input type. <srt/scc/webvtt/dfxp> allowed')
       sys.exit(1)

   outputValue = outputType.lower()
   if outputValue == 'scc':
        print (pycaption.SCCWriter().write(c))
   elif outputValue == 'srt':
        print (pycaption.SRTWriter().write(c))
   elif outputValue == 'dfxp':
        print (pycaption.DFXPWriter().write(c))
   elif outputValue == 'webvtt':
        print (pycaption.WebVTTWriter().write(c))
   else:
        print('Error: invalid output type. <srt/scc/webvtt/dfxp> allowed')
        sys.exit(1)

if __name__ == "__main__":
   main(sys.argv[1:])