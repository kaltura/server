<?php

/**
 * Subclass for performing query and update operations on the 'ui_conf' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class uiConfPeer extends BaseuiConfPeer
{
	public static function retrieveByConfFilePath ( $path , $id =null)
	{
		// search for the canonical way of writing the path
		$c = new Criteria();
		$c->add ( self::CONF_FILE_PATH , $path );
		if ( $id ) $c->add ( self::ID , $id , Criteria::NOT_EQUAL );
		self::getCriteriaFilter()->disable();
		$res = self::doSelect( $c );
		self::getCriteriaFilter()->enable();
		return $res;
	}
	
	public static function setDefaultCriteriaFilter ()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();

		$c = new Criteria();
		$c->add(self::STATUS, uiConf::UI_CONF_STATUS_DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter($c);
	}
		
}
