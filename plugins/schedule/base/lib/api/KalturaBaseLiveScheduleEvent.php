<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 * @abstract
 */
abstract class KalturaBaseLiveScheduleEvent extends KalturaEntryScheduleEvent
{
	
	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'sourceEntryId',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	
	/**
	 * @throws KalturaAPIException
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validateLiveStreamEventFields();
		parent::validateForInsert($propertiesToSkip);
	}
	
	/**
	 * @throws KalturaAPIException
	 */
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateLiveStreamEventFields();
		parent::validateForUpdate($sourceObject, $propertiesToSkip = array());
	}
	
	/**
	 * @throws KalturaAPIException
	 */
	protected function validateLiveStreamEventFields()
	{
	}
}