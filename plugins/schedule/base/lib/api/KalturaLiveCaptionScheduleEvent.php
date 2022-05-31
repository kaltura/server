<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaLiveCaptionScheduleEvent extends KalturaBaseLiveScheduleEvent
{
	/**
	 * TODO
	 * @var string
	 */
	public $mediaStreamUrl;

	/**
	 * TODO
	 * @var string
	 */
	public $captionStreamUrl;

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(is_null($sourceObject))
		{
			$sourceObject = new LiveCaptionScheduleEvent();
		}

		return parent::toObject($sourceObject, $propertiesToSkip);
	}

	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'mediaStreamUrl',
		'captionStreamUrl',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(),self::$map_between_objects);
	}

	/**
	 * {@inheritDoc}
	 * @see KalturaScheduleEvent::getScheduleEventType()
	 */
	public function getScheduleEventType()
	{
		return ScheduleEventType::LIVE_CAPTION;
	}
}
