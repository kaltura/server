<?php
/**
 * @package plugins.virtualEvent
 * @relatedService ScheduleEventService
 * @subpackage model.filters.base
 * @abstract
 */
class VirtualScheduleEventFilter extends ScheduleEventFilter
{
	public function init()
	{
		$this->fields = kArray::makeAssociativeDefaultValue(array(
			'_eq_virtual_event_id',
			'_in_virtual_event_id',
			'_notin_virtual_event_id',
			'_eq_virtual_schedule_event_sub_type'.
			'_in_virtual_schedule_event_sub_type',
			'_notin_virtual_schedule_event_sub_type',
		), null);
		
		$this->allowed_order_fields = array(
		);
	}
}