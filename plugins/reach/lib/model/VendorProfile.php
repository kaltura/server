<?php


/**
 * Skeleton subclass for representing a row from the 'vendor_profile' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.reach
 * @subpackage model
 */
class VendorProfile extends BaseVendorProfile 
{
	const CUSTOM_DATA_RULES_ARRAY_COMPRESSED = 				'rules_array_compressed';
	const CUSTOM_DATA_DICTIONARY_ARRAY_COMPRESSED = 		'dictionary_array_compressed';
	const CUSTOM_DATA_DEFAULT_OUTPUT_FORMAT = 				'default_output_format';
	const CUSTOM_DATA_DEFAULT_SOURCE_LANGUAGE = 			'default_source_language';
	
	const CUSTOM_DATA_AUTO_DISPLAY_MACHINE_ON_PLAYER = 		'auto_display_machine_captions_on_player';
	const CUSTOM_DATA_AUTO_DISPLAY_HUMAN_ON_PLAYER = 		'auto_display_human_captions_on_player';
	
	const CUSTOM_DATA_ENABLE_MACHINE_MODERATION =			'enable_maachine_moderation';
	const CUSTOM_DATA_ENABLE_HUMAN_MODERATION = 			'enable_human_moderation';
	
	const CUSTOM_DATA_ENABLE_METADATA_EXTRACT = 			'enable_metadata_extraction';
	const CUSTOM_DATA_ENABLE_SPEAKER_CHANGE_INDICATION = 	'enable_speaker_change_indication';
	const CUSTOM_DATA_ENABLE_SPEAKER_AUDIO_TAGS = 			'enable_audio_tags';
	const CUSTOM_DATA_ENABLE_POFANITY_REMOVAL = 			'enable_profanity_removal';
	const CUSTOM_DATA_MAX_CHARS_PER_LINE = 					'max_chars_per_line';
	const CUSTOM_DATA_VENDOR_CREDIT = 						'vendor_credit';
	
	const CUSTOM_DATA_CREDIT_USAGE_PERCENTAGE = 			'credit_usage_percentage';
	const CUSTOM_DATA_CONTENT_DELETION_POLICY = 			'content_deletion_policy';
	
	//setters
	
