<?php
/**
 * @package plugins.schedule
 * @subpackage model.filters
 */
class ScheduleEventResourceFilter extends baseObjectFilter
{
	public function init ()
	{
		$this->fields = kArray::makeAssociativeDefaultValue ( array (
				"_eq_event_id",
				"_in_event_id",
				"_eq_resource_id",
				"_in_resource_id",
				"_eq_entry_id",
				"_in_entry_id",
				"_gte_created_at",
				"_lte_created_at",
				"_gte_updated_at",
				"_lte_updated_at",
				"_eq_event_id_or_parent",
			) , NULL );

		$this->allowed_order_fields = array (
			"created_at", 
			"updated_at", 
		);
			
		$this->aliases = array ( 
		);
	}

	public function describe() 
	{
		return 
			array (
				"display_name" => "ScheduleEventResource",
				"desc" => ""
			);
	}
	
	public function getFieldNameFromPeer($field_name)
	{
		return ScheduleEventResourcePeer::translateFieldName($field_name, $this->field_name_translation_type, BasePeer::TYPE_COLNAME);
	}
	
	public function getIdFromPeer()
	{
		return ScheduleEventResourcePeer::ID;
	}
	
	/**
	 * {@inheritDoc}
	 * @see baseObjectFilter::attachToFinalCriteria()
	 */
	public function attachToFinalCriteria(Criteria $criteria)
	{
		if($this->is_set('_eq_event_id_or_parent'))
		{
			$scheduleEventResources = ScheduleEventResourcePeer::retrieveByEventId($this->get('_eq_event_id_or_parent'));
			if(!is_null($scheduleEventResources) && count($scheduleEventResources))
			{
				$scheduleEventResourceIds = array();
				foreach($scheduleEventResources as $scheduleEventResource)
				{
					/* @var $scheduleEventResource ScheduleEventResource */
					$scheduleEventResourceIds[] = $scheduleEventResource->getId();
				}
				$criteria->add(ScheduleEventResourcePeer::ID, $scheduleEventResourceIds, Criteria::IN);
			}
			else
			{
				$scheduleEvent = ScheduleEventPeer::retrieveByPK($this->get('_eq_event_id_or_parent'));
				if(!is_null($scheduleEvent))
				{
					if($scheduleEvent->getParentId())
						$criteria->add(ScheduleEventResourcePeer::EVENT_ID, $scheduleEvent->getParentId(), Criteria::EQUAL);
					else
						$criteria->add(ScheduleEventResourcePeer::EVENT_ID, $scheduleEvent->getId(), Criteria::EQUAL);
				}
			}
			$this->unsetByName('_eq_event_id_or_parent');
		}
		return parent::attachToFinalCriteria($criteria);
	}
}

