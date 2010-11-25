<?php

/**
 * Subclass for representing a row from the 'flavor_params_output' table.
 *
 * 
 *
 * @package lib.model
 */ 
class assetParamsOutput extends BaseflavorParamsOutput
{
	const STR_SEPARATOR = "|||" ;
	const KEY_SEPARATOR = "@@@" ; 
	
	public static function buildCommandLinesStr(array $command_lines)
	{
		$strArr = array();
		foreach($command_lines as $key => $value)
			$strArr[] = $key . self::KEY_SEPARATOR . $value;
			
		return implode ( self::STR_SEPARATOR , $strArr );
	}
	
	public function getCommandLinesStr()
	{
		$command_lines = $this->getCommandLines();
		if(is_null($command_lines))
			return null;
		
		return self::buildCommandLinesStr($command_lines);
	}

	public function setCommandLinesStr($v)
	{
		$arr = explode (self::STR_SEPARATOR , $v );
		$ret = array();
		foreach($arr as $line)
		{
			$arrLine = explode (self::KEY_SEPARATOR , $line, 2 );
			$ret[$arrLine[0]] = $arrLine[1];
		}
		$this->setCommandLines ( $ret );
	} 
		
	/**
	 * @return array
	 */
	public function getCommandLines()
	{
		$command_lines = parent::getCommandLines();
		if(is_null($command_lines))
			return null;
			
		try{
			return @unserialize($command_lines);
		}
		catch(Exception $e)
		{
			return null;
		}
	}
	
	
	/**
	 *
	 * @param array $v
	 */
	public function setCommandLines($v)
	{
		if(is_array($v))
			parent::setCommandLines(serialize($v));
	} 
	
	/**
	 * @return array
	 */
	public function getTagsArray()
	{
		return explode(',', $this->getTags());
	}
	
	/**
	 * @param string $v
	 * @return boolean
	 */
	public function hasTag($v)
	{
		$tags = explode(',', $this->getTags());
		return in_array($v, $tags);
	}
}
