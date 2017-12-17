<?php
/**
 * @package plugins.webex
 */
class WebexPlugin extends KalturaPlugin implements IKalturaImportHandler
{
	const PLUGIN_NAME = 'webex';
	
	const WEBEX_FLAVOR_PARAM_SYS_NAME = 'webex_flavor_params';

	private static $container_formats_to_file_extensions = array("arf"=>"arf", "mpeg-4" => "mp4");

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
		if ( !$curlInfo->headers['content-length'] 
		    || $curlInfo->headers['content-length'] >= 32000 
		    || !$curlInfo->headers['content-type'] 
		    || $curlInfo->headers['content-type'] != 'text/html')
				return $importData;
		
		KalturaLog::debug('content-length [' . $curlInfo->headers['content-length'] . '] content-type [' . $curlInfo->headers['content-type'] . ']');
		KalturaLog::info('Handle Import data: Webex Plugin');

		$downloadUrl = self::retrieveWebexDownloadFilePath($curlInfo, $importData->destFileLocalPath, $params);
		$fileSize = self::getFileSizeFromHeader($downloadUrl);

		$curlWrapper = new KCurlWrapper();
		$curlWrapper->setOpt(CURLOPT_COOKIE, 'DetectionBrowserStatus=3|1|32|1|11|2;'.$curlInfo->headers["set-cookie"]);
		$curlWrapper->setOpt(CURLOPT_RETURNTRANSFER, false);
		$fileName = pathinfo($importData->destFileLocalPath, PATHINFO_FILENAME);
		
		KalturaLog::info('destination: ' . $importData->destFileLocalPath);
		$tmpPath = tempnam(sys_get_temp_dir(), "webex");
		$result = $curlWrapper->exec($downloadUrl, $tmpPath);
		
		if (!$result)
		{	
			$code = $curlWrapper->getErrorNumber();
			$message = $curlWrapper->getError();
			throw new Exception($message, $code);
		}

		$mediaInfoBin = isset($params->mediaInfoCmd)? $params->mediaInfoCmd: "mediainfo";
		$mediaInfoParser = new KMediaInfoMediaParser($tmpPath, $mediaInfoBin);
		$mediaInfo = $mediaInfoParser->getMediaInfo();
		if (isset(self::$container_formats_to_file_extensions[$mediaInfo->containerFormat]) )
		{
			$fileExtension = self::$container_formats_to_file_extensions[$mediaInfo->containerFormat];
			$destFileLocalPath = preg_replace("/$fileName\.[\w\d]+/", $fileName.".".$fileExtension, $importData->destFileLocalPath);
		}
		else
		{
			$destFileLocalPath = preg_replace("/$fileName\.[\w\d]+/", "$fileName.arf", $importData->destFileLocalPath);
		}
		$importData->destFileLocalPath = $destFileLocalPath;
		rename($tmpPath, $importData->destFileLocalPath);
		
		$curlWrapper->close();

		$actualFileSize = kFile::fileSize($importData->destFileLocalPath);
		if($actualFileSize < $fileSize )
		{
			$percent = floor($actualFileSize * 100 / $fileSize);
			throw new kTemporaryException("Downloaded size: $actualFileSize($percent%)");
		}

		$importData->fileSize = $actualFileSize;
		if (!$importData->fileSize)
			throw new kTemporaryException("File size download from WEBEX was 0");
		
		return $importData;
	}



	public static function retrieveWebexDownloadFilePath($curlInfo, $destFileLocalPath, $params)
	{
		$matches = null;
		$recordId = null;
		if(isset($curlInfo->headers['set-cookie']))
		{
			$recordId = $curlInfo->getCookieValue($curlInfo->headers['set-cookie'], 'recordId');
			if ($recordId==null)
			{
				throw new Exception('recordId value not found');
			}
		}
		else
		{
			throw new Exception('set-cookie was not found in header');
		}

		$data = file_get_contents($destFileLocalPath);
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

		//direct url for downloading webex file
		return $url4;
	}

	public static function getFileSizeFromHeader($downloadUrl)
	{
		$curlHeaderWrapper = new KCurlWrapper();
		$curlHeaderResponse = $curlHeaderWrapper->getHeader($downloadUrl, true);

		if(!$curlHeaderResponse || $curlHeaderWrapper->getError())
			throw new kTemporaryException("Couldn't retrieve webex file headers, curl returned with the following error: [".$curlHeaderWrapper->getError()."]" );

		$fileSize = null;
		if(isset($curlHeaderResponse->headers['content-length']))
			$fileSize = $curlHeaderResponse->headers['content-length'];
		$curlHeaderWrapper->close();

		if(!$fileSize)
			throw new kTemporaryException("File size is missing from header");

		KalturaLog::info("Webex file headers: " . print_r($curlHeaderResponse,true));
		return $fileSize;
	}


}
