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
        KalturaLog::debug("starting");
		KBatchBase::impersonate($this->job->partnerId);
        $drmPlugin = KalturaDrmClientPlugin::get(KBatchBase::$kClient);
        $profile = $drmPlugin->drmProfile->getByProvider(KalturaDrmProviderType::CENC);
        KBatchBase::unimpersonate();
        $udrmData = $this->getUDRMdata($profile->licenseServerUrl, $profile->signingKey);
        if ($udrmData->success === false)
        {
            $logMsg = "Could not get UDRM Data error message '".$udrmData->errMsg."' ";
            KalturaLog::err($logMsg);
            throw new KOperationEngineException($logMsg);
        }

        if (!isset($this->data->srcFileSyncs) || !isset($this->data->srcFileSyncs[0]))
        {
            $logMsg = "Did not get input file";
            KalturaLog::err($logMsg);
            throw new KOperationEngineException($logMsg);
        }
        $encryptResult = $this->encryptWithEdash($udrmData->data);
        if ($encryptResult->system_ret_val != 0)
        {
            $logMsg = "There was a problem running the packager, got return value '" . $encryptResult->system_ret_val . "' got result '" . $encryptResult->result . "' ";
            KalturaLog::err($logMsg);
            throw new KOperationEngineException($logMsg);
        }

        $mpdResult = $this->createMPD();
        if ($mpdResult->system_ret_val != 0)
        {
            $logMsg = "There was a problem running the mpd generator, got return value '" . $mpdResult->system_ret_val . "' got result '" . $mpdResult->result . "' ";
            KalturaLog::err($logMsg);
            throw new KOperationEngineException($logMsg);
        }

        $mpdOutPath = $this->data->destFileSyncLocalPath.".mpd";
//        $this->data->destFileSyncLocalPath = $this->data->destFileSyncLocalPath.".mp4";

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
        $keyData = new stdClass();
        $keyData->success = false;
        if ($output === false)
        {
            $keyData->errMsg = "Curl had an error '".curl_error($ch)."'";
            return $keyData;
        }
        $keyData->data = json_decode($output);
        if (!isset($keyData->data->key_id))
        {
            $keyData->errMsg = "did not get good result from udrm service, output is '".$output."'";
            return $keyData;
        }
        $keyData->success = true;

        return $keyData;
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
        KalturaLog::debug("Going to run command '".$cmdLine."' ");

        $result = system($cmdLine, $system_ret_val);
        $ret_val = new stdClass();
        $ret_val->result = $result;
        $ret_val->system_ret_val = $system_ret_val;
        return $ret_val;
    }

    private function createMPD()
    {
        $cmdLine = $this->params->exePath."mpd_generator --input=".$this->data->destFileSyncLocalPath.
        ".media_info --output=".$this->data->destFileSyncLocalPath.".mpd ";
        KalturaLog::debug("Going to run command '".$cmdLine."' ");

        $result = system($cmdLine, $system_ret_val);
        $ret_val = new stdClass();
        $ret_val->result = $result;
        $ret_val->system_ret_val = $system_ret_val;
        return $ret_val;
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