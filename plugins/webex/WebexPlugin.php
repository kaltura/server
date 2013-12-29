<?php
/**
 * @package plugins.webex
 */
class WebexPlugin extends KalturaPlugin implements IKalturaImportHandler
{
	const PLUGIN_NAME = 'webex';
	
	const WEBEX_FLAVOR_PARAM_SYS_NAME = 'webex_flavor_params';


	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName() {
		return self::PLUGIN_NAME;
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaImportHandler::handleImportData()
	 */
	public static function handleImportContent($curlInfo,  $importData) {
		if (!($curlInfo->headers['content-length'] < 16000 && $curlInfo->headers['content-type'] == 'text/html'))
			return $importData;
		
		KalturaLog::info('Handle Import data: Webex Plugin');
		$matches = null;
		$recordId = null;
		$cookiesArr = explode(';', $curlInfo->headers["set-cookie"]);
		foreach($cookiesArr as $cookie)
		{
			list($cookieName, $cookieValue) = explode('=', trim($cookie));
			if($cookieName == 'recordId')
				$recordId = $cookieValue;
		}
		
		if (!$recordId)
		{
			KalturaLog::info('recordId value not found - exiting.');
			return $importData;
		}
		
		$data = file_get_contents($importData->destFileLocalPath);
		if(!preg_match("/href='([^']+)';/", $data, $matches))
		{
			KalturaLog::info("Starting URL not found");
			return $importData;
		}
		$url2 = $matches[1];
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_COOKIE, 'DetectionBrowserStatus=3|1|32|1|11|2;'.$curlInfo->headers["set-cookie"]);
		$result = $curlWrapper->exec($url2);
		
		if(!preg_match("/var prepareTicket = '([^']+)';/", $result, $matches))
		{
			KalturaLog::info("prepareTicket parameter not found");
			return $importData;
		}
		$prepareTicket = $matches[1];
		
		if (!preg_match('/function (download\(\).+prepareTicket;)/s', $result, $matches))
		{
			KalturaLog::info("download function not found");
			return $importData;
		}
		
		if (!preg_match('/http.+prepareTicket/', $matches[0], $matches))
		{
			KalturaLog::info("prepareTicket URL not found");
			return $importData;
		}
		
		$url3 = $matches[0];
		$url3 = str_replace(array('"',' ','+', 'recordId', 'prepareTicket=prepareTicket'), array('','','',$recordId, "prepareTicket=$prepareTicket"), $url3);
		
		if (!preg_match('/function (func\_prepare\(.+\).+ticket;)/s', $result, $matches))
		{
			KalturaLog::info("func_prepare function not found");
			return $importData;
		}
		
		if (!preg_match('/http.+ticket/', $matches[0], $matches))
		{
			KalturaLog::info("download URL not found");
			return $importData;
		}
		
		$url4 = $matches[0];
		$url4 = str_replace(array("'",' ','+'), '', $url4);
		
		$status = null;
		for($i = 0; $i < 10; $i++)
		{
			$result = $curlWrapper->exec($url3);
			
			if(!preg_match("/window\.parent\.func_prepare\('([^']+)','([^']*)','([^']*)'\);/", $result, $matches))
			{
				KalturaLog::info("Invalid result returned for prepareTicket request - should contain call to the func_prepare method");
				return $importData;
			}
			$status = $matches[1];
			if($status == 'OKOK')
				break;
				
			sleep(3);
		}
		
		if($status != 'OKOK')
		{
			KalturaLog::info("Invalid result returned for prepareTicket request");
			return $importData;
		}
			
		$ticket = $matches[3];
		
		$url4 = str_replace("ticket=ticket", "ticket=$ticket", $url4);
		
		$curlWrapper->setOpt(CURLOPT_RETURNTRANSFER, false);
		$fileName = pathinfo($importData->destFileLocalPath, PATHINFO_FILENAME);
		$destFileLocalPath = preg_replace("/$fileName\.[\w\d]+/", "$fileName.arf", $importData->destFileLocalPath);
		$importData->destFileLocalPath = $destFileLocalPath;
		KalturaLog::info('destination: ' . $importData->destFileLocalPath);
		$result = $curlWrapper->exec($url4, $importData->destFileLocalPath);
		
		if (!$result)
		{	
			KalturaLog::debug("getError: " . $curlWrapper->getError());
		}
		
		$curlWrapper->close();
		$importData->fileSize = kFile::fileSize($importData->destFileLocalPath);
		
		return $importData;

	}

}