<?php
/**
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaAccessControlContributor extends IKalturaBase
{
	/**
	 * Return true if we should skip Access Control rules validation, otherwise false
	 * 
	 * @param string $entryId
	 * @param ks $ks
	 * @return boolean
	 */
	public static function shouldSkipRulesValidation($entryId, $ks);
}