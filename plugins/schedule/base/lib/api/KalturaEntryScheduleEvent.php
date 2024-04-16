<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaEntryScheduleEvent extends KalturaScheduleEvent
{
	/**
	 * Entry to be used as template during content ingestion
	 * @var string
	 * @filter eq
	 */
	public $templateEntryId;

	/**
	 * Entries that associated with this event
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $entryIds;
	
	/**
	 * Categories that associated with this event
	 * @var string
	 * @filter like,mlikeor,mlikeand
	 */
	public $categoryIds;

	/**
	 * Blackout schedule events the conflict with this event
	 * @readonly
	 * @var KalturaScheduleEventArray
	 */
	public $blackoutConflicts;

	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array 
	 (	
		'templateEntryId',
		'entryIds',
		'categoryIds',
		'blackoutConflicts',
	 );
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function validate($startDate, $endDate)
	{
		$this->validateDates($startDate, $endDate);

		if ($this->templateEntryId && $this->recurrenceType === KalturaScheduleEventRecurrenceType::NONE)
		{
			$linkedByArray = !is_null($this->linkedBy) ? explode(',', $this->linkedBy) : array();
			$eventIdsToIgnore = array_merge(array($this->id) , array_filter($linkedByArray));
			$events = ScheduleEventPeer::retrieveOtherEvents($this->templateEntryId, $startDate, $endDate, $eventIdsToIgnore);
			if ($events)
			{
				throw new KalturaAPIException(KalturaScheduleErrors::SCHEDULE_TIME_IN_USE);
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate($sourceObject, $propertiesToSkip)
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		if (is_null($this->templateEntryId))
		{
			$this->templateEntryId = $sourceObject->getTemplateEntryId();
		}
		
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}