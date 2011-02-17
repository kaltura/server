<?php

/**
 * @package plugins.metadata
 * @subpackage model.interfaces
 */
interface iMetadataPeer
{
	public static function retrieveByPK($pk, PropelPDO $con = null);
}