<?php

abstract class kManifestRenderer
{
	const PLAY_STREAM_TYPE_LIVE = 'live';
	const PLAY_STREAM_TYPE_RECORDED = 'recorded';
	const PLAY_STREAM_TYPE_DVR = 'dvr';
	const PLAY_STREAM_TYPE_ANY = 'any';

	/**
	 * @var string
	 */
	public $entryId;

	/**
	 * @var int
	 */
	public $duration = null;
	
	/**
	 * @var kUrlTokenizer
	 */
	public $tokenizer = null;
	
	/**
	 * @var int
	 */
	public $cachingHeadersAge = 0;
	
	/**
	 * @var bool
	 */
	public $forceCachingHeaders = false;
	
	/**
	 * @var int
	 */
	public $lastModified = null;
	
	/**
	 * @var string
	 */
	public $deliveryCode = '';
	
	/**
	 * @var string
	 */
	public $defaultDeliveryCode = '';
	
	/**
	 * @var kSessionBase
	 */
	protected $ksObject = null;
	
	/**
	 * Array of classes required for load into the renderer scope in order to expand the manifest
	 * @var array
	 */
	public $contributors;
	
	protected function prepareFlavors()
	{
	}
	
	/**
	 * @return array<string>
	 */
	protected function getHeaders()
	{
		return array();
	}
	
	/**
	 * @return string
	 */
	protected function getManifestHeader ()
	{
		return '';
	}
	
	/**
	 * @return string
	 */
	protected function getManifestFooter()
	{
		return '';
	}
	
	/**
	 * @return array
	 */
	protected function getManifestFlavors()
	{
		return array();
	}
	
	protected function getSeparator ()
	{
		return "\n";
	}

	// allow to replace {deliveryCode} place holder with the deliveryCode parameter passed to the action
	// a publisher with a rtmpUrl set to {deliveryCode}.example.com/ondemand will be able to use different
	// cdn configuration for different sub publishers by passing a different deliveryCode to the KDP
	abstract protected function replaceDeliveryCode();
	
	abstract protected function tokenizeUrls();
	
	abstract protected function applyDomainPrefix();

	abstract protected function replacePlayServerSessionId();

	/**
	 * @param kSessionBase $ksObject
	 */
	public function setKsObject($ksObject)
	{
		$this->ksObject = $ksObject;
		if ($this->tokenizer)
		{
			$this->tokenizer->setKsObject($ksObject);
		}
	}
	
	/**
	 * @param string $playbackContext
	 */
	public function setPlaybackContext($playbackContext)
	{
		if ($this->tokenizer)
		{
			$this->tokenizer->setPlaybackContext($playbackContext);
		}
	}
	
	/**
	 * @param string $deliveryCode
	 */
	public function setDeliveryCode($deliveryCode)
	{
		$this->deliveryCode = $deliveryCode ? $deliveryCode : $this->defaultDeliveryCode;
	}

	protected function sendAnalyticsBeacon($host, $port)
	{
		// build the uri
		$output = array(
			'eventType' => '100',
			'service' => 'analytics',
			'action' => 'trackEvent',
			'entryId' => $this->entryId,
			'partnerId' => $this->partnerId,
			'playbackType' => $this->entryType,
		);

		$params = infraRequestUtils::getRequestParams();
		$mapping = array(
			'ks' => 'ks',
			'format' => 'deliveryType',
			'uiConfId' => 'uiConfId',
			'playSessionId' => 'sessionId',
			'clientTag' => 'clientTag', 
		);
		foreach ($mapping as $src => $dest)
		{
			if (!isset($params[$src]))
			{
				continue;
			}

			$output[$dest] = $params[$src];
		}

		if (isset($params['clientTag']) && strpos($params['clientTag'], 'html5:v') === 0)
		{
			$output['clientVer'] = substr($params['clientTag'], 7);
		}

		if (isset($params['referrer']))
		{
			$base64Referrer = $params['referrer'];
			$referrer = base64_decode(str_replace(array('-', '_', ' '), array('+', '/', '+'), $base64Referrer));
			if ($referrer)
			{
				$output['referrer'] = $referrer;
			}
		}

		$uri = '/api_v3/index.php?' . http_build_query($output, '', '&');

		// build the request
		$headers = array(
			'Host' => $host,
			'X-Forwarded-For' => infraRequestUtils::getRemoteAddress(),
		);
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$headers['User-Agent'] = $_SERVER['HTTP_USER_AGENT'];
		}

