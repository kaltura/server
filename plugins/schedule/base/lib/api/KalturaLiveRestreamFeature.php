<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaLiveRestreamFeature extends KalturaLiveFeature
{
	/**
	 * @var string
	 */
	public $primaryUrl;

	/**
	 * @var string
	 */
	public $secondaryUrl;

    /**
     * @var string
     */
    public $playbackUrl;

	/**
	 * @var string
	 */
	public $streamKey;

	private static $map_between_objects = array(
		'primaryUrl',
		'secondaryUrl',
        'playbackUrl',
		'streamKey'
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
			$sourceObject = new LiveRestreamFeature();
		}

		return parent::toObject($sourceObject, $propertiesToSkip);
	}
}