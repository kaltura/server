<?php


/**
 * Skeleton subclass for performing query and update operations on the 'conf_maps' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class ConfMapsPeer extends BaseConfMapsPeer {

	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
		{
			self::$s_criteria_filter = new criteriaFilter();
		}

		$c = new Criteria();
		$c->add(self::STATUS, ConfMapsStatus::STATUS_ENABLED);
		self::$s_criteria_filter->setFilter($c);
	}

	public static function getLatestMap($mapName , $hostNameRegex = null)
	{
		$c = new criteria();
		$c->add(self::MAP_NAME ,$mapName );
		if($hostNameRegex)
		{
			$c->add(self::HOST_NAME, $hostNameRegex);
		}
		$c->addDescendingOrderByColumn(self::VERSION);
		return self::doSelectOne($c);
	}
	public static function addNewMapVersion($mapName, $hostNameRegex, $content, $newVersion)
	{
		$c = new criteria();
		$c->add(self::MAP_NAME ,$mapName );
		$c->add(self::HOST_NAME ,$hostNameRegex );
		$c->add(self::CONTENT ,$content );
		$c->add(self::VERSION ,$newVersion );
		$c->add(self::STATUS ,ConfMapsStatus::STATUS_ENABLED );
		return self::doInsert($c);
	}
} // ConfMapsPeer