		$out = "GET {$uri} HTTP/1.1\r\n";

		foreach($headers as $header => $value)
		{
			$out .= "$header: $value\r\n";
		}

		$out .= "\r\n";

		// send the request
		$fp = fsockopen($host, $port, $errno, $errstr, 1);
		fwrite($fp, $out);
		fclose($fp);
	}
	
	final public function output()
	{
		$this->prepareFlavors();
		
		if ($this->deliveryCode)
			$this->replaceDeliveryCode();

		$this->replacePlayServerSessionId();
		
		$this->tokenizeUrls();
		$this->applyDomainPrefix();
	
		$headers = $this->getHeaders();
		$headers[] = "Access-Control-Allow-Origin:*";
		$headers[] = "Access-Control-Expose-Headers: Server,range,Content-Length,Content-Range";
		foreach ($headers as $header)
		{
			header($header);
		}
		
		if (kApiCache::hasExtraFields() && !$this->forceCachingHeaders)
			$this->cachingHeadersAge = 0;
		
		infraRequestUtils::sendCachingHeaders($this->cachingHeadersAge, true, $this->lastModified);

		$header = $this->getManifestHeader();
		$footer = $this->getManifestFooter();
		$flavors = $this->getManifestFlavors();
		foreach ($this->contributors as $contributorInstance)
		{
			/* @var $contributorInstance BaseManifestEditor */
			$header = $contributorInstance->editManifestHeader($header);
			$footer = $contributorInstance->editManifestFooter ($footer);
			$flavors = $contributorInstance->editManifestFlavors($flavors);
		}
		
		$separator = $this->getSeparator();
		
		$content = $header;
		if ($content)
		{
			$content .= $separator;
		}
		$content .= implode($separator, $flavors);
		$content .= $separator . $footer;
		
		header('Content-Length: ' . strlen($content));		// avoid chunked encoding
		
		echo $content;
		
		if (kConf::hasParam('internal_analytics_host'))
		{
			$statsHost = explode(':', kConf::get('internal_analytics_host'));
			$this->sendAnalyticsBeacon(
				$statsHost[0], 
				isset($statsHost[1]) ? $statsHost[1] : 80);
		}

		die;
	}
	
	public function getRequiredFiles()
	{
		$result = array(__file__);
		$thisClass = new ReflectionClass(get_class($this));
		$result[] = $thisClass->getFileName();
		if ($this->tokenizer)
		{
			$result[] = dirname(__file__) . '/storage/urlTokenizers/kUrlTokenizer.php';
			$tokenizerClass = new ReflectionClass(get_class($this->tokenizer));
			$result[] = $tokenizerClass->getFileName();
		}
		
		foreach ($this->contributors as $contributor)
		{
			$result[] = dirname(__FILE__) . '/manifest/BaseManifestEditor.php';
			$contributorClass = new ReflectionClass(get_class($contributor));
			$result[] = $contributorClass->getFileName();
		}
		
		return $result;
	}
	
	/**
	 * @param string $part1
	 * @param string $part2
	 * @return string
	 */
	static protected function urlJoin($part1, $part2)
	{
		if (!$part1)
			return $part2;
		if (!$part2)
			return $part1;
		return rtrim($part1, '/') . '/' . ltrim($part2, '/');
	}

	/**
	 * @param array $flavor
	 */
	static protected function normalizeUrlPrefix(&$flavor)
	{
		if(!isset($flavor['urlPrefix']) || !$flavor['urlPrefix'])
			return;
			
		$urlPrefix = $flavor['urlPrefix'];		
		$urlPrefixPath = parse_url($urlPrefix, PHP_URL_PATH);
		if (!$urlPrefixPath || substr($urlPrefix, -strlen($urlPrefixPath)) != $urlPrefixPath)
			return;

		$flavor['urlPrefix'] = substr($urlPrefix, 0, -strlen($urlPrefixPath));
		$flavor['url'] = self::urlJoin($urlPrefixPath, $flavor['url']);
	}

	protected static function generateSessionId()
	{
		return mt_rand();
	}
}

