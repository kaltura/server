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
	public static function handleImportContent($curlInfo,  $importData, $params) {
		if (!($curlInfo->headers['content-length'] < 16000 && $curlInfo->headers['content-type'] == 'text/html'))
			return null;
		
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
			return null;
		}
		
		$data = file_get_contents($importData->destFileLocalPath);
		if(!preg_match("/href='([^']+)';/", $data, $matches))
		{
			KalturaLog::err("Starting URL not found");
			return null;
		}
		$url2 = $matches[1];
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_COOKIE, 'DetectionBrowserStatus=3|1|32|1|11|2;'.$curlInfo->headers["set-cookie"]);
		$result = $curlWrapper->exec($url2);
		
		if(!preg_match("/var prepareTicket = '([^']+)';/", $result, $matches))
		{
			KalturaLog::err("prepareTicket parameter not found");
			return null;
		}
		$prepareTicket = $matches[1];
		
		if (!preg_match('/function (download\(\).+prepareTicket;)/s', $result, $matches))
		{
			KalturaLog::err("download function not found");
			return null;
		}
		
		if (!preg_match('/http.+prepareTicket/', $matches[0], $matches))
		{
			KalturaLog::err("prepareTicket URL not found");
			return null;
		}
		
		$url3 = $matches[0];
		$url3 = str_replace(array('"',' ','+', 'recordId', 'prepareTicket=prepareTicket'), array('','','',$recordId, "prepareTicket=$prepareTicket"), $url3);
		
		if (!preg_match('/function (func\_prepare\(.+\).+ticket;)/s', $result, $matches))
		{
			KalturaLog::err("func_prepare function not found");
			return null;
		}
		
		if (!preg_match('/http.+ticket/', $matches[0], $matches))
		{
			KalturaLog::err("download URL not found");
			return null;
		}
		
		$url4 = $matches[0];
		$url4 = str_replace(array("'",' ','+'), '', $url4);
		
		$status = null;
		$iterations = (isset($params->webex->iterations) && !is_null($params->webex->iterations)) ? intval($params->webex->iterations ) : 10;
		$sleep = (isset($params->webex->sleep) && !is_null($params->webex->sleep)) ? intval($params->webex->sleep ) : 3;
		for($i = 0; $i < $iterations; $i++)
		{
			$result = $curlWrapper->exec($url3);
			
			if(!preg_match("/window\.parent\.func_prepare\('([^']+)','([^']*)','([^']*)'\);/", $result, $matches))
			{
				KalturaLog::err("Invalid result returned for prepareTicket request - should contain call to the func_prepare method\n $result");
				return null;
			}
			$status = $matches[1];
			if($status == 'OKOK')
				break;
				
			sleep($sleep);
		}
		
		if($status != 'OKOK')
		{
			KalturaLog::info("Invalid result returned for prepareTicket request. Last reult:\n " . $result);
			return null;
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
			KalturaLog::err("getError: " . $curlWrapper->getError());
			return null;
		}
		
		$curlWrapper->close();
		$importData->fileSize = kFile::fileSize($importData->destFileLocalPath);
		
		return $importData;
	}

}