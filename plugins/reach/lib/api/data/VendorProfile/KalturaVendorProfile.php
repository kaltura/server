<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorProfile extends KalturaObject implements IRelatedFilterable
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,order
	 */
	public $id;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * @var KalturaVendorProfileStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;
	
	/**
	 * @var KalturaVendorProfileType
	 * @filter eq,in
	 */
	public $profileType;
	
	/**
	 * @var KalturaLanguage
	 */
	public $defaultSourceLanguage;
	
	/**
	 * @var KalturaVendorCatalogItemOutputFormat
	 */
	public $defaultOutputFormat;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableMachineModeration;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableHumanModeration;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $autoDisplayMachineCaptionsOnPlayer;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $autoDisplayHumanCaptionsOnPlayer;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableMetadataExtraction;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableSpeakerChangeIndication;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableAudioTags;
	
	/**
	 * @var KalturaNullableBoolean
	 */
	public $enableProfanityRemoval;
	
	/**
	 * @var int
	 */
	public $maxCharactersPerCaptionLine;
	
	/**
	 * @var KalturaVendorProfileRulesArray
	 */
	public $rules; //ToDo add object and sub classes

	/**
	 * @var KalturaVendorCredit
	 */
	public $credit;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $usedCredit;
	
	private static $map_between_objects = array
	(
		'id',
		'partnerId',
		'createdAt',
		'updatedAt',
		'status',
		'profileType' => 'type',
		'defaultSourceLanguage',
		'defaultOutputFormat',
		'enableMachineModeration',
		'enableHumanModeration',
		'autoDisplayMachineCaptionsOnPlayer',
		'autoDisplayHumanCaptionsOnPlayer',
		'enableMetadataExtraction',
		'enableSpeakerChangeIndication',
		'enableAudioTags',
		'enableProfanityRemoval',
		'maxCharactersPerCaptionLine',
		'rules' => 'rulesArray',
		'credit',
		'usedCredit',
	);
	
	/* (non-PHPdoc)
	 * @see KalturaCuePoint::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
 	 * @see KalturaObject::toInsertableObject()
 	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
			$object_to_fill = new VendorProfile();
		
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}
	
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validate();
		return parent::validateForInsert($propertiesToSkip);
	}
	
	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validate($sourceObject);
		return parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}
	
	private function validate(VendorProfile $sourceObject = null)
	{
		if(!$sourceObject) //Source object will be null on insert
			$this->validatePropertyNotNull("profileType");
		
		return;
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}

}