class kSingleUrlManifestRenderer extends kManifestRenderer
{
	/**
	 * @var array
	 */
	public $flavor = null;
	
	function __construct($flavors, $entryId = null) 
	{
		$this->flavor = reset($flavors);	
		$this->entryId = $entryId;
	}
	
	protected function replaceDeliveryCode()
	{
		$this->flavor['url'] = str_replace("{deliveryCode}", $this->deliveryCode, $this->flavor['url']);
		$this->flavor['urlPrefix'] = str_replace("{deliveryCode}", $this->deliveryCode, $this->flavor['urlPrefix']);
 	}
	
	protected function tokenizeUrls()
	{
		self::normalizeUrlPrefix($this->flavor);
		$url = $this->flavor['url'];
		$urlPrefix = isset($this->flavor['urlPrefix']) ? $this->flavor['urlPrefix'] : null;
		if ($this->tokenizer)
		{
			$url = $this->tokenizer->tokenizeSingleUrl($url, $urlPrefix);
		}
		
		if($urlPrefix !== null)
		{
			$url = self::urlJoin($urlPrefix, $url);
			unset($this->flavor['urlPrefix']);	// no longer need the prefix
		}
		
		$this->flavor['url'] = $url;
	}
	
	protected function applyDomainPrefix()
	{
		$domainPrefix = isset($this->flavor['domainPrefix']) ? $this->flavor['domainPrefix'] : null;
		
		if($domainPrefix)
		{
			$urlParts = explode("://", $this->flavor['url']);
			$this->flavor['url'] = $urlParts[0] . "://" . $domainPrefix . $urlParts[1];
		}
		
		unset($this->flavor['domainPrefix']);
	}

	protected function replacePlayServerSessionId()
	{
		$this->flavor['url'] = str_replace("{sessionId}",self::generateSessionId(), $this->flavor['url']);
	}
}

class kMultiFlavorManifestRenderer extends kManifestRenderer
{
	/**
	 * @var array
	 */
	public $flavors = array();
	
	/**
	 * @var string
	 */
	public $baseUrl = '';
	
	function __construct($flavors, $entryId = null, $baseUrl = '')
	{
		$this->flavors = $flavors;
		$this->entryId = $entryId;
		$this->baseUrl = $baseUrl;
	}
	
	protected function replaceDeliveryCode()
	{
		$this->baseUrl = str_replace("{deliveryCode}", $this->deliveryCode, $this->baseUrl);
		
		foreach ($this->flavors as &$flavor)
		{
			$flavor['url'] = str_replace("{deliveryCode}", $this->deliveryCode, $flavor['url']);
			if (isset($flavor['urlPrefix']))
				$flavor['urlPrefix'] = str_replace("{deliveryCode}", $this->deliveryCode, $flavor['urlPrefix']);
		}
	}
	
	protected function tokenizeUrls()
	{
		if ($this->baseUrl)
		{
			if ($this->tokenizer)
			{
				$this->tokenizer->tokenizeMultiUrls($this->baseUrl, $this->flavors);
			}
			return;
		}

		$prefixes = array();
		foreach ($this->flavors as &$flavor)
		{
			self::normalizeUrlPrefix($flavor);
			if(!isset($flavor['urlPrefix']))
			{
				$prefixes = array();
				break;
			}
			$prefixes[$flavor['urlPrefix']] = true;
		}
		
		if (count($prefixes) == 1)
		{
			reset($prefixes);
			$baseUrl = key($prefixes);
			if ($this->tokenizer)
			{
				$this->tokenizer->tokenizeMultiUrls($baseUrl, $this->flavors);
			}
			foreach ($this->flavors as &$flavor)
			{
				$flavor['url'] = self::urlJoin($baseUrl, $flavor['url']);
				unset($flavor['urlPrefix']);		// no longer need the prefix
			}
			return;
		}
		
		foreach ($this->flavors as &$flavor)
		{
			$url = $flavor['url'];
			$urlPrefix = isset($flavor['urlPrefix']) ? $flavor['urlPrefix'] : null;
			if ($this->tokenizer)
			{
				$url = $this->tokenizer->tokenizeSingleUrl($url, $urlPrefix);
			}
			
			if($urlPrefix !== null)
			{
				$url = self::urlJoin($urlPrefix, $url);
				unset($flavor['urlPrefix']);		// no longer need the prefix
			}
			
			$flavor['url'] = $url;
		}
	}
	
