<?php

abstract class kManifestRenderer
{
	const PLAY_STREAM_TYPE_LIVE = 'live';
	const PLAY_STREAM_TYPE_RECORDED = 'recorded';
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
	 * @var string
	 */
	public $deliveryCode = '';
	
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
	protected function getBody()
	{
		return '';
	}

	// allow to replace {deliveryCode} place holder with the deliveryCode parameter passed to the action
	// a publisher with a rtmpUrl set to {deliveryCode}.example.com/ondemand will be able to use different
	// cdn configuration for different sub publishers by passing a different deliveryCode to the KDP
	abstract protected function replaceDeliveryCode();
	
	abstract protected function tokenizeUrls();
		
	/**
	 * @param string $playbackContext
	 */
	public function output($deliveryCode, $playbackContext)
	{
		$this->deliveryCode = $deliveryCode;		
		if ($this->deliveryCode)
			$this->replaceDeliveryCode();
	
		if ($this->tokenizer)
		{
			$this->tokenizer->setPlaybackContext($playbackContext);
		}
		
		$this->tokenizeUrls();
	
		$headers = $this->getHeaders();
		foreach ($headers as $header)
		{
			header($header);
		}
		
		infraRequestUtils::sendCachingHeaders(kApiCache::hasExtraFields() ? 0 : $this->cachingHeadersAge);

		echo $this->getBody();
		die;
	}
	
