<?php
class kAkamaiUrlManager extends kUrlManager
{

	/**
	 * Returns the URL path with the authorization token appended. See the
	 *   README for more details.
	 */
	function urlauth_gen_url($sUrl, $sParam, $nWindow,
	                         $sSalt, $sExtract, $nTime) {
	
		$sToken = $this->urlauth_gen_token($sUrl, $nWindow, $sSalt,
	                                    $sExtract, $nTime);
		if ($sToken == null) {
			return;
		}
	
		if (($sParam == "") || (!is_string($sParam))) {
			$sParam = "__gda__";
		}
	
	    if ((strlen($sParam) < 5) || (strlen($sParam) > 12)) {
	        return;
	    }
	
		if (($nWindow < 0) || (!is_integer($nWindow))) {
			return;
		}
	
		if (($nTime <= 0) || (!is_integer($nTime))) {
			$nTime = time();
		}
	
		$nExpires = $nWindow + $nTime;
	
		if (strpos($sUrl, "?") === false) {
			$res = $sUrl . "?" . $sParam . "=" . $nExpires . "_" . $sToken;
		} else {
			$res = $sUrl . "&" . $sParam . "=" . $nExpires . "_" . $sToken;
		}
	
		return $res;
	}
	
	/**
	 * Returns the hash portion of the token. This function should not be
	 *   called directly.
	 */
	function urlauth_gen_token($sUrl, $nWindow, $sSalt,
	                           $sExtract, $nTime) {
		if (($sUrl == "") || (!is_string($sUrl))) {
			return;
		}
	
		if (($nWindow < 0) || (!is_integer($nWindow))) {
			return;
		}
	
		if (($sSalt == "") || (!is_string($sSalt))) {
			return;
		}
	
		if (!is_string($sExtract)) {
			$sExtract = "";
		}
	
		if (($nTime <= 0) || (!is_integer($nTime))) {
			$nTime = time();
		}
	
		$nExpires = $nWindow + $nTime;
		$sExpByte1 = chr($nExpires & 0xff);
		$sExpByte2 = chr(($nExpires >> 8) & 0xff);
		$sExpByte3 = chr(($nExpires >> 16) & 0xff);
		$sExpByte4 = chr(($nExpires >> 24) & 0xff);
	
		$sData = $sExpByte1 . $sExpByte2 . $sExpByte3 . $sExpByte4
	                 . $sUrl . $sExtract . $sSalt;
	
		$sHash = $this->_unHex(md5($sData));
	
		$sToken = md5($sSalt . $sHash);
		return $sToken;
	}
	
	/**
	 * Helper function used to translate hex data to binary
	 */
	function _unHex($str) {
		$res = "";
		for ($i = 0; $i < strlen($str); $i += 2) {
	        $res .= chr(hexdec(substr($str, $i, 2)));
		}
		return $res;
	}

	/**
	 * @param string baseUrl
	 * @param array $flavorUrls
	 */
	public function finalizeUrls(&$baseUrl, &$flavorsUrls)
	{
		if (!count($flavorsUrls))
			return;

		if ($this->protocol == StorageProfile::PLAY_FORMAT_RTMP && @$this->params['rtmp_auth_salt'])
		{
			$profile = $this->params['rtmp_auth_profile'];
			$type = $this->params['rtmp_auth_type'];
			$salt = $this->params['rtmp_auth_salt'];
			$window = $this->params['rtmp_auth_seconds'];
			$aifp = $this->params['rtmp_auth_aifp'];
			$usePrefix = @$this->params['rtmp_auth_slist_find_prefix'];

			if ($usePrefix)
			{
				$urls = array();
				$minLen = 1024;

				foreach($flavorsUrls as $flavor)
				{
					$url = $flavor["url"];
					if (substr($url, 0, 4) == "mp4:")
						$url = substr($url, 4);
					$urls[] = $url;

					$minLen = min($minLen, strlen($url));
				}

				$url = array_pop($urls);

				$scan = true;
				for($i = 0; $i < $minLen && $scan; $i++)
				{
					$c = substr($url, $i, 1);
					foreach($urls as $url)
					{
						if ($c != substr($url, $i, 1))
						{
							$scan = false;
							break;
						}
					}
				}
	
				$prefix = substr($url, 0, $i - 1);
			}
			else
			{
				$prefix = "";
				foreach($flavorsUrls as $flavor)
				{
					$url = $flavor["url"];
					if (substr($url, 0, 4) == "mp4:")
						$url = substr($url, 4);
					$prefix = $prefix . $url . ";";
				}
			}

			$factory = new StreamTokenFactory;
			$token = $factory->getToken($type, $prefix, null, $profile, $salt, null, $window, null, null, null);
			$auth = "?auth=".$token->getToken()."&aifp=$aifp&slist=$prefix";
			$baseUrl .= $auth;
			foreach($flavorsUrls as &$flavor)
			{
        			$url = $flavor["url"];
        			$flavor["url"] = $url.$auth;
			}
		}
	}

	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	public function getFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$partnerId = $flavorAsset->getPartnerId();
		$subpId = $flavorAsset->getentry()->getSubpId();
		$flavorAssetId = $flavorAsset->getId();
		$flavorAssetVersion = $flavorAsset->getVersion();