	protected function applyDomainPrefix()
	{
		foreach ($this->flavors as &$flavor)
		{
			$domainPrefix = isset($flavor['domainPrefix']) ? $flavor['domainPrefix'] : null;
			
			if($domainPrefix)
			{
				$urlParts = explode("://", $flavor['url']);
				$flavor['url'] = $urlParts[0] . "://" . $domainPrefix . $urlParts[1];
			}
			
			unset($flavor['domainPrefix']);
		}
	}

	protected function replacePlayServerSessionId()
	{
		$sessionId = self::generateSessionId();

		foreach ($this->flavors as &$flavor)
		{
			$flavor['url'] = str_replace("{sessionId}", $sessionId, $flavor['url']);
		}
	}
}

class kF4MManifestRenderer extends kMultiFlavorManifestRenderer
{
	/**
	 * @var string
	 */
	public $streamType = self::PLAY_STREAM_TYPE_RECORDED;

	/**
	 * @var strimg
	 */
	public $mediaUrl = '';

	/**
	 * @var string
	 */
	public $mimeType = 'video/x-flv';

	/**
	 * @var array
	 */
	public $bootstrapInfos = array();

	/**
	 * @var int
	 */
	public $dvrWindow = null;
	
	function __construct($flavor, $entryId = null, $baseUrl = '') {
		parent::__construct($flavor, $entryId, $baseUrl);
		
		$entry = entryPeer::retrieveByPK($this->entryId);
		$this->setMimeType($entry);
	}
	
	/**
	 * @return array<string>
	 */
	protected function getHeaders()
	{
		return array(
			"Content-Type: text/xml; charset=UTF-8",
			"Content-Disposition: inline; filename=manifest.xml",
			);
	}

	/**
	 * @return string
	 */
	protected function buildFlavorsArray()
	{
		$flavorsArray = array();

		$deliveryCodeStr = '';
		if ($this->streamType == self::PLAY_STREAM_TYPE_LIVE && $this->deliveryCode)
		{
			$deliveryCodeStr = '?deliveryCode='.$this->deliveryCode;
		}
		
		foreach($this->flavors as $flavor)
		{
			$url = $flavor['url'];
			$bitrate			= isset($flavor['bitrate'])			? $flavor['bitrate']			: 0;
			$width				= isset($flavor['width'])			? $flavor['width']				: 0;
			$height				= isset($flavor['height'])			? $flavor['height']				: 0;
			$bootstrapInfoId	= isset($flavor['bootstrapInfoId'])	? $flavor['bootstrapInfoId']	: '';
			
			$url = htmlspecialchars($url . $deliveryCodeStr);
			
			$mediaElement = "<media url=\"$url\" bitrate=\"$bitrate\" width=\"$width\" height=\"$height\"";
			if(isset($flavor['bootstrapInfoId']) && isset($this->bootstrapInfos[$flavor['bootstrapInfoId']]))
			{
				$bootstrapInfo = $this->bootstrapInfos[$flavor['bootstrapInfoId']];
				$bootstrapInfoElement = '<bootstrapInfo id="' . $bootstrapInfo['id'] . '" profile="named" url="' . $bootstrapInfo['url'] . '" />';
				$mediaElement = $bootstrapInfoElement . $mediaElement . ' bootstrapInfoId="' . $flavor['bootstrapInfoId'] . '"';
			}
			$mediaElement .= ' />';
			
			$flavorsArray[] = $mediaElement;
		}		
		
		return $flavorsArray;
	}
	