	public function getRequiredFiles()
	{
		$result = array(__file__);
		if ($this->tokenizer)
		{
			$result[] = dirname(__file__) . '/storage/urlTokenizers/kUrlTokenizer.php';
			$tokenizerClass = new ReflectionClass(get_class($this->tokenizer));
			$result[] = $tokenizerClass->getFileName();
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
}

class kSingleUrlManifestRenderer extends kManifestRenderer
{
	/**
	 * @var array
	 */
	public $flavor = null;
	
	protected function replaceDeliveryCode()
	{
		$this->flavor['url'] = str_replace("{deliveryCode}", $this->deliveryCode, $this->flavor['url']);
	}
	
	protected function tokenizeUrls()
	{
		$url = $this->flavor['url'];
		if ($this->tokenizer)
		{
			$url = $this->tokenizer->tokenizeSingleUrl($url);
		}
		
		$this->flavor['url'] = self::urlJoin($this->flavor['urlPrefix'], $url);
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
	
	protected function replaceDeliveryCode()
	{
		$this->baseUrl = str_replace("{deliveryCode}", $this->deliveryCode, $this->baseUrl);
		
		foreach ($this->flavors as &$flavor)
		{
			$flavor['url'] = str_replace("{deliveryCode}", $this->deliveryCode, $flavor['url']);
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
		}
		else
		{
			foreach ($this->flavors as &$flavor)
			{
				$url = $flavor['url'];
				if ($this->tokenizer)
				{
					$url = $this->tokenizer->tokenizeSingleUrl($url);
				}
				$flavor['url'] = self::urlJoin($flavor['urlPrefix'], $url);
			}
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
	protected function buildFlavorsXml()
	{
		$flavorsXml = '';

		$deliveryCodeStr = '';
		if ($this->streamType == self::PLAY_STREAM_TYPE_LIVE && $this->deliveryCode)
		{
			$deliveryCodeStr = '?deliveryCode='.$this->deliveryCode;
		}
		
		foreach($this->flavors as $flavor)
		{
			$url = $flavor['url'];
			$bitrate	= isset($flavor['bitrate'])	? $flavor['bitrate']	: 0;
			$width		= isset($flavor['width'])	? $flavor['width']		: 0;
			$height		= isset($flavor['height'])	? $flavor['height']		: 0;
			
			$url = htmlspecialchars($url . $deliveryCodeStr);
			$flavorsXml .= "<media url=\"$url\" bitrate=\"$bitrate\" width=\"$width\" height=\"$height\"/>";
		}		
		
		return $flavorsXml;
	}
	
	/**
	 * @return string
	 */
	protected function getBody()
	{		
		$durationXml = ($this->duration ? "<duration>{$this->duration}</duration>" : '');
		$baseUrlXml = ($this->baseUrl ? "<baseURL>".htmlspecialchars($this->baseUrl)."</baseURL>" : '');
		$flavorsXml = $this->buildFlavorsXml();
		$mediaUrl = '';
		if ($this->mediaUrl)
		{
			$mediaUrl = "<media url=\"".htmlspecialchars($this->mediaUrl)."\"/>";
		}
				
		return 
"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<manifest xmlns=\"http://ns.adobe.com/f4m/1.0\">
	<id>{$this->entryId}</id>
	<mimeType>{$this->mimeType}</mimeType>
	<streamType>{$this->streamType}</streamType>					
	{$durationXml}
	{$baseUrlXml}
	{$flavorsXml}
	{$mediaUrl}
</manifest>";
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

	/**
	 * @return string
	 */
	protected function buildFlavorsXml()
	{
		$flavorsXml = '';

		foreach($this->flavors as $flavor)
		{
			$url = $flavor['url'];
			$bitrate	= isset($flavor['bitrate'])	? $flavor['bitrate']	: 0;
			$width		= isset($flavor['width'])	? $flavor['width']		: 0;
			$height		= isset($flavor['height'])	? $flavor['height']		: 0;
			
			$flavorsXml .= "<media href=\"$url\" bitrate=\"$bitrate\" width=\"$width\" height=\"$height\"/>";
		}		
		
		return $flavorsXml;
	}
	
	/**
	 * @return string
	 */
	protected function getBody()
	{		
		$durationXml = ($this->duration ? "<duration>{$this->duration}</duration>" : '');
		$flavorsXml = $this->buildFlavorsXml();
		$mediaUrl = '';
				
		return 
"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<manifest xmlns=\"http://ns.adobe.com/f4m/2.0\">
	<id>{$this->entryId}</id>
	{$durationXml}
	{$flavorsXml}
</manifest>";
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

	/**
	 * @return string
	 */
	protected function getBody()
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

	/**
	 * @return string
	 */
	protected function getBody()
	{
		$durationXml = ($this->duration ? "<duration>{$this->duration}</duration>" : '');
		$flavorsXml = '';
		$domain = '';
		foreach ($this->flavors as $flavor)
		{
			$url = $flavor['url'];
			$bitrate = isset($flavor['bitrate'])	? $flavor['bitrate']	: 0;

			$domain = parse_url($url, PHP_URL_SCHEME)."://".parse_url($url, PHP_URL_HOST);
			$url = parse_url($url, PHP_URL_PATH);
			
			$url = htmlspecialchars($url);
			$flavorsXml .= "<video src=\"{$url}\" system-bitrate=\"".($bitrate * 1000)."\"/>"; 
		}
			
		return 
'<?xml version="1.0"?>
<!DOCTYPE smil PUBLIC "-//W3C//DTD SMIL 2.0//EN" "http://www.w3.org/2001/SMIL20/SMIL20.dtd">
<smil xmlns="http://www.w3.org/2001/SMIL20/Language">
	<head>
		<meta name="title" content="" />
		<meta name="httpBase" content="'.$domain.'" />
		<meta name="vod" content="true"/>
	</head>
	<body>
		<switch id="video">'.$flavorsXml.'</switch>
	</body>
</smil>';
	}
}

class kM3U8ManifestRenderer extends kMultiFlavorManifestRenderer
{
	/**
	 * @return array<string>
	 */
	protected function getHeaders()
	{
		return array("Content-Type: application/x-mpegurl");
	}

	/**
	 * @return string
	 */
	protected function getBody()
	{
		$content = "#EXTM3U\n";
		foreach($this->flavors as $flavor)
		{
			$bitrate = (isset($flavor['bitrate']) ? $flavor['bitrate'] : 0) * 1000;
			$codecs = "";
			if ($bitrate && $bitrate <= 64000)
				$codecs = ',CODECS="mp4a.40.2"';
			$content .= "#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH={$bitrate}{$codecs}\n";
			$content .= $flavor['url']."\n";
		}

		return $content;
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

	/**
	 * @return string
	 */
	protected function getBody()
	{
		return '<html><head><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($this->flavor['url']) . '"></head></html>';
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