		$partnerPath = myPartnerUtils::getUrlForPartner($partnerId, $subpId);
		
		$this->setFileExtension($flavorAsset->getFileExt());
		$this->setContainerFormat($flavorAsset->getContainerFormat());	

		$versionString = (!$flavorAssetVersion || $flavorAssetVersion == 1 ? '' : "/v/$flavorAssetVersion");
		$url = "$partnerPath/serveFlavor{$versionString}/flavorId/$flavorAssetId";
		if($this->protocol==StorageProfile::PLAY_FORMAT_RTSP) {
			$url = Akamaizer::generateARL($this->params["rtsp_host"].$url."/a.mov", $this->params["rtsp_cpcode"], 0, 0, true);
			return $url;
		}
	
		if($this->protocol==StorageProfile::PLAY_FORMAT_APPLE_HTTP) {
			if (strpos($flavorAsset->getTags(), flavorParams::TAG_APPLEMBR) === FALSE)
			{
				// we use index_0_av.m3u8 instead of master.m3u8 as temporary solution to overcome
				// an extra "redirection" done on the part of akamai.
				// the auto created master.m3u8 file contains a single item playlist to the index_0_av.m3u8 file
				// this extra "redirection" fails
				$url = "http://".@$this->params['hd_ios']."/i".$url."/index_0_av.m3u8";				
			}
			else
				$url .= "/file/playlist.m3u8";
		}
		else {
			if($this->clipTo)
				$url .= "/clipTo/$this->clipTo";

			if($this->protocol == "hdnetworksmil")
			{
				$url = "http://".$this->params["hd_flash"].$url.'/forceproxy/true';
			}
			else if($this->protocol == StorageProfile::PLAY_FORMAT_RTMP)
			{
				$url .= '/forceproxy/true';
				$url = trim($url, "/");
				if($this->extention && strtolower($this->extention) != 'flv' ||
					$this->containerFormat && strtolower($this->containerFormat) != 'flash video')
					$url = "mp4:$url";
			}
			else
			{		
				if($this->extention)
					$url .= "/name/$flavorAssetId.$this->extention";
						
				if($this->seekFromTime > 0)
					$url .= '?aktimeoffset=' . floor($this->seekFromTime / 1000);
			}
		}
		
		$url = str_replace('\\', '/', $url);

		if ($this->protocol == StorageProfile::PLAY_FORMAT_HTTP && @$this->params['http_auth_salt'])
		{
			$window = $this->params['http_auth_seconds'];
			$param = $this->params['http_auth_param'];
			$salt = $this->params['http_auth_salt'];
			$root_dir = $this->params['http_auth_root_dir'];
			$url = $this->urlauth_gen_url($root_dir.$url, $param, $window, $salt, null, null);
		}

