<?php

class KCEncOperationEngine extends KOperationEngine
{
    const URL_EXTENSION = "widevine/encryption?signature=";
    const SYSTEM_NAME = 'OVP';

    /**
	 * @var array
	 * batch job parameters
	 */
	private $params;
	
	public function __construct($params, $outFilePath)
	{
		$this->params = $params;
	}
	
	/* (non-PHPdoc)
	 * @see KOperationEngine::getCmdLine()
	 */
	protected function getCmdLine() {}

	/*
	 * (non-PHPdoc)
	 * @see KOperationEngine::doOperation()
	 * cEnc operation engine
	 */
	protected function doOperation()
	{
		KBatchBase::impersonate($this->job->partnerId);
        $drmPlugin = KalturaDrmClientPlugin::get(KBatchBase::$kClient);
        $profile = $drmPlugin->drmProfile->getByProvider(KalturaDrmProviderType::CENC);
        KBatchBase::unimpersonate();
        $udrmData = $this->getUDRMdata($profile->licenseServerUrl, $profile->signingKey);
        if (!isset($this->data->srcFileSyncs) || !isset($this->data->srcFileSyncs[0]))
        {
            $logMsg = "Did not get input file";
            KalturaLog::err($logMsg);
            throw new KOperationEngineException($logMsg);
        }
        $encryptResult = $this->encryptWithEdash($udrmData);
        $mpdResult = $this->createMPD();
        $mpdOutPath = $this->data->destFileSyncLocalPath.".mpd";
        $fsDescArr = array();
        $fsDesc = new KalturaDestFileSyncDescriptor();
        $fsDesc->fileSyncLocalPath = $mpdOutPath;
        $fsDesc->fileSyncObjectSubType = 7; //FILE_SYNC_ASSET_SUB_TYPE_MPD;
        $fsDescArr[] = $fsDesc;
        $this->data->extraDestFileSyncs  = $fsDescArr;

		return true;
	}

    private function getUDRMdata($licenseServerUrl, $signingKey)
    {

        $jsonPostData = '{"ca_system":"'. self::SYSTEM_NAME .'", "account_id":"'.$this->job->partnerId.'", "content_id":"'. $this->job->entryId.'", "files":"'.$this->data->flavorParamsOutput->flavorParamsId.'"}';
        $signature = self::signDataWithKey($jsonPostData, $signingKey);

        $serviceURL = $licenseServerUrl. "" . self::URL_EXTENSION . "" .$signature;
        $ch = curl_init($serviceURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-type: application/json')	);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPostData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        KalturaLog::debug("calling udrm service '".$serviceURL."' with data '".$jsonPostData."' ");
        $output = curl_exec($ch);
        if ($output === false)
        {
            $logMsg = "Could not get UDRM Data error message 'Curl had an error '".curl_error($ch)."' ";
            KalturaLog::err($logMsg);
            throw new KOperationEngineException($logMsg);

        }
        $ret_val = json_decode($output);
        if (!isset($ret_val->key_id))
        {
            $logMsg = "did not get good result from udrm service, output is '".$output."'";
            KalturaLog::err($logMsg);
            throw new KOperationEngineException($logMsg);
        }
        return $ret_val;
    }


    private function encryptWithEdash($udrmData)
    {
        $inputFile = $this->data->srcFileSyncs[0]->actualFileSyncLocalPath;
        $keyIdDecoded = self::strToHex(base64_decode($udrmData->key_id));
        $udrmKey = self::strToHex(base64_decode($udrmData->key));
        $cmdLine = $this->params->exePath."packager input=".$inputFile.",".$this->operator->params.",output=".$this->data->destFileSyncLocalPath.
            " --output_media_info --enable_fixed_key_encryption --key_id ".$keyIdDecoded." --key ".$udrmKey;
        foreach ($udrmData->pssh as $currPssh)
        {
            $psshEncoded = self::strToHex(base64_decode($currPssh->data));
            $cmdLine .= " --pssh ".$psshEncoded;
        }
        KalturaLog::info("Going to run command '".$cmdLine."' ");

        $result = system($cmdLine, $system_ret_val);
	    if ($system_ret_val != 0)
	    {
		    $logMsg = "There was a problem running the packager, got return value '$system_ret_val' got result '$result' ";
		    KalturaLog::err($logMsg);
		    throw new KOperationEngineException($logMsg);
	    }

        return $result;
    }

    private function createMPD()
    {
        $cmdLine = $this->params->exePath."mpd_generator --input=".$this->data->destFileSyncLocalPath.
        ".media_info --output=".$this->data->destFileSyncLocalPath.".mpd ";
        KalturaLog::info("Going to run command '".$cmdLine."' ");

        $result = system($cmdLine, $system_ret_val);
	    if ($system_ret_val != 0)
	    {
		    $logMsg = "There was a problem running the mpd generator, got return value '$system_ret_val' got result '$result' ";
		    KalturaLog::err($logMsg);
		    throw new KOperationEngineException($logMsg);
	    }

        return $result;
    }

    public static function strToHex($string)
    {
        $hex='';
        for ($i=0; $i < strlen($string); $i++)
        {
            $currChar = dechex(ord($string[$i]));
            if (strlen($currChar) == 1)
                $currChar = "0".$currChar;
            $hex .= $currChar;
        }
        return strtoupper($hex);
    }

    public static function signDataWithKey($dataToSign, $signingKey)
    {
        return urlencode(base64_encode(sha1($signingKey.$dataToSign,TRUE)));
    }

}