	public function setEnableMachineModeration($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENABLE_MACHINE_MODERATION, $v);
	}
	
	public function setEnableHumanModeration($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENABLE_HUMAN_MODERATION, $v);
	}
	
	public function setDefaultOutputFormat($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DEFAULT_OUTPUT_FORMAT, $v);
	}
	
	public function setDefaultSourceLanguage($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DEFAULT_SOURCE_LANGUAGE, $v);
	}
	
	public function setAutoDisplayMachineCaptionsOnPlayer($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_AUTO_DISPLAY_MACHINE_ON_PLAYER, $v);
	}
	
	public function setAutoDisplayHumanCaptionsOnPlayer($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_AUTO_DISPLAY_HUMAN_ON_PLAYER, $v);
	}
	
	public function setEnableMetadataExtraction($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENABLE_METADATA_EXTRACT, $v);
	}
	
	public function setEnableSpeakerChangeIndication($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENABLE_SPEAKER_CHANGE_INDICATION, $v);
	}
	
	public function setEnableAudioTags($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENABLE_SPEAKER_AUDIO_TAGS, $v);
	}
	
	public function setEnableProfanityRemoval($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENABLE_POFANITY_REMOVAL, $v);
	}
	
	public function setMaxCharactersPerCaptionLine($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_MAX_CHARS_PER_LINE, $v);
	}
	
	public function setRulesArrayCompressed($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_RULES_ARRAY_COMPRESSED, $v);
	}
	
	public function setRulesArray($rules)
	{
		$serializedRulesArray = serialize($rules);
		
		if(strlen($serializedRulesArray) > myCustomData::MAX_TEXT_FIELD_SIZE)
		{
			$this->setRulesArrayCompressed(true);
			$serializedRulesArray = gzcompress($serializedRulesArray);
			if(strlen(utf8_encode($serializedRulesArray)) > myCustomData::MAX_MEDIUM_TEXT_FIELD_SIZE)
				throw new kCoreException('Exceeded max size allowed for access control', kCoreException::EXCEEDED_MAX_CUSTOM_DATA_SIZE);
			
		}
		else
		{
			$this->setRulesArrayCompressed(false);
		}
		
		$this->setRules($serializedRulesArray);
	}

	public function setDictionariesArrayCompressed($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DICTIONARY_ARRAY_COMPRESSED, $v);
	}

	public function setDictionariesArray($dictionaries)
	{
		$serializedDictionariesArray = serialize($dictionaries);

		if(strlen($serializedDictionariesArray) > myCustomData::MAX_TEXT_FIELD_SIZE)
		{
			$this->setDictionariesArrayCompressed(true);
			$serializedDictionariesArray = gzcompress($serializedDictionariesArray);
			if(strlen(utf8_encode($serializedDictionariesArray)) > myCustomData::MAX_MEDIUM_TEXT_FIELD_SIZE)
				throw new kCoreException('Exceeded max size allowed for access control', kCoreException::EXCEEDED_MAX_CUSTOM_DATA_SIZE);

		}
		else
		{
			$this->setDictionariesArrayCompressed(false);
		}

		$this->setDictionary($serializedDictionariesArray);
	}

	public function getDictionariesArrayCompressed()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DICTIONARY_ARRAY_COMPRESSED, null, false);
	}

	/**
	 * @return array<kDictionary>
	 */
	public function getDictionariesArray()
	{
		$dictionaries = array();
		$dictionariesString = $this->getDictionary();
		if($dictionariesString )
		{
			try
			{
				if($this->getDictionariesArrayCompressed())
					$dictionariesString   = gzuncompress($dictionariesString  );

				$dictionaries = unserialize($dictionariesString );
			}
			catch(Exception $e)
			{
				KalturaLog::err("Unable to unserialize [$dictionariesString ], " . $e->getMessage());
				$dictionaries = array();
			}
		}
		return $dictionaries;
	}
	
	public function setCredit($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_VENDOR_CREDIT, serialize($v));
	}
	
	public function setCreditUsagePercentage($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_CREDIT_USAGE_PERCENTAGE, $v);
	}
	
	public function setContentDeletionPolicy($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_CONTENT_DELETION_POLICY, $v);
	}
	
	//getters
	
	public function getEnableMachineModeration()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENABLE_MACHINE_MODERATION,null, false);
	}
	
	public function getEnableHumanModeration()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENABLE_HUMAN_MODERATION,null, false);
	}
	
	public function getDefaultOutputFormat()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DEFAULT_OUTPUT_FORMAT ,null, VendorCatalogItemOutputFormat::SRT);
	}
	
	public function getDefaultSourceLanguage()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DEFAULT_SOURCE_LANGUAGE ,null, LanguageKey::ENG);
	}
	
	public function getAutoDisplayMachineCaptionsOnPlayer()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_AUTO_DISPLAY_MACHINE_ON_PLAYER, null, false);
	}
	
	public function getAutoDisplayHumanCaptionsOnPlayer()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_AUTO_DISPLAY_HUMAN_ON_PLAYER, null, false);
	}
	
	public function getEnableMetadataExtraction()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENABLE_METADATA_EXTRACT ,null, true);
	}
	
	public function getEnableSpeakerChangeIndication()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENABLE_SPEAKER_CHANGE_INDICATION ,null, false);
	}
	
	public function getEnableAudioTags()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENABLE_SPEAKER_AUDIO_TAGS ,null, false);
	}
	
	public function getEnableProfanityRemoval()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENABLE_POFANITY_REMOVAL ,null, true);
	}
	
	public function getMaxCharactersPerCaptionLine()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_MAX_CHARS_PER_LINE ,null, null);
	}
	
	public function getRulesArrayCompressed()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_RULES_ARRAY_COMPRESSED, null, false);
	}
	
	/**
	 * @return array<kRule>
	 */
	public function getRulesArray()
	{
		$rules = array();
		$rulesString = $this->getRules();
		if($rulesString)
		{
			try
			{
				if($this->getRulesArrayCompressed())
					$rulesString = gzuncompress($rulesString);
				
				$rules = unserialize($rulesString);
			}
			catch(Exception $e)
			{
				KalturaLog::err("Unable to unserialize [$rulesString], " . $e->getMessage());
				$rules = array();
			}
		}
		
		return $rules;
	}
	
	/**
	 * @return kVendorCredit
	 */
	public function getCredit()
	{
		$credit = $this->getFromCustomData(self::CUSTOM_DATA_VENDOR_CREDIT);
		
		if($credit)
			$credit = unserialize($credit);
		
		return $credit;
	}

	/**
	 * @param $language
	 * @return string
	 */
	public function getDictionaryByLanguage($language)
	{
		foreach ($this->getDictionariesArray() as $dictionary)
		{
			/* @var kDictionary $dictionary*/
			if ($dictionary->getLanguage() == $language)
			{
				return $dictionary;
			}
		}
		return null;
	}


	
	public function getCreditUsagePercentage()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CREDIT_USAGE_PERCENTAGE, null, 0);
	}
	
	public function getContentDeletionPolicy($v)
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CONTENT_DELETION_POLICY, null, VendorProfileContentDeletionPolicy::DO_NOTHING);
	}

	public function syncCredit()
	{
		$vendorProfileCredit = $this->getCredit();
		if ($vendorProfileCredit)
		{
			$syncedCredit = $vendorProfileCredit->syncCredit($this->getId());
			$this->setUsedCredit($syncedCredit);
		}
	}
	
	public function shouldModerate($type)
	{
		if($type == VendorServiceType::HUMAN)
			return $this->getEnableHumanModeration();
		
		if($type == VendorServiceType::MACHINE)
			return $this->getEnableMachineModeration();
		
		return false;
	}
	
	public function syncCreditPercentageUsage()
	{
		$currentCredit = $this->getCredit()->getCurrentCredit();
		$creditUsagePercentage = ($currentCredit == VendorProfileCreditValues::UNLIMITED_CREDIT) ? 0 : 100;
		
		if($currentCredit != 0 && $currentCredit != VendorProfileCreditValues::UNLIMITED_CREDIT)
		{
			$usedCredit = $this->getUsedCredit();
			$creditUsagePercentage = ($usedCredit/$currentCredit)*100;
		}
		
		$this->setCreditUsagePercentage($creditUsagePercentage);
		$this->save();
	}
	
} // VendorProfile
