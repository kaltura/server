<?php
/**
 * Created by IntelliJ IDEA.
 * User: asafrobinovich
 * Date: 10/7/15
 * Time: 3:34 PM
 */

class KAMFMediaInfoParser extends KBaseMediaParser{

    protected $cmdPath;
    protected $ffmprobeBin;

    /**
     * @param string $filePath
     * @param string $cmdPath
     */
    public function __construct($filePath, $cmdPath="ffmpeg", $ffprobeBin="ffprobe")
    {
        $this->cmdPath = $cmdPath;
        $this->ffprobeBin = $ffprobeBin;
        //parent::__construct($filePath);
    }

    protected function getCommand()
    {
        if(!isset($filePath)) $filePath=$this->filePath;
        return "{$this->ffprobeBin} -i {$filePath} -select_streams 2:2 -show_streams -show_programs -v quiet -show_data -show_packets -print_format json";
    }

    /**
     * @return KalturaMediaInfo
     */
    public function getMediaInfoFromString($str)
    {
        return $this->parseOutput($str);
    }

    // parse the output of the command and return an array of objects
    // - pts
    // - timestamp (unix timestamp)
    // - offset
    protected function parseOutput($output)
    {
        $outputlower = strtolower($output);
        $jsonObj = json_decode($outputlower);

        // Check for json decode errors caused by inproper utf8 encoding.
        if(json_last_error()!=JSON_ERROR_NONE) $jsonObj = json_decode(utf8_encode($outputlower));

        $jsonObj = $jsonObj->packets_and_frames;

        for ($i = 0; $i < count($jsonObj); $i++) {
            //print "$i\n";
            $tmp = $jsonObj[$i];
            print $tmp->pts;
            print "\n";
            print $this->getOffsetFromAMF($tmp->data);
            print "\n";
        }

        return $jsonObj;
    }

    private function getOffsetFromAMF($AMFData){
        $AMFDataStream = $this->getByteStreamFromFFProbeAMFData($AMFData);

        // look for 74696d657374616d70 which is the hex encoding of "timestamp"
        // this is fallowed by 00 (AMF for Encoded as IEEE 64-bit double-precision floating point number)
        // this is fallowed by a 64bit (8 bytes = 16 chars) of the number

        $pos = strpos($AMFDataStream, "74696d657374616d70");

        $ret = substr($AMFDataStream, $pos + 2 + 18, 16);

        print $pos."\n";
        print $AMFDataStream."\n";
        print $ret;

        return $ret;
    }

    // parse the output of ffprobe that looks like:
    //        \n00000000: 0200 0a6f 6e4d 6574 6144 6174 6103 0008  ...onMetaData...
    //        \n00000010: 6475 7261 7469 6f6e 0000 0000 0000 0000  duration........
    //        \n00000020: 0000 0577 6964 7468 0040 8400 0000 0000  ...width.@......
    //        \n00000030: 0000 0668 6569 6768 7400 407e 0000 0000  ...height.@~....
    // and generate a continuous byte stream (as a string)

    private function getByteStreamFromFFProbeAMFData($AMFData)
    {
        $lines = explode("\n", $AMFData);

        $ret = "";
        for ($i = 0; $i < count($lines); $i++) {
            $ret .= str_replace(' ', '', substr($lines[$i], 10, 40));
        }

        return $ret;
    }
}