<?php

class KCEncOperationEngine extends KOperationEngine
{
	CONST SYSTEM_NAME = 'OVP';

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
        KalturaLog::debug("called");

		KBatchBase::impersonate($this->job->partnerId);
        $drmPlugin = KalturaDrmClientPlugin::get(KBatchBase::$kClient);
        $profile = $drmPlugin->drmProfile->getByProvider(KalturaDrmProviderType::CENC);
        KBatchBase::unimpersonate();
        $udrmData = $this->getUDRMdata($profile->licenseServerUrl, $profile->signingKey);
        if (!isset($udrmData))
        {
            throw new KOperationEngineException("Could not get UDRM Data");
        }

        if (!isset($this->data->srcFileSyncs) || !isset($this->data->srcFileSyncs[0]))
        {
            $logMsg = "Did not get input file";
            KalturaLog::notice($logMsg);
            throw new KOperationEngineException($logMsg);
        }
        $inputFile = $this->data->srcFileSyncs[0]->actualFileSyncLocalPath;
        $this->encryptWithEdash($udrmData);
        $this->createMPD();

        $mpdOutPath = $this->data->destFileSyncLocalPath.".mpd";
        $this->data->destFileSyncLocalPath = $this->data->destFileSyncLocalPath.".mp4";
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

        $jsonPostData = '{"ca_system":"'.KCEncOperationEngine::SYSTEM_NAME.'", "account_id":"'.$this->job->partnerId.'", "content_id":"'. $this->job->entryId.'", "file_id":"'.$this->data->flavorParamsOutput->flavorParamsId.'"}';
        $signature = urlencode(base64_encode(sha1($signingKey.$jsonPostData,TRUE)));

        $serviceURL = $licenseServerUrl."widevine/encryption?signature=".$signature;
        $ch = curl_init($serviceURL);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-type: application/json')	);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPostData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        KalturaLog::debug("calling udrm service '".$serviceURL."' with data '".$jsonPostData."' ");
        $output = curl_exec($ch);
        if ($output === false)
        {
            KalturaLog::notice("Curl had an error '".curl_error($ch)."'");
            return null;
        }
        $keyData = json_decode($output);
        if (!isset($keyData->key_id))
        {
            KalturaLog::notice("did not get good result from udrm service, output is '".$output."'");
            return null;
        }
        return $keyData;
    }


    private function encryptWithEdash($udrmData)
    {
        $inputFile = $this->data->srcFileSyncs[0]->actualFileSyncLocalPath;
        $keyIdDecoded = self::strToHex(base64_decode($udrmData->key_id));
        $udrmKey = self::strToHex(base64_decode($udrmData->key));
        $cmdLine = $this->params->exePath."packager input=".$inputFile.",".$this->operator->params.",output=".$this->data->destFileSyncLocalPath.".mp4 ".
            "--output_media_info --enable_fixed_key_encryption --key_id ".$keyIdDecoded." --key ".$udrmKey;
        foreach ($udrmData->pssh as $currPssh)
        {
            $psshEncoded = self::strToHex(base64_decode($currPssh->data));
            $cmdLine .= " --pssh ".$psshEncoded;
        }
        KalturaLog::debug("Going to run command '".$cmdLine."' ");

        $result = system($cmdLine, $ret_val);
        if ($ret_val != 0)
        {
            $logMsg = "There was a problem running the packager, got return value '" . $ret_val . "' got result '" . $result . "' ";
            KalturaLog::err($logMsg);
            throw new KOperationEngineException($logMsg);
        }
        return $result;
    }

    private function createMPD()
    {
        $inputFile = $this->data->srcFileSyncs[0]->actualFileSyncLocalPath;
        $cmdLine = $this->params->exePath."mpd_generator --input=".$this->data->destFileSyncLocalPath.".mp4.media_info --output=".$this->data->destFileSyncLocalPath.".mpd ";
        KalturaLog::notice("Going to run command '".$cmdLine."' ");

        $result = system($cmdLine, $ret_val);
        if ($ret_val != 0)
        {
            $logMsg = "There was a problem running the mpd generator, got return value '" . $ret_val . "' got result '" . $result . "' ";
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


}