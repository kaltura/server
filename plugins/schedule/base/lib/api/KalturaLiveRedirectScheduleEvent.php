<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaLiveRedirectScheduleEvent extends KalturaEntryScheduleEvent
{
	/**
	 * The vod entry to redirect
	 * @var string
	 */
	public $redirectEntryId;
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject ($sourceObject = null, $propertiesToSkip = array())
	{
		if (is_null($sourceObject))
		{
			$sourceObject = new LiveRedirectScheduleEvent();
		}
		
		return parent ::toObject($sourceObject, $propertiesToSkip);
	}
	
	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'redirectEntryId',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects ()
	{
		return array_merge(parent ::getMapBetweenObjects(),
		                   self ::$map_between_objects);
	}
	
	/**
	 * {@inheritDoc}
	 * @see KalturaScheduleEvent::getScheduleEventType()
	 */
	public function getScheduleEventType ()
	{
		return ScheduleEventType::LIVE_REDIRECT;
	}
	
	/**
	 * @throws KalturaAPIException
	 */
	public function validateForInsert ($propertiesToSkip = array())
	{
		$this -> validateLiveStreamEventFields();
		parent ::validateForInsert($propertiesToSkip);
	}
	
	protected function getSingleScheduleEventMaxDuration()
	{
		return 60*60*24*365*5;//5 years
	}
	
	/**
	 * @throws KalturaAPIException
	 */
	public function validateForUpdate ($sourceObject, $propertiesToSkip = array())
	{
		$this -> validateLiveStreamEventFields();
		parent ::validateForUpdate($sourceObject, $propertiesToSkip = array());
	}
	
	/**
	 * @throws KalturaAPIException
	 */
	protected function validateLiveStreamEventFields ()
	{
		if($this -> redirectEntryId && !entryPeer ::retrieveByPK($this ->
			redirectEntryId))
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND,
			                              $this -> redirectEntryId);
		}
	}
	
}