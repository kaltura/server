<?php
/**
 * Akamai On-Demand ARL Generation
 *
 * PURPOSE/NOTES:
 * --------------
 *
 * A URL to ARL function call for streaming on-demand files.
 *
 * USAGE:
 * ------
 *
 * Akamai code can be placed in an ASP page or in the global.asa page.
 * Invocation is:
 *
 *	GenerateARL(<url>, <cpcode>, <objectdata>, <ad>, <munge>)
 *
 * Where the url is the original url to the .asf or .wma media file,
 * the cpcode is the value given to the customer by Akamai,
 * deterministic is whether you want the same input to always
 * generate the same ARL output (0 for false or 1 for true), and
 * objectdata is the type of fingerprint for the object data field.
 * Values for objectdata are:
 *     1 - Creation Time - Current Timestamp
 *     1 - File Last modified time(note, file must exist on local web server)
 *     2 - Version number(see OBJECTDATA_VERSION define below).
 *
 * <ad> Invokes Real Ad Plug-in on Real Server. This should only be used on Real SMIL files
 * that are invoking an ad server. (0 for off or 1 for on)
 *
 * <munge>  Encode original URL. Obscures the original URL, but is not cryptographically secure.
 * (0 for off and 1 for on)
 *
 * The url must be fully formed and not relative. For example:
 *
 *	http://www.foo.com/movie.asf
 *
 * is fine, but:
 *
 *	../movie.asf                                or
 *       /movie.asf
 *
 * is not OK. This function does not attempt to validate the URL other than
 * ensuring the file extension is .asf or .wma, however (aliases are not
 * currently supported). So if the URL you input is not something that could
 * be resolved by a browser, the generated ARL will also be invalid.
 *
 * An example invocation would be:
 *
 *	ARL = GenerateARL("http://www.foo.com/movie.asf", 801, 0, 0, 0)
 *
 * After which ARL would contain a value like:
 *
 *	mms://a712.v8010.c801.g.vm.akamaistream.net/7/712/801/7d029a36/www.foo.com/movie.asf
 *
 * After a movie/sound file is changed its ARL must be regenerated. The timestamp
 * (in this example 7d029a36) indicates to the Akamai cache whether it needs to
 * fetch a new copy of the movie. Ideally the object data should never change unless
 * the source movie or sound file has changed. Object data must be alphanumeric
 * ranging from 3 to 14 characters.
 *
 * ERROR CODES:
 * ------------
 * The following error codes may be returned instead of an ARL if the input
 * is incorrect:
 *
 * INVALID_URL            = -1
 * INVALID_CPCODE         = -2
 * INVALID_FILE_NAME      = -3
 *
 * If you do not wish to have error codes returned, you can invoke:
 *
 *	GenerateSafeARL(<url>, <cpcode>, <objectdata>, <ad>, <munge>)
 *
 * This function performs exactly as GenerateARL with the exception that
 * any INVALID argument will cause the output to be the original URL.
 * This function is suggested for runtime, customer-focused environments.
 *
 * @package Core
 * @subpackage storage.Akamai
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

		$pos = 0;
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

class kAkamaiRtspUrlTokenizer extends kUrlTokenizer
{
	/**
	 * @var string
	 */
	protected $host;
	
	/**
	 * @var int
	 */
	protected $cpcode;
	
	/**
	 * @param string $url
	 * @return string
	 */
	public function tokenizeSingleUrl($url)
	{
		return Akamaizer::generateARL($this->host . $url . "/a.mov", $this->cpcode, 0, 0, true);
	}
	
	/**
	 * @return the $host
	 */
	public function getHost() {
		return $this->host;
	}

	/**
	 * @return the $cpcode
	 */
	public function getCpcode() {
		return $this->cpcode;
	}

	/**
	 * @param string $host
	 */
	public function setHost($host) {
		$this->host = $host;
	}

	/**
	 * @param number $cpcode
	 */
	public function setCpcode($cpcode) {
		$this->cpcode = $cpcode;
	}

	
	
}

