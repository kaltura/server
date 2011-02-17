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