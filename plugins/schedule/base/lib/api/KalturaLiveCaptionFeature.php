<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaLiveCaptionFeature extends KalturaLiveFeature
{
	/**
	 * @var string
	 */
	public $mediaUrl;

	/**
	 * @var string
	 */
	public $mediaKey;

	/**
	 * @var string
	 */
	public $captionUrl;

	/**
	 * @var string
	 */
	public $captionToken;

	/**
	 * @var string
	 */
	public $language;

	private static $map_between_objects = array(
		'mediaUrl',
		'mediaKey',
		'captionUrl',
		'captionToken',
		'language'
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(is_null($sourceObject))
		{
			$sourceObject = new LiveCaptionFeature();
		}

		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}