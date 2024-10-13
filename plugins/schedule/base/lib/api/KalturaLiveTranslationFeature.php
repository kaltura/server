<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaLiveTranslationFeature extends KalturaLiveFeature
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
	 * 3 letter code
	 */
	public $language;

	private static $map_between_objects = array(
		'mediaUrl',
		'mediaKey',
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
			$sourceObject = new LiveTranslationFeature();
		}

		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}