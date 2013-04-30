<?php
/**
 * @package plugins.webex
 */
class WebexPlugin extends KalturaPlugin implements IKalturaPermissions, IKalturaImportHandler
{
	const PLUGIN_NAME = 'webex';
	
	const WEBEX = 'webex';
	
	
	/* (non-PHPdoc)
	 * @see IKalturaPermissions::isAllowedPartner()
	 */
	public static function isAllowedPartner($partnerId) {
		$partner = PartnerPeer::retrieveByPK($partnerId);
		
		return $partner->getPluginEnabled(self::PLUGIN_NAME);
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaPlugin::getPluginName()
	 */
	public static function getPluginName() {
		return self::PLUGIN_NAME;
		
	}

	/* (non-PHPdoc)
	 * @see IKalturaImportHandler::handleImportData()
	 */
	public static function handleImportContent(array $curlInfo, $data, KalturaImportJobData $importData) {
		if ($curlInfo["download_content_length"] < 16000 && $curlInfo["content_type"] == 'text/html')
		{
			KalturaLog::info('Handle Import data: Webex Plugin');
			$matches = null;
			$cookies = array();
			if(preg_match_all('/\nSet-Cookie: ([^\r\n]+) path=.+/i', $data, $matches))
				$cookies = implode(' ', $matches[1]);
				
			$recordId = null;
			$cookiesArr = explode('; ', trim($cookies, ';'));
			foreach($cookiesArr as $cookie)
			{
				list($cookieName, $cookieValue) = explode('=', $cookie);
				if($cookieName == 'recordId')
					$recordId = $cookieValue;
			}
			
			if(!preg_match("/href='([^']+)';/", $data, $matches))
			{
				KalturaLog::info("Starting URL not found");
				return $data;
			}
			$url2 = $matches[1];
			$curlWrapper = new KCurlWrapper($url2);
			curl_setopt($curlWrapper->ch, CURLOPT_COOKIE, $cookies);
			$result = $curlWrapper->exec();
			
			if(!preg_match("/var prepareTicket = '([^']+)';/", $result, $matches))
			{
				KalturaLog::info("prepareTicket parameter not found");
				return $data;
			}
			$prepareTicket = $matches[1];
			
			if (!preg_match('/function (download\(\).+prepareTicket;)/s', $result, $matches))
			{
				KalturaLog::info("download function not found");
				return $data;
			}
			
			if (!preg_match('/http.+prepareTicket/', $matches[0], $matches))
			{
				KalturaLog::info("prepareTicket URL not found");
				return $data;
			}
			
			$url3 = $matches[0];
			$url3 = str_replace(array('"',' ','+', 'recordId', 'prepareTicket=prepareTicket'), array('','','',$recordId, "prepareTicket=$prepareTicket"), $url3);
			
			if (!preg_match('/function (func\_prepare\(.+\).+ticket;)/s', $result, $matches))
			{
				KalturaLog::info("func_prepare function not found");
				return $data;
			}
			
			if (!preg_match('/http.+ticket/', $matches[0], $matches))
			{
				KalturaLog::info("download URL not found");
				return $data;
			}
			
			$url4 = $matches[0];
			$url4 = str_replace(array('"',' ','+'), '', $url4);
			
			curl_setopt($curlWrapper->ch, CURLOPT_URL, $url3);
			
			$status = null;
			for($i = 0; $i < 10; $i++)
			{
				$result = $curlWrapper->exec();
				
				if(!preg_match("/window\.parent\.func_prepare\('([^']+)','([^']*)','([^']*)'\);/", $result, $matches))
				{
					KalturaLog::info("Invalid result returned for prepareTicket request - should contain call to the func_prepare method");
					return $data;
				}
				$status = $matches[1];
				if($status == 'OKOK')
					break;
					
				sleep(3);
			}
			
			if($status != 'OKOK')
			{
				KalturaLog::info("Invalid result returned for prepareTicket request");
				return $data;
			}
				
			$ticket = $matches[3];
			
			$url4 = str_replace("ticket=ticket", "ticket=$ticket", $url4);
			
			curl_setopt($curlWrapper->ch, CURLOPT_URL, $url4);
			curl_setopt($curlWrapper->ch, CURLOPT_RETURNTRANSFER, false);
			KalturaLog::info('destination: ' . $importData->destFileLocalPath);
			$result = $curlWrapper->exec($importData->destFileLocalPath);
			$curlWrapper->close();
			
			return $result;
		}
		
		return $data;
	}
}