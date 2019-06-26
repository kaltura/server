<?php

class KAMFMediaInfoParser{

    const timestampHexVal = "74696d657374616d70"; // hax representation of the string "timestamp"
    const KalturaSyncPointHexVal = '4b616c7475726153796e63506f696e74'; //hax representation of the string "KalturaSyncPoint"
    const AMFNumberDataTypePrefix ="00";
    const IEEE754DoubleFloatInHexLength = 16;
    const MinAMFSizeToTryParse = 205;
    const MaxAMFDiscontinuanceMS = 1000;
    const MinDistanceBetweenAMFsInMS = 60000;

    protected $ffprobeBin = 'ffprobeKAMFMediaInfoParser';
    protected $filePath;

    public function __construct($filePath, $ffprobeBin=null)
    {
        if (is_null($ffprobeBin)) {

            if (kConf::hasParam('bin_path_ffprobeKAMFMediaInfoParser')) {
                $this->ffprobeBin = kConf::get('bin_path_ffprobeKAMFMediaInfoParser');
            }
        }
        else{
            $this->ffprobeBin = $ffprobeBin;
        }
        if (!file_exists($filePath))
            throw new kApplicativeException(KBaseMediaParser::ERROR_NFS_FILE_DOESNT_EXIST, "File not found at [$filePath]");

        $this->filePath = $filePath;
    }

    // returns an array of KAMFData
    public function getAMFInfo()
    {
        $output = $this->getRawMediaInfo();
        return $this->parseOutput($output);
    }

    // get the raw output of running the command
    public function getRawMediaInfo()
    {
        $cmd = $this->getCommand();
        KalturaLog::debug("Executing '$cmd'");
        $output = shell_exec($cmd);
        if (trim($output) === "")
            throw new kApplicativeException(KBaseMediaParser::ERROR_EXTRACT_MEDIA_FAILED, "Failed to parse media using " . get_class($this));

        return $output;
    }

    protected function getCommand()
    {
        return "{$this->ffprobeBin} -i \"{$this->filePath}\" -select_streams 2:2 -show_streams -show_programs -v quiet -show_data -show_packets -print_format json";
    }

    // parse the output of the command and return an array of string of the form pts;timestamp
    protected function parseOutput($output)
    {
        $amf = array();
        $outputLower = strtolower($output);
        $jsonObj = json_decode($outputLower);

        if (!is_null($jsonObj)) {
            // Check for json decode errors caused by inproper utf8 encoding.
            if (json_last_error() != JSON_ERROR_NONE) $jsonObj = json_decode(utf8_encode($outputLower));

            $jsonObj = $jsonObj->packets;

            foreach ($jsonObj as $tmp) {
                // the first data packet is of smaller size of 205 chars
                if (strlen($tmp->data) > self::MinAMFSizeToTryParse) {
                    $amfTs = $this->getTimestampFromAMF($tmp->data);
                    $amfPts = $tmp->pts;

                    if ($amfTs >= 0 && $this->shouldSaveAMF($amf, $amfTs, $amfPts)) {
                        $amfData = $amfPts . ';' . $amfTs;
                        array_push($amf, $amfData);
                    }
                }
            }
            KalturaLog::debug('amf array: ' . print_r($amf, true));
        }
        else{
            KalturaLog::warning('failed to json_decode. returning an empty AMF array');
        }

        return $amf;
    }

    private function shouldSaveAMF($amfArray, $amfTs, $amfPts){

        if (count($amfArray) == 0) {
            KalturaLog::debug('adding AMF - first in the segment ts= ' . $amfTs . ' pts= ' . $amfPts);
            return true;
        }

        $amfParts = explode(';', $amfArray[count($amfArray)-1]);
        $lastAmfPts = $amfParts[0];
        $lastAmfTs = $amfParts[1];
        $tsDelta = $amfTs - $lastAmfTs;
        $ptsDelta = $amfPts - $lastAmfPts;

        if (abs($tsDelta - $ptsDelta) >=  self::MaxAMFDiscontinuanceMS){
            if ($tsDelta > self::MinDistanceBetweenAMFsInMS) {
                KalturaLog::debug('got discontinuance - adding AMF. ' . 'tsDelta= ' . $tsDelta . ' ptsDelta= ' . $ptsDelta);
                return true;
            }
            else{
                KalturaLog::debug('got discontinuance, but not adding AMF since time from last AMF is less than ' . self::MinDistanceBetweenAMFsInMS . 'ms. tsDelta= ' . $tsDelta . ' ptsDelta= ' . $ptsDelta);
            }
        }
        else{
            KalturaLog::debug('NOT adding AMF. ' . 'tsDelta= ' . $tsDelta . ' ptsDelta= ' . $ptsDelta);
        }
        return false;
    }

    // get the timestamp field of the KalturaSyncPoint.
    // if failed, for example, not a KalturaSyncPoint, return -1
    private function getTimestampFromAMF($AMFData){
        $AMFDataStream = $this->getByteStreamFromFFProbeAMFData($AMFData);

        if (strpos($AMFDataStream, self::KalturaSyncPointHexVal) === false){
            KalturaLog::debug('got AMF not containing KalturaSyncPointHexVal string');
            return -1;
        }

        // look for 74696d657374616d70 which is the hex encoding of "timestamp"
        // this is fallowed by 00 (AMF for Encoded as IEEE 64-bit double-precision floating point number)
        // this is fallowed by a 64bit (8 bytes = 16 chars) of the number
        $pos = strpos($AMFDataStream, self::timestampHexVal);
        $numAsHex = substr($AMFDataStream, $pos + strlen(self::AMFNumberDataTypePrefix) + strlen(self::timestampHexVal), self::IEEE754DoubleFloatInHexLength);
        return $this->hex2float($numAsHex);
    }

    // get number from hex representation of IEEE 754 double-precision binary floating-point format
    private function hex2float($number) {
        // convert hex string to binary
        $binfinal = sprintf("%064b",hexdec($number));

        // first bit is the sign bit
        $sign = substr($binfinal, 0, 1);

        // get and decode exponent
        $exp = substr($binfinal, 1, 11);
        $exp = bindec($exp)-1023;

        // get the significant digits as an array
        $mantissa = "1".substr($binfinal, 12);
        $mantissa = str_split($mantissa);

        $significand=0;
        for ($i = 0; $i < 53; $i++) {
            $significand += (1 / pow(2,$i))*$mantissa[$i];
        }
        return $significand * pow(2,$exp) * ($sign*-2+1);
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