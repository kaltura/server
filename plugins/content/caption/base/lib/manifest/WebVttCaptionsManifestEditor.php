<?php
/**
 * @package plugins.caption
 * @subpackage manifest
 *
 */
class WebVttCaptionsManifestEditor extends BaseManifestEditor
{
	/**
	 * Structured array containing captions information
	 * Structure: "label" => {caption asset label}, "default" => {caption asset default behavior}, "language" => {caption asset language}, "url"=> {caption asset external URL}
	 * @var array
	 */
	public $captions;
	
	
	
	/* (non-PHPdoc)
	 * @see BaseManifestEditor::editManifestHeader()
	 */
	public function editManifestHeader ($manifestHeader)
	{
		foreach ($this->captions as $captionItem)
		{
			$url = $captionItem["url"];
			$urlPrefix = isset($captionItem["urlPrefix"]) ? $captionItem["urlPrefix"] : null;
			
			$tokenizer = isset($captionItem["tokenizer"]) ? $captionItem["tokenizer"] : null;						
			if ($tokenizer)
			{
				$url = $tokenizer->tokenizeSingleUrl($url, $urlPrefix);
			}
			
			if ($urlPrefix)
			{
				$url = rtrim($urlPrefix, '/') . '/' . ltrim($url, '/');
			}
			
			$manifestHeader .= "\n";
			$language = '';
			if (isset($captionItem["language"]))
				$language = 'LANGUAGE="' . $captionItem["language"] . '",';
			$manifestHeader .= '#EXT-X-MEDIA:TYPE=SUBTITLES,GROUP-ID="subs",NAME="' . 
				$captionItem["label"] . '",DEFAULT='.$captionItem["default"] . 
				',AUTOSELECT=YES,FORCED=NO,' . $language. 'URI="' . $url . '"';
		}
		
		return $manifestHeader;
	}
	
	
	/* (non-PHPdoc)
	 * @see BaseManifestEditor::editManifestFlavors()
	 */
	public function editManifestFlavors (array $manifestFlavors)
	{
		if ($this->captions)
		{
			foreach ($manifestFlavors as &$flavor)
			{
				if(!empty($flavor))
				{
					$flavorParts = explode("\n", $flavor);
					$flavorParts[0] .= ',SUBTITLES="subs"';
					$flavor = implode("\n", $flavorParts);
				}
			}
		}
		
		return $manifestFlavors;
	}
}