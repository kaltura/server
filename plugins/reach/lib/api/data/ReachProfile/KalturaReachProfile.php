<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService ignore
 */
class KalturaReachProfile extends KalturaObject implements IRelatedFilterable
{
	const MAX_DICTIONARY_LENGTH = 5000;

	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,order
	 */
	public $id;

	/**
	 * The name of the profile
	 * @var string
	 */
	public $name;

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
	 * @var KalturaReachProfileStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;

	/**
	 * @var KalturaReachProfileType
	 * @filter eq,in
	 */
	public $profileType;

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
	 * @var string
	 */
	public $labelAdditionForMachineServiceType;

	/**
	 * @var string
	 */
	public $labelAdditionForHumanServiceType;

	/**
	 * @var KalturaReachProfileContentDeletionPolicy
	 */
	public $contentDeletionPolicy;

	/**
	 * @var KalturaRuleArray
	 */
	public $rules;

	/**
	 * @var KalturaBaseVendorCredit
	 * @requiresPermission update
	 */
	public $credit;

	/**
	 * @var float
	 * @readonly
	 */
	public $usedCredit;

	/**
	 * @var KalturaDictionaryArray
	 */
	public $dictionaries;

	/**
	 * Comma separated flavorParamsIds that the vendor should look for it matching asset when trying to download the asset
	 * @var string
	 */
	public $flavorParamsIds;

	/**
	 * Indicates in which region the task processing should task place
	 * @var KalturaVendorTaskProcessingRegion
	 */
	public $vendorTaskProcessingRegion;

	private static $map_between_objects = array
	(
		'id',
		'name',
		'partnerId',
		'createdAt',
		'updatedAt',
		'status',
		'profileType' => 'type',
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
		'labelAdditionForMachineServiceType',
		'labelAdditionForHumanServiceType',
		'contentDeletionPolicy',
		'rules' => 'rulesArray',
		'credit',
		'usedCredit',
		'dictionaries' => 'dictionariesArray',
		'flavorParamsIds',
		'vendorTaskProcessingRegion'
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
			$object_to_fill = new ReachProfile();

		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull("profileType");
		$this->validatePropertyNotNull("credit");
		$this->credit->validateForInsert();

		$rules = $this->rules ? $this->rules : array();
		foreach($rules as $rule)
		{
			$rule->validateForInsert($propertiesToSkip);
		}

		//validating dictionary duplications
		$this->validateDictionary();

		return parent::validateForInsert($propertiesToSkip);
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		/** @var $sourceObject ReachProfile */
		//validating dictionary duplications
		$this->validateDictionary();
		if (isset($this->credit))
		{
			$this->credit->validateForUpdate($sourceObject->getCredit());
		}

		$rules = $this->rules ? $this->rules : array();
		foreach($rules as $rule)
		{
			$rule->validateForUpdate($rule->toObject(), $propertiesToSkip);
		}

		return parent::validateForUpdate($sourceObject, array('credit'));
	}

	private function validateDictionaryLength($data)
	{
		return strlen($data) <= self::MAX_DICTIONARY_LENGTH ? true : false;
	}

	public function getExtraFilters()
	{
		return array();
	}

	public function getFilterDocs()
	{
		return array();
	}


	/* (non-PHPdoc)
	 * @see KalturaObject::fromObject()
	 */
	public function doFromObject($dbObject, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $dbObject ReachProfile */
		parent::doFromObject($dbObject, $responseProfile);

		if ($this->shouldGet('credit', $responseProfile) && !is_null($dbObject->getCredit()))
		{
			$this->credit = KalturaBaseVendorCredit::getInstance($dbObject->getCredit(), $responseProfile);
		}
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		$sourceCredit = null;
		$newCredit = null;
		if ($dbObject)
		{
			$sourceCredit = $dbObject->getCredit();
		}
		else
		{
			$dbObject = new ReachProfile();
		}

		$dbObject = parent::toObject($dbObject, $skip);
		if (isset($this->credit))
		{
			if ($this->credit->isMatchingCoreClass($sourceCredit))
			{
				$newCredit = $this->credit->toObject($sourceCredit);
			}
			else
			{
				$newCredit = $this->credit->toObject(null);
				/** @var kVendorCredit $newCredit */
				$newCredit->setInnerParams($sourceCredit);
			}
			$dbObject->setCredit($newCredit);
		}
		return $dbObject;
	}

	private function validateDictionary()
	{
		if(!$this->dictionaries)
			return;

		$languages = array();
		foreach ($this->dictionaries as $dictionary)
		{
			/* @var KalturaDictionary $dictionary */
			if (in_array($dictionary->language, $languages))
				throw new KalturaAPIException(KalturaReachErrors::DICTIONARY_LANGUAGE_DUPLICATION, $dictionary->language);

			if (!$this->validateDictionaryLength($dictionary->data))
				throw new KalturaAPIException(KalturaReachErrors::MAX_DICTIONARY_LENGTH_EXCEEDED, $dictionary->language, self::MAX_DICTIONARY_LENGTH);

			$languages[] = $dictionary->language;
		}
	}
}
