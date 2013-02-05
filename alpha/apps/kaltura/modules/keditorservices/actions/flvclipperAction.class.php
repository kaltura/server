<?php
/**
 * @package    Core
 * @subpackage kEditorServices
 */
class flvclipperAction extends kalturaAction
{
	static private function hmac($hashfunc, $key, $data)
    {
        $blocksize=64;

        if (strlen($key) > $blocksize)
        {
            $key = pack('H*', $hashfunc($key));
        }

        $key = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));

        return bin2hex($hmac);
    }

	public function execute()
	{
		requestUtils::handleConditionalGet();

		$entry_id 			= $this->getRequestParameter("entry_id");
		$ks_str 			= $this->getRequestParameter("ks");
		$base64_referrer 	= $this->getRequestParameter("referrer");
		$flavor_id			= $this->getRequestParameter('flavor');
		preg_match("/\/p\/(\d+)\//",$_SERVER['REQUEST_URI'],$pregPartner);
		$partner_id			= $pregPartner[1];
		
		$playManifestRedirectUrl = '';		
		//determine HTTPS
		$isHttps = false;
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$isHttps = true;	

		//assemble the URL
		$playManifestRedirectUrl .= ($isHttps ? 'https://'. kEnvironment::get('cdn_host_https') : 'http://'. kEnvironment::get('cdn_host'))
				.'/p/'.$partner_id
				.'/sp/'.$partner_id.'00'
				.'/playManifest'
				.'/entryId/'.$entry_id
				.'/flavorId/'.$flavor_id
				.'/format/'.'url'
				.'/protocol/'. ($isHttps ? 'https' : 'http') 
				.($ks_str ? '/ks'.$ks_str : "")
				.'/a/a.f4m'
				.($base64_referrer ? '?referrer='.$base64_referrer : "")
				;
		//stamp the header and die
		KalturaLog::debug("Redirecting flvClipper request to [$playManifestRedirectUrl]");
		header('Location:'.$playManifestRedirectUrl);
		die();
	}
	
	function checkForPreview(KSecureEntryHelper $securyEntryHelper, $clip_to)
	{
		$request = $_SERVER["REQUEST_URI"];
		$preview_length_msec = $securyEntryHelper->getPreviewLength() * 1000;
		if ((int)$clip_to !== (int)$preview_length_msec)
		{
			if (strpos($request, '/clip_to/') !== false) // when requesting invalid clip_to
			{
				if ($preview_length_msec === 0) // don't preview length 0, it will cause infinite loop because clip_to defaults to 2147483647
				{
					header("Content-Type: video/x-flv");
					KExternalErrors::dieGracefully();
				}
					
				$request = str_replace('/clip_to/'.$clip_to, '/clip_to/'.$preview_length_msec, $request);
				header("Location: $request");
			}
			else // redirect to same url with clip_to
			{
				if (strpos($request, "?") !== false)
				{
					$last_slash = strrpos($request, "/");
					$request = substr_replace($request, "/clip_to/$preview_length_msec", $last_slash, 0);
					header("Location: $request");
				}
				else
				{
					header("Location: $request/clip_to/$preview_length_msec");
				}
			}
			KExternalErrors::dieGracefully();
		}
	}
}
?>
