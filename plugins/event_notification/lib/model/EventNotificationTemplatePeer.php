<?php
/**
 * Skeleton subclass for performing query and update operations on the 'event_notification_template' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.eventNotification
 * @subpackage model
 */
class EventNotificationTemplatePeer extends BaseEventNotificationTemplatePeer
{
	// cache classes by their type
	protected static $class_types_cache = array ();
	
	const TM_CLASS = 'EventNotificationTemplateTableMap';
	
	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplatePeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
		if (self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter ();
		
		$c = new Criteria ();
		$c->add ( EventNotificationTemplatePeer::STATUS, EventNotificationTemplateStatus::DELETED, Criteria::NOT_EQUAL );
		
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplatePeer::getOMClass()
	 */
	public static function getOMClass($row, $colnum)
	{
		$type = null;
		if ($row)
		{
			$typeField = self::translateFieldName ( EventNotificationTemplatePeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM );
			$type = $row [$typeField];
			if (isset ( self::$class_types_cache [$type] ))
				return self::$class_types_cache [$type];
			
			$extendedCls = KalturaPluginManager::getObjectClass ( 'EventNotificationTemplate', $type );
			if ($extendedCls)
			{
				self::$class_types_cache [$type] = $extendedCls;
				return $extendedCls;
			}
		}
		
		throw new kCoreException ( "Event notification template type [$type] not found", kCoreException::OBJECT_TYPE_NOT_FOUND, $type );
	}
	
	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplatePeer::buildTableMap()
	 */
	public static function buildTableMap()
	{
		$dbMap = Propel::getDatabaseMap ( EventNotificationTemplatePeer::DATABASE_NAME );
		if (! $dbMap->hasTable ( EventNotificationTemplatePeer::TABLE_NAME ))
		{
			$dbMap->addTableObject ( new EventNotificationTemplateTableMap () );
		}
	}
	
	/**
	 * Retrieve a single object by pkey and type
	 *
	 * @param      int $type the type.
	 * @param      int $pk the primary key.
	 * @param      PropelPDO $con the connection to use
	 * @return     EventNotificationTemplate
	 */
	public static function retrieveTypeByPK($type, $pk, PropelPDO $con = null)
	{
		if (null !== ($obj = EventNotificationTemplatePeer::getInstanceFromPool ( ( string ) $pk )))
		{
			if ($obj->getType () != $type)
				return null;
			
			return $obj;
		}
		
		$criteria = new Criteria ( EventNotificationTemplatePeer::DATABASE_NAME );
		$criteria->add ( EventNotificationTemplatePeer::ID, $pk );
		$criteria->add ( EventNotificationTemplatePeer::TYPE, $type );
		
		return EventNotificationTemplatePeer::doSelectOne ( $criteria, $con );
	}
	
	/**
	 * Retrieve event notification tamplates according to partner
	 *
	 * @param      int $partnerId use null to retrieve from shared partner only
	 * @param      PropelPDO $con the connection to use
	 * @return     array<EventNotificationTemplate>
	 */
	public static function retrieveByPartnerId($partnerId = null, PropelPDO $con = null)
	{
		$criteria = new Criteria ( EventNotificationTemplatePeer::DATABASE_NAME );
		$criteria->add ( EventNotificationTemplatePeer::STATUS, EventNotificationTemplateStatus::ACTIVE );
		
		$partnerIds = $partnerId ? array(PartnerPeer::GLOBAL_PARTNER, $partnerId) : array($partnerId);
		
		$criteria->add(EventNotificationTemplatePeer::PARTNER_ID, array_map('strval',  $partnerIds), Criteria::IN);
		return EventNotificationTemplatePeer::doSelect ( $criteria, $con );
	}
	
	/**
	 * Retrieve event notification tamplates according to event and object type
	 *
	 * @param      int $eventType
	 * @param      int $objectType
	 * @param      int $partnerId use null to retrieve from shared partner only
	 * @param      PropelPDO $con the connection to use
	 * @return     array<EventNotificationTemplate>
	 */
	public static function retrieveByEventType($eventType, $objectType, $partnerId = null, PropelPDO $con = null)
	{
		$criteria = new Criteria ( EventNotificationTemplatePeer::DATABASE_NAME );
		$criteria->add ( EventNotificationTemplatePeer::STATUS, EventNotificationTemplateStatus::ACTIVE );
		$criteria->add ( EventNotificationTemplatePeer::EVENT_TYPE, $eventType );
		$criteria->add ( EventNotificationTemplatePeer::OBJECT_TYPE, $objectType );
		
		$partnerIds = $partnerId ? array(PartnerPeer::GLOBAL_PARTNER, $partnerId) : array($partnerId); 
		
		$criteria->add(EventNotificationTemplatePeer::PARTNER_ID, array_map('strval',  $partnerIds), Criteria::IN);
		return EventNotificationTemplatePeer::doSelect ( $criteria, $con );
	}
	
	/**
	 * Retrieve event notification templates according to systemName
	 * @param string $systemName
	 * @param int $excludeId
	 * @param array<int> $partnerIds
	 * @param PropelPDO $con
	 * @return EventNotificationTemplate
	 */
	public static function retrieveBySystemName ($systemName, $excludeId = null, $partnerIds = null, PropelPDO $con = null)
	{
	    $criteria = new Criteria ( EventNotificationTemplatePeer::DATABASE_NAME );
		$criteria->add ( EventNotificationTemplatePeer::STATUS, EventNotificationTemplateStatus::ACTIVE );
		$criteria->add ( EventNotificationTemplatePeer::SYSTEM_NAME, $systemName );
		if ($excludeId)
		    $criteria->add( EventNotificationTemplatePeer::ID, $excludeId, Criteria::NOT_EQUAL);
		
		// use the partner ids list if given
		if (!$partnerIds)
		{
		    $partnerIds = array (kCurrentContext::getCurrentPartnerId());
		}
		
		$criteria->add(EventNotificationTemplatePeer::PARTNER_ID, array_map('strval',  $partnerIds), Criteria::IN);
		$criteria->addDescendingOrderByColumn(EventNotificationTemplatePeer::PARTNER_ID);
		return EventNotificationTemplatePeer::doSelectOne($criteria);
	}

	public static function getCacheInvalidationKeys()
	{
		return array(array("eventNotificationTemplate:partnerId=%s", self::PARTNER_ID));		
	}
} // EventNotificationTemplatePeer


EventNotificationTemplatePeer::buildTableMap ();