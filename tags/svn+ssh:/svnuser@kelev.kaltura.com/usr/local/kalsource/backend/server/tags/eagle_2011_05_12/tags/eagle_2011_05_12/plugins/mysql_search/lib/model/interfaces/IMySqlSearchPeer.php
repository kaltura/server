<?php

interface IMySqlSearchPeer
{
	/**
	 * @return string
	 */
	public static function getPrimaryKeyField();
	
	/**
	 * @return string
	 */
	public static function getSearchPrimaryKeyField();
	
	/**
	 * @param string $name field name
	 * @param string $fromType One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
	 *                         BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM
	 * @param string $toType   One of the class type constants
	 * @return string translated name of the field.
	 * @throws PropelException - if the specified name could not be found in the fieldname mappings.
	 */
	static public function translateFieldName($name, $fromType, $toType);
	
	/**
	 * @param Criteria $criteria
	 * @param PropelPDO $con
	 * @return KalturaStatement
	 */
	public static function doSelectStmt(Criteria $criteria, PropelPDO $con = null);
	
	/**
	 * @param Criteria $criteria
	 * @param bool $distinct
	 * @param PropelPDO $con
	 * @return int
	 */
	public static function doCount(Criteria $criteria, $distinct = false, PropelPDO $con = null);
	
	/**
	 * @param Criteria $criteria
	 * @param bool $distinct
	 * @param PropelPDO $con
	 */
	public static function doCountOnSourceTable(Criteria $criteria, $distinct = false, PropelPDO $con = null);
}