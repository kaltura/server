<?php

abstract class SyndicationFeedRenderer {
	
	const LEVEL_INDENTATION = '  ';
	
	protected $syndicationFeed;
	protected $syndicationFeedDB;
	protected $mimeType;
	
	public function init($syndicationFeed, $syndicationFeedDB, $mimeType) {
		$this->syndicationFeed = $syndicationFeed;
		$this->syndicationFeedDB = $syndicationFeedDB;
		$this->mimeType = $mimeType;
	}
	
	public abstract function handleHeader();
	public abstract function handleBody($entry, $e = null, $flavorAssetUrl = null);
	public abstract function handleFooter();
	
	/**
	 * @return the HTTP header
	 */
	public function handleHttpHeader() {
		return "content-type: text/xml; charset=utf-8";
	}
	
	/**
	 * Finalizes the object and format it to printable version
	 * @param string $entryMrss Current mrss format
	 * @param boolean $moreItems Whether this is the last entry
	 * @return The formatted mrss
	 */
	public function finalize($entryMrss, $moreItems) {
		return $entryMrss;
	}
	
	protected function getPlayerUrl($entryId)
	{
		$uiconfId = ($this->syndicationFeed->playerUiconfId)? '/ui_conf_id/'.$this->syndicationFeed->playerUiconfId: '';
		$url = 'http://'.kConf::get('www_host').
		'/kwidget/wid/_'.$this->syndicationFeed->partnerId.
		'/entry_id/'.$entryId.$uiconfId;
		return $url;
	}
	
	
	// Writer functions
	
	protected function stringToSafeXml($string, $now = false)
	{
		$string = @iconv('utf-8', 'utf-8', $string);
		$safe = kString::xmlEncode($string);
		return $safe;
	}
	
	
	protected function writeFullXmlNode($nodeName, $value, $level, $attributes = array())
	{
		$res = '';
		$res .= $this->writeOpenXmlNode($nodeName, $level, $attributes, false);
		$res .= kString::xmlEncode(kString::xmlDecode("$value")); //to create a valid XML (without unescaped special chars)
		//we decode before encoding to avoid breaking an xml which its special chars had already been escaped
		$res .= $this->writeClosingXmlNode($nodeName, 0);
		return $res;
	}
	
	protected function writeOpenXmlNode($nodeName, $level, $attributes = array(), $eol = true)
	{
		$tag = $this->getSpacesForLevel($level)."<$nodeName";
		if(count($attributes))
		{
			foreach($attributes as $key => $val)
			{
				$tag .= ' '.$key.'="'.$val.'"';
			}
		}
		$tag .= ">";
	
		if($eol)
			$tag .= PHP_EOL;
	
		return $tag;
	}
	
	protected function writeClosingXmlNode($nodeName, $level = 0)
	{
		return $this->getSpacesForLevel($level)."</$nodeName>".PHP_EOL;
	}
	
	protected function getSpacesForLevel($level)
	{
		$spaces = '';
		for($i=0;$i<$level;$i++) $spaces .= self::LEVEL_INDENTATION;
		return $spaces;
	}
	
	protected function secondsToWords($seconds)
	{
		/*** return value ***/
		$ret = "";
	
		/*** get the hours ***/
		$hours = intval(intval($seconds) / 3600);
		if($hours > 0)
		{
			$ret .= "$hours:";
		}
		/*** get the minutes ***/
		$minutes = (intval($seconds) / 60)%60;
		$ret .= ($minutes >= 10 || $minutes == 0)? "$minutes:": "0$minutes:";
	
		/*** get the seconds ***/
		$seconds = intval($seconds)%60;
		$ret .= ($seconds >= 10)? "$seconds": "0$seconds";
	
		return $ret;
	}
}

?>