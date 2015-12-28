<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaEntryScheduleEvent extends KalturaScheduleEvent
{
	/**
	 * Entry id to be used
	 * @var string
	 */
	public $entryId;
	
	/**
	 * Entry template to be used to create a new entry
	 * @var KalturaBaseEntry
	 */
	public $entryTemplate;
	
	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)  
	 */
	private static $map_between_objects = array 
	 (	
	 	'entryId',
		'entryTemplate',
	 );
		 
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert($propertiesToSkip)
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$notNulls = 0;
		
		if(!$this->isNull('entryId'))
		{
			$notNulls++;
		}
		
		if(!$this->isNull('entryTemplate'))
		{
			$notNulls++;
		}
		
		if($notNulls > 1)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_ALL_MUST_BE_NULL_BUT_ONE, 'entryId / entryTemplate');
		}
		
		parent::validateForInsert($propertiesToSkip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::validateForUpdate($sourceObject, $propertiesToSkip)
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		/* @var $sourceObject EntryScheduleEvent */
		$notNulls = 0;
		
		if(!$this->isNull('entryId') || $sourceObject->getEntryId())
		{
			$notNulls++;
		}
		
		if(!$this->isNull('entryTemplate') || $sourceObject->getEntry())
		{
			$notNulls++;
		}
		
		if($notNulls > 1)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_ALL_MUST_BE_NULL_BUT_ONE, 'entryId / entryTemplate');
		}
		
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}