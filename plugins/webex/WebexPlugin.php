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
			return $importData;
		KalturaLog::debug('content-length [' . $curlInfo->headers['content-length'] . '] content-type [' . $curlInfo->headers['content-type'] . ']');
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
			throw new Exception('recordId value not found');
		}
		
		$data = file_get_contents($importData->destFileLocalPath);
		KalturaLog::info("data:\n\n$data\n\n");
		if(!preg_match("/href='([^']+)';/", $data, $matches))
		{
			throw new Exception('Starting URL not found');
		}
		$url2 = $matches[1];
		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_COOKIE, 'DetectionBrowserStatus=3|1|32|1|11|2;'.$curlInfo->headers["set-cookie"]);
		$result = $curlWrapper->exec($url2);
		KalturaLog::info("result:\n\n$result\n\n");
		
		if(!preg_match("/var prepareTicket = '([^']+)';/", $result, $matches))
		{
			throw new Exception('prepareTicket parameter not found');
		}
		$prepareTicket = $matches[1];
		
		if (!preg_match('/function (download\(\).+prepareTicket;)/s', $result, $matches))
		{
			throw new Exception('download function not found');
		}
		if (!preg_match('/http.+prepareTicket/', $matches[0], $matches))
		{
			throw new Exception('prepareTicket URL not found');
		}
		$url3 = $matches[0];
		$url3 = str_replace(array('"',' ','+', 'recordId', 'prepareTicket=prepareTicket'), array('','','',$recordId, "prepareTicket=$prepareTicket"), $url3);
		
		if (!preg_match("/var downloadUrl = '(http[^']+)' \\+ ticket;/", $result, $matches))
		{
			throw new Exception('Download URL not found');
		}
		$url4 = $matches[1];
		
		$status = null;
		$iterations = (isset($params->webex->iterations) && !is_null($params->webex->iterations)) ? intval($params->webex->iterations ) : 10;
		$sleep = (isset($params->webex->sleep) && !is_null($params->webex->sleep)) ? intval($params->webex->sleep ) : 3;
		for($i = 0; $i < $iterations; $i++)
		{
			$result = $curlWrapper->exec($url3);
			KalturaLog::info("result ($i):\n\n$result\n\n");
			
			if(!preg_match("/window\\.parent\\.func_prepare\\('([^']+)','([^']*)','([^']*)'\\);/", $result, $matches))
			{
				KalturaLog::err("Invalid result returned for prepareTicket request - should contain call to the func_prepare method\n $result");
				throw new Exception('Invalid result: func_prepare function not found');
			}
			$status = $matches[1];
			if($status == 'OKOK')
				break;
				
			sleep($sleep);
		}
		
		if($status != 'OKOK')
		{
			KalturaLog::info("Invalid result returned for prepareTicket request. Last result:\n " . $result);
			throw new kTemporaryException('Invalid result returned for prepareTicket request');
		}
			
		$ticket = $matches[3];
		
		$url4 .= $ticket;
		
		$curlWrapper->setOpt(CURLOPT_RETURNTRANSFER, false);
		$fileName = pathinfo($importData->destFileLocalPath, PATHINFO_FILENAME);
		$destFileLocalPath = preg_replace("/$fileName\.[\w\d]+/", "$fileName.arf", $importData->destFileLocalPath);
		$importData->destFileLocalPath = $destFileLocalPath;
		KalturaLog::info('destination: ' . $importData->destFileLocalPath);
		$result = $curlWrapper->exec($url4, $importData->destFileLocalPath);
		
		if (!$result)
		{	
			$code = $curlWrapper->getErrorNumber();
			$message = $curlWrapper->getError();
			throw new Exception($message, $code);
		}
		
		$curlWrapper->close();
		$importData->fileSize = kFile::fileSize($importData->destFileLocalPath);
		
		return $importData;
	}

}