		return $url;
	}
	
	/**
	 * @return boolean
	 */
	public function authenticateRequest($url)
	{
		return $this->authenticateRequestUrl($_SERVER["SCRIPT_URL"]);
	}
	
	/**
	 * @return boolean
	 */
	public function authenticateRequestUrl($url)
	{
		$authHeaderData = $this->params['auth_header_data'];
		$authHeaderSign = $this->params['auth_header_sign'];
		$authHeaderTimeout = $this->params['auth_header_timeout'];
		$authHeaderSalt = $this->params['auth_header_salt'];
		
		$authData = @$_SERVER[$authHeaderData];
		$authSign = @$_SERVER[$authHeaderSign];
		$window = $this->params['smooth_auth_seconds'];
		$param = $this->params['smooth_auth_param'];
		$salt = $this->params['smooth_auth_salt'];
		
		list($version, $ghostIp, $clientIp, $time, $uniqueId, $nonce) = explode(",", $authData);
		if ($authHeaderTimeout) {
		    // Compare the absolute value of the difference between the current time
		    // and the "token" time.
			if (abs(time() - $time) > $authHeaderTimeout ) {
				return false;
			}
		}
		
		$newSign = base64_encode(md5($authHeaderSalt . $authData . $url, true));		
        if ($newSign == $authSign) {
            return true;
        }

        return false;
	}

	/**
	 * @param FileSync $fileSync
	 * @return string
	 */
	public function getFileSyncUrl(FileSync $fileSync)
	{
		$fileSync = kFileSyncUtils::resolve($fileSync);
		
		$storage = StorageProfilePeer::retrieveByPK($fileSync->getDc());
		if(!$storage)
			return parent::getFileSyncUrl($fileSync);
		
		$serverUrl = $storage->getDeliveryIisBaseUrl();
		$partnerPath = myPartnerUtils::getUrlForPartner($fileSync->getPartnerId(), $fileSync->getPartnerId() * 100);
		
		if ($this->protocol == StorageProfile::PLAY_FORMAT_APPLE_HTTP)
			return $partnerPath.$fileSync->getFilePath()."/playlist.m3u8";
		
		if($fileSync->getObjectSubType() != entry::FILE_SYNC_ENTRY_SUB_TYPE_ISM)
			return parent::getFileSyncUrl($fileSync);
		
		$path = $partnerPath.'/serveIsm/objectId/' . $fileSync->getObjectId() . '_' . $fileSync->getObjectSubType() . '_' . $fileSync->getVersion() . '.' . pathinfo(kFileSyncUtils::resolve($fileSync)->getFilePath(), PATHINFO_EXTENSION) . '/manifest';
//		$path = $partnerPath.'/serveIsm/objectId/'.pathinfo(kFileSyncUtils::resolve($fileSync)->getFilePath(), PATHINFO_BASENAME).'/manifest';		
		$matches = null;
		if(preg_match('/(https?:\/\/[^\/]+)(.*)/', $serverUrl, $matches))
		{
			$serverUrl = $matches[1];
			$path = $matches[2] . $path;
		}
		
		$path = str_replace('//', '/', $path);

		$window = $this->params['smooth_auth_seconds'];
		$param = $this->params['smooth_auth_param'];
		$salt = $this->params['smooth_auth_salt'];
		$authPath = $this->urlauth_gen_url($path, $param, $window, $salt, null, null);
		
		return $serverUrl . '/' . $authPath;
	}

	/**
	 * check whether this url manager sent the current request.
	 * if so, return a string describing the usage. e.g. cdn.acme.com+token for
	 * using cdn.acme.com with secure token delivery. This string can be matched to the
	 * partner settings in order to enforce a specific delivery method. 
	 * @return string
	 */
	public function identifyRequest()
	{
		$delivery = @$_SERVER['HTTP_X_FORWARDED_HOST'];
		if (!$delivery)
			$delivery = @$_SERVER['HTTP_HOST'];
		if ($delivery != @$this->params["http_header_host"])
			return false;
		
		$uri = $_SERVER["REQUEST_URI"];
		if (strpos($uri, "/s/") === 0)
			$delivery .= "+token";
			
		return $delivery;
	}

}

