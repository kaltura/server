<?php
/**
 * @package plugins.bumper
 * @subpackage api.objects
 * @relatedService BumperService
 */
class KalturaBumper extends KalturaObject
{
	/**
	 * @var string
	 */
	public $entryId;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var KalturaPlaybackSourceArray
	 * @readonly
	 */
	public $sources;

	private static $map_between_objects = array
	(
		'entryId',
		'url',
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new kBumper();
		}

		return parent::toObject($dbObject, $skip);
	}

	protected function validateEntryId()
	{
		$dbEntry = entryPeer::retrieveByPK($this->entryId);
		if (!$dbEntry)
		{
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryId);
		}
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validateEntryId();
		return parent::validateForInsert($propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateEntryId();
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
}
