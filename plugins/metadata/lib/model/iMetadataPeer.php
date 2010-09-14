<?php

interface iMetadataPeer
{
	public static function retrieveByPK($pk, PropelPDO $con = null);
	public static function saveToSphinx($objectId, array $data);
}