/*
 ''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
 ' Akamai On-Demand ARL Generation
 '
 ' PURPOSE/NOTES:
 ' --------------
 '
 ' A URL to ARL function call for streaming on-demand files.
 '
 ' USAGE:
 ' ------
 '
 ' Akamai code can be placed in an ASP page or in the global.asa page.
 ' Invocation is:
 '
 '	GenerateARL(<url>, <cpcode>, <objectdata>, <ad>, <munge>)
 '
 ' Where the url is the original url to the .asf or .wma media file,
 ' the cpcode is the value given to the customer by Akamai,
 ' deterministic is whether you want the same input to always
 ' generate the same ARL output (0 for false or 1 for true), and
 ' objectdata is the type of fingerprint for the object data field.
 ' Values for objectdata are:
 '     1 - Creation Time - Current Timestamp
 '     1 - File Last modified time(note, file must exist on local web server)
 '     2 - Version number(see OBJECTDATA_VERSION define below).
 '
 ' <ad> Invokes Real Ad Plug-in on Real Server. This should only be used on Real SMIL files
 ' that are invoking an ad server. (0 for off or 1 for on)
 '
 ' <munge>  Encode original URL. Obscures the original URL, but is not cryptographically secure.
 ' (0 for off and 1 for on)
 '
 ' The url must be fully formed and not relative. For example:
 '
 '	http://www.foo.com/movie.asf
 '
 ' is fine, but:
 '
 '	../movie.asf                                or
 '       /movie.asf
 '
 ' is not OK. This function does not attempt to validate the URL other than
 ' ensuring the file extension is .asf or .wma, however (aliases are not
 ' currently supported). So if the URL you input is not something that could
 ' be resolved by a browser, the generated ARL will also be invalid.
 '
 ' An example invocation would be:
 '
 '	ARL = GenerateARL("http://www.foo.com/movie.asf", 801, 0, 0, 0)
 '
 ' After which ARL would contain a value like:
 '
 '	mms://a712.v8010.c801.g.vm.akamaistream.net/7/712/801/7d029a36/www.foo.com/movie.asf
 '
 ' After a movie/sound file is changed its ARL must be regenerated. The timestamp
 ' (in this example 7d029a36) indicates to the Akamai cache whether it needs to
 ' fetch a new copy of the movie. Ideally the object data should never change unless
 ' the source movie or sound file has changed. Object data must be alphanumeric
 ' ranging from 3 to 14 characters.
 '
 ' ERROR CODES:
 ' ------------
 ' The following error codes may be returned instead of an ARL if the input
 ' is incorrect:
 '
 ' INVALID_URL            = -1
 ' INVALID_CPCODE         = -2
 ' INVALID_FILE_NAME      = -3
 '
 ' If you do not wish to have error codes returned, you can invoke:
 '
 '	GenerateSafeARL(<url>, <cpcode>, <objectdata>, <ad>, <munge>)
 '
 ' This function performs exactly as GenerateARL with the exception that
 ' any INVALID argument will cause the output to be the original URL.
 ' This function is suggested for runtime, customer-focused environments.
 '
 ''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''''
 */

class Akamaizer
{
	// For deterministic = 1 (true) only, use a static object data version number:
	const OBJECTDATA_VERSION            = "v001";
	const IS_OBJECTDATA_CREATIONTIME    = 0;
	const IS_OBJECTDATA_LASTMODTIME     = 1;
	const IS_OBJECTDATA_VERSION         = 2;

	// Error Codes
	const INVALID_URL       = -1;
	const INVALID_CPCODE    = -2;
	const INVALID_FILE_NAME = -3;


	static public function generateSafeARL($url, $cpcode, $objectdata, $ad, $bMunge)
	{
		$origUrl = $url;

		$url = trim($url); //removing leading and trailing whitespace

		$temp = self::generateArl($url, $cpcode, $objectdata, $ad, $bMunge);
		if ($temp == self::INVALID_URL || $temp == self::INVALID_CPCODE || $temp == self::INVALID_FILE_NAME)
		return $origUrl;
		else
		return $temp;
	}

