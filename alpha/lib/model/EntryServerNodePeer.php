<?php


/**
 * Skeleton subclass for performing query and update operations on the 'entry_server_node' table.
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
class EntryServerNodePeer extends BaseEntryServerNodePeer {

	// cache classes by their type
	protected static $class_types_cache = array(
		EntryServerNodeType::LIVE_PRIMARY => LiveEntryServerNode::OM_CLASS,
		EntryServerNodeType::LIVE_BACKUP => LiveEntryServerNode::OM_CLASS,
		EntryServerNodeType::LIVE_CLIPPING_TASK => ClippingTaskEntryServerNode::OM_CLASS,
	);
	
	public static $connectedServerNodeStatuses = array(
		EntryServerNodeStatus::AUTHENTICATED,
		EntryServerNodeStatus::BROADCASTING,
		EntryServerNodeStatus::PLAYABLE
	);

	public static function getOMClass($row, $column)
	{
		if ($row)
		{
			$typeField = self::translateFieldName(EntryServerNodePeer::SERVER_TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$entryServerNodeServerType = $row[$typeField];
			if(isset(self::$class_types_cache[$entryServerNodeServerType]))
				return self::$class_types_cache[$entryServerNodeServerType];

			$extendedCls = KalturaPluginManager::getObjectClass(parent::OM_CLASS, $entryServerNodeServerType);
			if($extendedCls)
			{
				self::$class_types_cache[$entryServerNodeServerType] = $extendedCls;
				return $extendedCls;
			}

			self::$class_types_cache[$entryServerNodeServerType] = parent::OM_CLASS;
		}
		return self::$class_types_cache[$entryServerNodeServerType];
	}

	/**
	 * Retrieve an array of a single object by EntryId and EntryServerNodeType.
	 *
	 * @param      string $entryId .
	 * @param      EntryServerNodeType $serverType .
	 * @param      PropelPDO $con the connection to use
	 * @return 	   EntryServerNode of matching EntryServerNode
	 * @throws     kCoreException
	 */
	public static function retrieveByEntryIdAndServerType($entryId, $serverType, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(EntryServerNodePeer::ENTRY_ID, $entryId);
		$criteria->add(EntryServerNodePeer::SERVER_TYPE, $serverType);

		return EntryServerNodePeer::doSelectOne($criteria, $con);
	}
	
	/**
	 * Retrieve a count of all EntryServerNodeType associated with a partner.
	 *
	 * @param      string $partnerId .
	 * @param      EntryServerNodeType $serverType .
	 * @param      PropelPDO $con the connection to use
	 * @return 	   int of EntryServerNodes under partnerId
	 * @throws     kCoreException
	 */
	public static function retrieveByPartnerIdAndServerType($partnerId, $serverType, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(EntryServerNodePeer::PARTNER_ID, $partnerId);
		$criteria->add(EntryServerNodePeer::SERVER_TYPE, $serverType);
		
		return EntryServerNodePeer::doCount($criteria, $con);
	}
	
	/**
	 * Retrieve an array of a objects by EntryId
	 *
	 * @param      string $entryId.
	 * @param      PropelPDO $con the connection to use
	 * @return     array Array of matching EntryServerNodes
	 */
	public static function retrieveByEntryId($entryId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(EntryServerNodePeer::ENTRY_ID, $entryId);

		return EntryServerNodePeer::doSelect($criteria, $con);

	}

	/**
	 * Deleted all db instances matching the EntryId
	 *
	 * @param      string $entryId.
	 * @param      PropelPDO $con the connection to use
	 * @return     array Array of effected rows
	 */
	public static function deleteByEntryId($entryId, PropelPDO $con = null)
	{
		$criteria = new Criteria();
		$criteria->add(EntryServerNodePeer::ENTRY_ID, $entryId);
		return EntryServerNodePeer::doDelete($criteria, $con);
	}

	public static function getCacheInvalidationKeys()
	{
		return array(array("entryServerNode:id=%s", self::ID), array("entryServerNode:entryId%s", self::ENTRY_ID));		
	}

	public static function retrievePlayableByEntryId($entryId)
	{
		$entryServerNodes =  self::retrieveByEntryId($entryId);
		$playableEntryServerNodes = array();
		foreach ( $entryServerNodes as $entryServerNode)
		{
			/* @var EntryServerNode $entryServerNode */
			if ($entryServerNode->getStatus() == EntryServerNodeStatus::PLAYABLE && in_array($entryServerNode->getServerType(),array(EntryServerNodeType::LIVE_BACKUP, EntryServerNodeType::LIVE_PRIMARY)))
				$playableEntryServerNodes[] = $entryServerNode;
		}
		return $playableEntryServerNodes;
	}
	
	public static function retrieveByEntryIdAndStatuses($entryId, $statuses)
	{
		$c = new Criteria();
		$c->add(EntryServerNodePeer::ENTRY_ID, $entryId);
		$c->add(EntryServerNodePeer::STATUS, $statuses, Criteria::IN);
		
		return EntryServerNodePeer::doSelect($c);
	}
	
	public static function retrieveConnectedEntryServerNodesByPartner($partnerId, $excludeEntryId)
	{		
		$c = KalturaCriteria::create(EntryServerNodePeer::OM_CLASS);
		$c->add(EntryServerNodePeer::PARTNER_ID, $partnerId);
		$c->add(EntryServerNodePeer::ENTRY_ID, $excludeEntryId, Criteria::NOT_EQUAL);
		$c->add(EntryServerNodePeer::STATUS, self::$connectedServerNodeStatuses, Criteria::IN);
		$c->addGroupByColumn(EntryServerNodePeer::ENTRY_ID);
		
		return EntryServerNodePeer::doSelect($c);
	}

	public static function retrieveByServerNodeIdAndType($serverNodeId, $type)
	{
		$c = KalturaCriteria::create(EntryServerNodePeer::OM_CLASS);
		$c->add(EntryServerNodePeer::SERVER_NODE_ID, $serverNodeId);
		$c->add(EntryServerNodePeer::SERVER_TYPE, $type);
		return EntryServerNodePeer::doSelect($c);
	}
} // EntryServerNodePeer
