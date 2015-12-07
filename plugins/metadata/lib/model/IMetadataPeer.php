<?php

/**
 * @package plugins.metadata
 * @subpackage model.interfaces
 */
interface IMetadataPeer
{
	public static function retrieveByPK($pk, PropelPDO $con = null);
	
	public static function validateMetadataObjects($profileField, $objectIds, &$errorMessage);
}