	static public function generateARL($url, $cpcode, $objectdata, $ad, $bMunge)
	{
		//remove protocol from url
		$url = preg_replace("/http:\/\/|mms:\/\/|rtsp:\/\//i", "", $url);
		 
		if ($bMunge == 1)
		$typecode = 5;
		else
		$typecode = 7;

		if (self::isQT($url))
		{
			$protocol = "rtsp";
			$domain = ".g.vq.akamaistream.net";
		}
		elseif (self::isWMT($url))
		{
			$protocol = "mms";
			$domain = ".g.vm.akamaistream.net";
		}
		elseif (self::isReal($url))
		{
			$protocol = "rtsp";
			$domain = ".g.vr.akamaistream.net";

			if ($ad == 1 && self::isSMIL($url))
			$typecode = "adtag/general/ondemand/$typecode";
			else
			$typecode = "ondemand/$typecode";
		}
		elseif (self::isGraphic($url))
		{
			$protocol = "http";
			$domain = ".g.akamai.net";
		}
		else
		{
			// URL invalid
			return self::INVALID_URL;
		}

		if (!is_numeric($cpcode))
		{
			// cpcode invalid (non numberic)
			return self::INVALID_CPCODE;
		}

		if ($cpcode > 999999 || $cpcode < 1)
		{
			// cpcode invalid
			return self::INVALID_CPCODE;
		}

		$customerid = "c$cpcode";
		$streamtype = "v"; // VOD

		$deterministic = 1;
		$serialno = self::generateSerial($url, $deterministic);

		$objectdata = self::generateObjectData($url, intval($objectdata));
		if ($objectdata == false)
		return self::INVALID_FILE_NAME;

		$serialno = self::generateSerial($url, $deterministic);
		$streamidhash = self::generateStreamIDHash($url, $deterministic);

		$cpcode4 = $cpcode;
		while (strlen($cpcode4) < 4)
		$cpcode4 = "0$cpcode4";

		$streamid = $cpcode4.$streamidhash;

		if ($bMunge == 1)
		{
			$fileName = substr($url, strrpos($url, "/") - strlen($url) + 1);
			$path = substr($url, 0, strrpos($url, "/"));
				
			$url = self::munge($cpcode, $path)."/".$fileName;
		}

		return $protocol . "://a" . $serialno . "." . $streamtype . $streamid . "." . $customerid .
		$domain . "/" . $typecode . "/" .
		$serialno . "/" . $cpcode .
			 "/" . $objectdata . "/" . $url;
	}

	static public function isQT($input_url)
	{
		return preg_match("/\.mov\b|\.sdp\b/", $input_url) != 0;
	}

	static public function isWMT($input_url)
	{
		return preg_match("/\.asf\b|\.wma\b|\.wax\b|\.wmv\b/", $input_url) != 0;
	}

	static public function isReal($input_url)
	{
		return preg_match("/\.rm\b|\.ra\b|\.rt\b|\.rp\b|\.smil\b|\.smi\b|\.swf\b/", $input_url) != 0;
	}

	static public function isSMIL($input_url)
	{
		return preg_match("\.smil\b|\.smi\b", $input_url) != 0;
	}

	static function generateSerial($url, $deterministic)
	{
		// For load balancing purposes, the serial number should be spread as randomly
		// as possible.

		srand();
		return rand(1, 2000);
		/*
		 if ($deterministic == 0)
		 {
		 // randomize based on current time:
		 //
		 $seed	= microtime(true);
		 }
		 else
		 {
		 // randomize deterministically:
		 //
		 $seed	= self::createHash($url);
		 }

		 // need to loop here in case bit shifting generates a zero, but we also
		 // need to force the number generator to initialize to a specific sequence
		 // (based on the seed) and continue to use that sequence for each new
		 // random number.  That's done by calling Rnd with a -seed (-1 * seed) value
		 // the _first_ time, and with (+1 * seed) for each successive call.
		 //
		 $serialno = 0;
		 $sign = -1;
		 while ($serialno == 0)
		 {
		 //generate random serial number between 1 and 2000
		 //
		 //$serialno = floor(2000 * rand(0, abs($sign * $seed)) + 1);
		 $serialno = rand(1, 2000);
		 $sign = 1;
		 }

		 return $serialno;
		 */
	}


	static public function generateStreamIDHash($url, $deterministic)
	{
		// For load balancing purposes, the hash should be spread as randomly
		// as possible.

		if ($deterministic)
		{
			// randomize deterministically:
			$randomize = self::createHash($url);
		}
		else
		{
			// randomize based on current time:
			$randomize (hour(now) & minute(now) & second(now));
		}

		$streamidhash = 0;

		srand();
		// generate random serial number between 0 and 15
		$streamidhash = rand(0, 15);

		//echo "DEBUG: Raw streamidhash is $streamidhash\n";

		$streamidhash = dechex($streamidhash);

		return $streamidhash;
	}

