<?php

/**
 * @package infra
 * @subpackage cache
 */
class kApiCache
{
	/**
	 * @return int
	 */
	public static function getTime()
	{
		if (class_exists('KalturaResponseCacher'))
			KalturaResponseCacher::setConditionalCacheExpiry(600);
		return time();
	}
}
