import sys, getopt, pycaption
import io

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

   if inputType.lower() in ['scc']:
        c = pycaption.SCCReader().read(str1)
   elif inputType.lower() in ['srt']:
        c = pycaption.SRTReader().read(str1)
   elif inputType.lower() in ['dfxp']:
        c = pycaption.DFXPReader().read(str1)
   elif inputType.lower() in ['webvtt']:
        c = pycaption.WebVTTReader().read(str1)
   else:
       print('Error: invalid input type. <srt/scc/webvtt/dfxp> allowed')
       sys.exit(1)

   if outputType.lower() in ['scc']:
        print (pycaption.SCCWriter().write(c))
   elif outputType.lower() in ['srt']:
        print (pycaption.SRTWriter().write(c))
   elif outputType.lower() in ['dfxp']:
        print (pycaption.DFXPWriter().write(c))
   elif outputType.lower() in ['webvtt']:
        print (pycaption.WebVTTWriter().write(c))
   else:
        print('Error: invalid output type. <srt/scc/webvtt/dfxp> allowed')
        sys.exit(1)

if __name__ == "__main__":
   main(sys.argv[1:])