<?php
/**
 * @package plugins.caption
 * @subpackage lib
 */
class srtCaptionsContentManager extends kCaptionsContentManager
{
	/* (non-PHPdoc)
	 * @see kCaptionsContentManager::parse()
	 */
	public function parse($content)
	{
		
	}
	
	/**
	 * @return srtCaptionsContentManager
	 */
	public static function get()
	{
		return new srtCaptionsContentManager();
	}
}