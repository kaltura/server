<?php


/**
 * Skeleton subclass for performing query and update operations on the 'server_node' table.
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
class ServerNodePeer extends BaseServerNodePeer {

	const EDGE_SERVER_NODE_OM_CLASS = 'EdgeServerNode';
	
	// cache classes by their type
	protected static $class_types_cache = array(
			serverNodeType::EDGE => self::EDGE_SERVER_NODE_OM_CLASS,
	);
	
	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
			self::$s_criteria_filter = new criteriaFilter ();
	
		$c = KalturaCriteria::create(ServerNodePeer::OM_CLASS);
		$c->addAnd ( ServerNodePeer::STATUS, ServerNodeStatus::DELETED, Criteria::NOT_EQUAL);
	
		self::$s_criteria_filter->setFilter($c);
	}
	
	public static function retrieveByHostName($hostName)
	{
		$c = new Criteria();
	
		$c->add(ServerNodePeer::HOST_NAME, $hostName);
	
		return ServerNodePeer::doSelectOne($c);
	}
	
	public static function retrieveByPartnerIdAndHostName($partnerId, $hostName)
	{
		$c = new Criteria();
	
		$c->add(ServerNodePeer::PARTNER_ID, $partnerId);
		$c->add(ServerNodePeer::HOST_NAME, $hostName);
	
		return ServerNodePeer::doSelectOne($c);
	}
	
	public static function retrieveOrderedServerNodesArrayByPKs($pks, PropelPDO $con = null)
	{
		if (empty($pks)) {
			$objs = array();
		}
		else {
			$criteria = new Criteria(ServerNodePeer::DATABASE_NAME);
			$criteria->add(ServerNodePeer::ID, $pks, Criteria::IN);
			$criteria->add(ServerNodePeer::STATUS, ServerNodeStatus::ACTIVE);
			$orderBy = "FIELD (" . self::ID . "," . implode(",", $pks) . ")";  // first take the pattner_id and then the rest
			$criteria->addAscendingOrderByColumn($orderBy);
			$objs = ServerNodePeer::doSelect($criteria, $con);
		}
	
		return $objs;
	}
	
	/* (non-PHPdoc)
	 * @see BaseRemoteServerPeer::getOMClass()
	 */
	public static function getOMClass($row, $colnum)
	{
		$serverNodeType = null;
		if($row)
		{
			$typeField = self::translateFieldName(self::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$serverNodeType = $row[$typeField];
			if(isset(self::$class_types_cache[$serverNodeType]))
				return self::$class_types_cache[$serverNodeType];
	
			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $serverNodeType);
			if($extendedCls)
			{
				self::$class_types_cache[$serverNodeType] = $extendedCls;
				return $extendedCls;
			}
		}
			
		throw new Exception("Can't instantiate un-typed [$serverNodeType] remoteServer [" . print_r($row, true) . "]");
	}
	
} // ServerNodePeer
