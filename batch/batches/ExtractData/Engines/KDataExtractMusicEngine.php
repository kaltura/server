<?php
/**
 *
 * @package Scheduler
 * @subpackage DataExtracter.engines
 */

class KDataExtractMusicEngine extends KDataExtractEngine
{

    const REQUEST_URL = 'http://identify-eu-west-1.acrcloud.com/v1/identify';
    const ACCESS_KEY = 'ed5151427d8f5480186c92a10c802707';
    const ACCESS_SECRET = '2BGJPChRIBTPxQfI5Xy1S4sBLChKXMJ5cvD55bvU';

    CONST PYTHON_EXE_CMD = 'python /tmp/david/python2.7/musicExtarct.py';

    public function getSubType()
    {
        return KalturaEventType::MUSIC;
    }

    public function extractData(KalturaFileContainer $fileContainer, $extraParams = array())
    {
        return $this->extractFromPythonSDK($fileContainer->filePath, 600);

        $http_method = "POST";
        $http_uri = "/v1/identify";
        $data_type = "audio";
        $signature_version = "1" ;
        $timestamp = time() ;

        $requrl = self::REQUEST_URL;
        $access_key =  self::ACCESS_KEY;
        $access_secret =  self::ACCESS_SECRET;

        $string_to_sign = $http_method . "\n" .
            $http_uri ."\n" .
            $access_key . "\n" .
            $data_type . "\n" .
            $signature_version . "\n" .
            $timestamp;
        $signature = hash_hmac("sha1", $string_to_sign, $access_secret, true);

        $signature = base64_encode($signature);

        // supported file formats: mp3,wav,wma,amr,ogg, ape,acc,spx,m4a,mp4,FLAC, etc
        // File size: < 1M , You'de better cut large file to small file, within 15 seconds data size is better
        $file = $fileContainer->filePath;
        $filesize = $fileContainer->fileSize;
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        KalturaLog::debug("filePath: " . $file . "filePath: " . $filesize . "extension: " . $ext);
        $cfile = class_exists('CurlFile', false) ? new CURLFile($file, $ext, basename($file)) : "@{$file}";

        $postfields = array(
            "sample" => $cfile,
            "sample_bytes"=>$filesize,
            "access_key"=>$access_key,
            "data_type"=>$data_type,
            "signature"=>$signature,
            "signature_version"=>$signature_version,
            "timestamp"=>$timestamp);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        KalturaLog::debug("acrCloud data: " . print_r($result,true));
        curl_close($ch);

        $obj = json_decode($result,true);
        echo(print_r($obj, true));

        if($obj['status']['code'] != 0 )
            return null;

        $musicData = array();
        $startTimes = array(5000, 15000, 25000, 35000);
        foreach ($obj['metadata']['music'] as $song)
        {
            $songDetails = array();
            $songDetails[self::START_TIME_FIELD] = array_shift($startTimes);
            //$songDetails['startTime'] = $song['play_offset_ms']; // sample_begin_time_offset_ms -> need to trade to
            $data =  array('name' => $song['title'],
                'artist' => $song['artists'][0]['name'],
                'album' => $song['album']['name'],
                'spotifyId' => $song['external_metadata']['spotify']['track']['id']);
            $songDetails[self::DATA_FIELD] = json_encode($data);
            $musicData[] = $songDetails;
        }

        KalturaLog::debug("music data: " . print_r($musicData, true));


        return $musicData;
    }

    private function extractFromPythonSDK($path, $duration)
    {
        $musicData = array();
        $cmd = self::PYTHON_EXE_CMD . " $path ";
        for($i = 0 ; $i < $duration; $i =+ 10)
        {
            $output = shell_exec($cmd . $i);
            KalturaLog::info("Excute: $cmd");
            $data = $this->buildDataFromOutput($output, $i*1000);
            if ($data && !self::checkIfSongAlreadyExist($musicData, $data))
                $musicData[] = $data;
        }
        return $musicData;
    }

    private function buildDataFromOutput($output, $offset)
    {
        $obj = json_decode($output,true);
        echo(print_r($obj, true));

        if($obj['status']['code'] != 0 )
            return null;

        $song = $obj['metadata']['music'][0];
        $songDetails[self::START_TIME_FIELD] = $offset;
        $data =  array('name' => $song['title'],
            'artist' => $song['artists'][0]['name'],
            'album' => $song['album']['name'],
            'spotifyId' => $song['external_metadata']['spotify']['track']['id']);
        $songDetails[self::DATA_FIELD] = json_encode($data);

        return $songDetails;
    }

    private static function getSongName($songDetails)
    {
        $data = json_decode($songDetails[self::DATA_FIELD]);
        return $data->name;
    }

    private static function checkIfSongAlreadyExist($musicDataList, $newSongDetails)
    {
        $newSongName = self::getSongName($newSongDetails);
        foreach($musicDataList as $song)
        {
            if ($newSongName == self::getSongName($song))
                return true;
        }
        return false;
    }

}