	protected function getManifestHeader()
	{
		$durationXml = ($this->duration ? "<duration>{$this->duration}</duration>" : '');
		$baseUrlXml = ($this->baseUrl ? "<baseURL>".htmlspecialchars($this->baseUrl)."</baseURL>" : '');
		$dvrXml = ($this->dvrWindow ? "<dvrInfo windowDuration=\"{$this->dvrWindow}\"></dvrInfo>" : '');
		
		return 
	"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
	<manifest xmlns=\"http://ns.adobe.com/f4m/1.0\">
		<id>{$this->entryId}</id>
		<mimeType>{$this->mimeType}</mimeType>
		<streamType>{$this->streamType}</streamType>		
		{$dvrXml}					
		{$durationXml}		
		{$baseUrlXml}";
	}
	
	protected function getManifestFooter()
	{
		$mediaUrl = '';
		if ($this->mediaUrl)
		{
			$mediaUrl = "<media url=\"".htmlspecialchars($this->mediaUrl)."\"/>";
		}
		return "{$mediaUrl}
			</manifest>";
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestFlavors()
	 */
	protected function getManifestFlavors()
	{
		return $this->buildFlavorsArray();
	}
	
	/**
	 * @param array $flavors
	 * @return string
	 */
	protected function setMimeType(entry $entry)
	{
		if ($entry->getType() == entryType::MEDIA_CLIP && count($this->flavors))
		{
			$isMp3 = true;
			foreach($this->flavors as $flavor)
			{
				if (!isset($flavor['ext']) || strtolower($flavor['ext']) != 'mp3')
					$isMp3 = false;
			}
	
			if ($isMp3) {
				$this->mimeType = 'audio/mpeg';
				return;
			}
		}
	
		$this->mimeType = 'video/x-flv';
	}
}
	
class kF4Mv2ManifestRenderer extends kMultiFlavorManifestRenderer
{
	/**
	 * @return array<string>
	 */
	protected function getHeaders()
	{
		return array(
			"Content-Type: text/xml; charset=UTF-8",
			"Content-Disposition: inline; filename=manifest.xml",
			);
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestFlavors()
	 */
	protected function getManifestFlavors()
	{
		return $this->buildFlavorsArray();
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestFooter()
	 */
	protected function getManifestFooter()
	{
		return "</manifest>";
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestHeader()
	 */
	protected function getManifestHeader()
	{
		$durationXml = ($this->duration ? "<duration>{$this->duration}</duration>" : '');
		
		return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
		<manifest xmlns=\"http://ns.adobe.com/f4m/2.0\">
			<id>{$this->entryId}</id>
			{$durationXml}";
	}

	/**
	 * @return array
	 */
	protected function buildFlavorsArray()
	{
		$flavorsArray = array();

		foreach($this->flavors as $flavor)
		{
			$url = $flavor['url'];
			$bitrate	= isset($flavor['bitrate'])	? $flavor['bitrate']	: 0;
			$width		= isset($flavor['width'])	? $flavor['width']		: 0;
			$height		= isset($flavor['height'])	? $flavor['height']		: 0;
			
			$flavorsArray[] = "<media href=\"$url\" bitrate=\"$bitrate\" width=\"$width\" height=\"$height\"/>";
		}		
		
		return $flavorsArray;
	}

}
	
class kSilverLightManifestRenderer extends kSingleUrlManifestRenderer
{
	/**
	 * @var string
	 */
	public $streamType = self::PLAY_STREAM_TYPE_RECORDED;

	/**
	 * @return array<string>
	 */
	protected function getHeaders()
	{
		return array(
			"Content-Type: text/xml; charset=UTF-8",
			"Content-Disposition: inline; filename=manifest.xml",
			);
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestHeader()
	 */
	protected function getManifestHeader()
	{
		$manifestUrl = htmlspecialchars($this->flavor['url']);		
		$durationXml = ($this->duration ? "<duration>{$this->duration}</duration>" : '');

		return 
			"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<manifest url=\"{$manifestUrl}\">
				<id>{$this->entryId}</id>
				<streamType>{$this->streamType}</streamType>
				{$durationXml}
			</manifest>";
	}
}

class kSmilManifestRenderer extends kMultiFlavorManifestRenderer
{
	/**
	 * @return array<string>
	 */
	protected function getHeaders()
	{
		return array(
			"Content-Type: text/xml; charset=UTF-8",
			"Content-Disposition: inline; filename=manifest.xml",
			);
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestFlavors()
	 */
	protected function getManifestFlavors()
	{
		$flavorsArr = array();
		foreach ($this->flavors as $flavor)
		{
			$bitrate = isset($flavor['bitrate'])	? $flavor['bitrate']	: 0;

			$url = $flavor['url'];
			$url = parse_url($url, PHP_URL_PATH);
			$url = htmlspecialchars($url);
			$flavorsArr[] = "<video src=\"{$url}\" system-bitrate=\"".($bitrate * 1000)."\"/>"; 
		}
		
		return $flavorsArr;
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestHeader()
	 */
	protected function getManifestHeader()
	{
		$domain = '';
		foreach ($this->flavors as $flavor)
		{
			$url = $flavor['url'];
			$domain = parse_url($url, PHP_URL_SCHEME)."://".parse_url($url, PHP_URL_HOST);
		}
		
		return '<?xml version="1.0"?>
				<!DOCTYPE smil PUBLIC "-//W3C//DTD SMIL 2.0//EN" "http://www.w3.org/2001/SMIL20/SMIL20.dtd">
				<smil xmlns="http://www.w3.org/2001/SMIL20/Language">
					<head>
						<meta name="title" content="" />
						<meta name="httpBase" content="'.$domain.'" />
						<meta name="vod" content="true"/>
					</head>
					<body>
						<switch id="video">';
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestFooter()
	 */
	protected function getManifestFooter()
	{
		return '</switch>
			</body>
		</smil>';
	}

}

class kM3U8ManifestRenderer extends kMultiFlavorManifestRenderer
{
	/**
	* @var bool
	*/
	protected $hasAudioFlavors = false;
	
	function __construct($flavors, $entryId = null, $baseUrl = '') 
	{
		parent::__construct($flavors, $entryId, $baseUrl);
		
		// check if audio flavors exist
		foreach($this->flavors as $flavor) {
			if (isset($flavor['audioLanguage'])) {
				$this->hasAudioFlavors = true;
				break;
			}
		}
	}
    
	/**
	 * @return array<string>
	 */
	protected function getHeaders()
	{
		return array("Content-Type: application/x-mpegurl");
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestFlavors()
	 */
	protected function getManifestFlavors()
	{
		$audioFlavorsArr = array(); 
		$audio = null;
		if ($this->hasAudioFlavors) {
			$audio = ",AUDIO=\"audio\"";
		}
		
		$flavorsArr = array();
		foreach($this->flavors as $flavor)
		{
			// Sperate audio flavors from video flavors
			if ( isset($flavor['audioLanguage']) || isset($flavor['audioLabel']) ) {
				$isFirstAudioStream = (count($audioFlavorsArr) == 0) ? "YES" : "NO";
				$language = (isset($flavor['audioLanguage'])) ? $flavor['audioLanguage'] : 'und';
				$languageName = (isset($flavor['audioLabel'])) ? $flavor['audioLabel'] : $flavor['audioLanguageName'];
				$content = "#EXT-X-MEDIA:TYPE=AUDIO,GROUP-ID=\"audio\",LANGUAGE=\"{$language}\",NAME=\"{$languageName}\"" . 
						",AUTOSELECT=$isFirstAudioStream,DEFAULT=$isFirstAudioStream,URI=\"{$flavor['url']}\"";
				$audioFlavorsArr[] = $content;
			}
			else {
				$bitrate = (isset($flavor['bitrate']) ? $flavor['bitrate'] : 0) * 1024;
				$codecs = "";
				// in case of Akamai HDN1.0 increase the reported bitrate due to mpeg2-ts overhead
				if (strpos($flavor['url'], "index_0_av.m3u8"))
					$bitrate += 40 * 1024;

				$resolution = '';
				if(isset($flavor['width']) && isset($flavor['height']) &&
					(($flavor['width'] > 0) || ($flavor['height'] > 0)))
				{
					$width = $flavor['width'];
					$height = $flavor['height'];
					if ($width && $height)
						$resolution = ",RESOLUTION={$width}x{$height}";
				}
				else if ($bitrate && $bitrate <= 65536)
					$codecs = ',CODECS="mp4a.40.2"';
				$content = "#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH={$bitrate}{$resolution}{$codecs}{$audio}\n";
				$content .= $flavor['url'];
				$flavorsArr[] = $content;
			}
		}
		if (count($audioFlavorsArr) > 0) {
			return array_merge($audioFlavorsArr, array(''), $flavorsArr);
		}		
		return $flavorsArr;
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestHeader()
	 */
	protected function getManifestHeader()
	{
		return "#EXTM3U";
	}

}

class kRtspManifestRenderer extends kSingleUrlManifestRenderer
{
	/**
	 * @return array<string>
	 */
	protected function getHeaders()
	{
		return array("Content-Type: text/html; charset=UTF-8");
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestHeader()
	 */
	protected function getManifestHeader()
	{
		return '<html><head><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($this->flavor['url']) . '"></head></html>';
	}
	
}

class kDashBaseManifestRenderer extends kMultiFlavorManifestRenderer
{
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getHeaders()
	 */
	protected function getHeaders()
	{
		return array(
			'Content-Type: application/dash+xml',
		);
	}

	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestHeader()
	 */
	protected function getManifestHeader ()
	{
		return '<?xml version="1.0" encoding="UTF-8"?>
<MPD xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
     xmlns="urn:mpeg:dash:schema:mpd:2011"
     xmlns:xlink="http://www.w3.org/1999/xlink"
     xsi:schemaLocation="urn:mpeg:DASH:schema:MPD:2011 http://standards.iso.org/ittf/PubliclyAvailableStandards/MPEG-DASH_schema_files/DASH-MPD.xsd"
     minimumUpdatePeriod="PT0S">';
	}
	
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestFooter()
	 */
	protected function getManifestFooter()
	{
		return '</MPD>';
	}
}

class kDashRedirectManifestRenderer extends kDashBaseManifestRenderer
{
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestFlavors()
	 */
	protected function getManifestFlavors()
	{
		$flavor = reset($this->flavors);
		$url = str_replace(" ", "%20", $flavor['url']);
		return array(
			"<Location>$url</Location>"
		);
	}
}

class kRedirectManifestRenderer extends kSingleUrlManifestRenderer
{
	/**
	 * @return array<string>
	 */
	protected function getHeaders()
	{
		$url = str_replace(" ", "%20", $this->flavor['url']);
		return array("location:{$url}");
	}
}

class kJSONManifestRenderer extends kMultiFlavorManifestRenderer
{
	/**
	 * @return array<string>
	 */
	protected function getHeaders()
	{
		return array(
				header("Content-Type: application/json"),
		);
	}
	
	protected function buildFlavorsArray()
	{
		return array(
				'entryId' => $this->entryId,
				'duration' => $this->duration,
				'baseUrl' => $this->baseUrl,
				'flavors' => $this->flavors,
		);
	}


	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestFlavors()
	 */
	protected function getManifestFlavors()
	{
		$result = $this->buildFlavorsArray();
		return array(json_encode($result));
	}
}

class kJSONPManifestRenderer extends kJSONManifestRenderer
{
	/* (non-PHPdoc)
	 * @see kManifestRenderer::getManifestFlavors()
	 */
	protected function getManifestFlavors()
	{
		$ALLOWED_REGEX = "/^[0-9_a-zA-Z.]*$/";
		$callback = isset($_GET["callback"]) ? $_GET["callback"] : null;
		// check for a valid callback, prevent xss
		if (is_null($callback) || !preg_match($ALLOWED_REGEX, $callback))
			die("Expecting \"callback\" parameter for jsonp format");
		
		$result = $this->buildFlavorsArray();
		return array($callback . '(' . json_encode($result) . ')');
	}
}
