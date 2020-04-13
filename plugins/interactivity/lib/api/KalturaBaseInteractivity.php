<?php

/**
 * @package plugins.interactivity
 * @subpackage api.objects
 */
abstract class KalturaBaseInteractivity extends KalturaObject
{
	/**
	 *
	 * @var string
	 */
	public $data;

	/**
	 *
	 * @var int
	 */
	public $version;

	/**
	 * @readonly
	 * @var string
	 */
	public $entryId;

	/**
	 * Interactivity update date as Unix timestamp (In seconds)
	 * @readonly
	 * @var time
	 */
	public $updatedAt;

	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	protected static $map_between_objects = array
	(
		'entryId',
		'updatedAt',
		'version',
		'data',
	);

	protected function getMapBetweenObjects ( )
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	abstract protected function getFileSyncSubType();

	/**
	 * @param kBaseInteractivity $source_object
	 * @param KalturaDetachedResponseProfile|null $responseProfile
	 * @throws KalturaAPIException
	 * @throws PropelException
	 * @throws kCoreException
	 */
	public function doFromObject($source_object, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$fileSync = $this->getFileSync($source_object);
		$this->entryId = $source_object->getEntryId();
		$this->version = $fileSync->getVersion();
		$this->updatedAt = $fileSync->getUpdatedAt('U');
		$this->data = kFileSyncUtils::getContentsByFileSync($fileSync);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		parent::validateForInsert($propertiesToSkip = array());
		$this->validateData();
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		parent::validateForUpdate($propertiesToSkip);
		$this->validateVersion();
		$this->validateData();
	}

	/**
	 * @param kBaseInteractivity $source_object
	 * @return FileSync
	 * @throws KalturaAPIException
	 * @throws PropelException
	 * @throws kCoreException
	 */
	protected function getFileSync($source_object)
	{
		$syncKey = $source_object->getSyncKey($this->getFileSyncSubType());
		list($fileSync, $local) = kFileSyncUtils::getReadyFileSyncForKey($syncKey, true, false);
		/* @var $fileSync FileSync */
		if (!$fileSync)
		{
			throw new KalturaAPIException($this->getNoDataErrorMsg(), $source_object->getEntryId());
		}

		return $fileSync;
	}

	protected function validateVersion()
	{
		if(is_null($this->version))
		{
			throw new KalturaAPIException(KalturaInteractivityErrors::VERSION_IS_MANDATORY);
		}
	}

	protected function validateData()
	{
		$json = json_decode($this->data);
		if(!$json)
		{
			throw new KalturaAPIException(KalturaInteractivityErrors::DATA_IS_NOT_VALID_JSON);
		}
	}

	abstract protected function getNoDataErrorMsg();
}