	static public function generateObjectData($aURL, $objectdata)
	{
		// When the object data changes it indicates to the Akamai server
		// that it must refresh the object (movie/sound file). Ideally this number
		// should never change except when a movie/sound file itself changes.
		// It must be alphanumeric ranging from 3 to 14 characters.

		if ($objectdata == self::IS_OBJECTDATA_LASTMODTIME)
		{
			return 0;
			//return getLastModifiedTime(URLtoFile($aURL))
		}
		elseif ($objectdata == self::IS_OBJECTDATA_CREATIONTIME)
		{
			return self::getCurrentTime();
		}
		else
		{
			// Here we are using a simple version number which would need to be incremented
			// everytime a movie/sound file changes. You may want to implement something more
			// intelligent like the modified timestamp of the object in question.

			return self::OBJECTDATA_VERSION;
		}
	}

	static public function createHash($url)
	{
		// Here's a primitive hash of a URL to a numeric.
		// We just add the ASCII values of all the individual characters of the
		// filename.

		$seed = 0;
		$pos = 1;
		while ($pos <= strlen($url))
		{
			// echo "DEBUG" . substr($url, $pos, 1) . " becomes " & ord(substr($url, $pos, 1)) ."\n";
			$seed = $seed + ord(substr($url, $pos, 1));
			$pos++;
		}

		// echo "DEBUG: seed is $seed\n";
		return $seed;
	}


	static public function getCurrentTime()
	{
		$holdnow = getDate();

		$yyyy = $holdnow["year"];

		$mm = $holdnow["mon"];
		if (strlen($mm) == 1)
		$mm = "0$mm";

		$dd = $holdnow["mday"];
		if (strlen($dd) == 1)
		$dd = "0$dd";

		$hh = $holdnow["hours"];
		if (strlen($hh) == 1)
		$hh = "0$hh";

		$mmin = $holdnow["minutes"];
		if (strlen($mmin) == 1)
		$mmin = "0$mmin";

		// generate object data based on the time:
		$objectdata = dechex($yyyy) . dechex($mm) . dechex($dd) . dechex($hh) . dechex($mmin);

		return $objectdata;
	}

	/*
	 public function URLtoFile($aURL)
	 {
		//Get path and file name from URL
		$pos = strchr(strchr(aURL, "//") + 2, aURL, "/")
			
		//Map web path and file to local directory and file
		return Server.MapPath(Mid(aURL, pos))
		}
		*/

	static function munge($cpCode, $stringToMunge)
	{
		// Encode original URL. Obscures the original URL,
		// but is not cryptographically secure.

		$hash = $cpCode + 0;
		$result = "1a1a1a";
		$iLen = strlen($stringToMunge);

		$pos = 1;
		while ($pos <= $iLen)
		{
			$character = substr($stringToMunge, $pos, 1);
			$hash = ($hash + ord($character)) % 256;
			 
			//echo hash . "= ";

			$tmp = dechex($hash);
			if (strlen($tmp) == 1)
			$tmp = "0$tmp";

			//echo hash . "<BR>";

			$result .= $tmp;
			$pos++;
		}

		return $result;
	}

	static function generateLiveWMARL($cpcode, $port)
	{
		// Note, this function only generates WindowsMedia ARLs
		//

		$protocol 			=	"mms";
		$domaininfo 			=	".l" . $cpcode . $port . ".c" . $cpcode;
		$domain				=	".g.lm.akamaistream.net";
		$typecode			=	"/D/";
		$serialno 			=	self::generateSerial($port, 1);

		return $protocol . "://a" . $serialno . $domaininfo . $domain . $typecode .
		$serialno . "/" . $cpcode	. "/" . OBJECTDATA_VERSION . "/reflector:" . $port;
	}

	static function generateLiveRMARL($cpcode, $port)
	{
		// Note, this function only generates RealMedia ARLs (for live RealAudio or RealText streams)
		//

		$protocol 			=	"rtsp";
		$domaininfo 			=	".l" & cpcode & port & ".c" & cpcode;
		$domain				=	".g.lr.akamaistream.net";
		$typecode			=	"/live/D/";
		$serialno 			=	self::generateSerial($port, 1);

		return $protocol . "://a" . $serialno . $domaininfo . $domain . $typecode .
		$serialno . "/" . $cpcode	. "/" . self::OBJECTDATA_VERSION . "/reflector:" . $port;
	}
		
	//==============================================================================
	// Now, if you want to handle serving up on-demand graphic content (like .gif
	// and .jpg) the Akamai way, first add the following function to mod.asp:
	//
	static function isGraphic($input_url)
	{
		return preg_match("/\.gif\b|\.jpg\b|\.jpeg\b/", $input_url) != 0;
	}
}

