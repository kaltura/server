<?php

/**
 * @package plugins.metadata
 * @subpackage model.interfaces
 */
interface IMetadataPeer
{
	public function setPartnerId($partnerId);
	
	public static function retrieveByPK($pk, PropelPDO $con = null);
}