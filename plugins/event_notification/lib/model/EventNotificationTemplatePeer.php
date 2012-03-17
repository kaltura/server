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
	protected static $class_types_cache = array();

	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplatePeer::setDefaultCriteriaFilter()
	 */
	public static function setDefaultCriteriaFilter()
	{
		if(self::$s_criteria_filter == null)
			self::$s_criteria_filter = new criteriaFilter();
		
		$c = new Criteria();
		$c->add(EventNotificationTemplatePeer::STATUS, EventNotificationTemplateStatus::DELETED, Criteria::NOT_EQUAL);
		 
		self::$s_criteria_filter->setFilter($c);
	}
	
	/* (non-PHPdoc)
	 * @see BaseEventNotificationTemplatePeer::getOMClass()
	 */
	public static function getOMClass($row)
	{
		$type = null;
		if($row)
		{
			$typeField = self::translateFieldName(EventNotificationTemplatePeer::TYPE, BasePeer::TYPE_COLNAME, BasePeer::TYPE_NUM);
			$type = $row[$typeField];
			if(isset(self::$class_types_cache[$type]))
				return self::$class_types_cache[$type];
				
			$extendedCls = KalturaPluginManager::getObjectClass(self::OM_CLASS, $type);
			if($extendedCls)
			{
				self::$class_types_cache[$type] = $extendedCls;
				return $extendedCls;
			}
		}

		throw new kCoreException("Event notification template type [$type] not found", kCoreException::OBJECT_TYPE_NOT_FOUND, $type);
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
		$criteria = new Criteria(EventNotificationTemplatePeer::DATABASE_NAME);
		$criteria->add(EventNotificationTemplatePeer::EVENT_TYPE, $eventType);
		$criteria->add(EventNotificationTemplatePeer::OBJECT_TYPE, $objectType);
		
		if($partnerId)
			$criteria->add(EventNotificationTemplatePeer::PARTNER_ID, array(Partner::SHARED_CONTENT_PARTNER_ID, $partnerId), Criteria::IN);
		else
			$criteria->add(EventNotificationTemplatePeer::PARTNER_ID, Partner::SHARED_CONTENT_PARTNER_ID);

		return EventNotificationTemplatePeer::doSelect($criteria, $con);
	}

} // EventNotificationTemplatePeer
