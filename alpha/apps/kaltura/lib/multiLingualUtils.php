<?php

class multiLingualUtils
{
	const MULTI = 'MULTI';
	const MULTI_LINGUAL = 'multiLingual';
	const MULTI_LANGUAGE_MAPPING = 'multiLanguageMapping';
	const DEFAULT_LANGUAGE = 'default_language';
	
	/**
	 * Returns the value that should be set tin the relevant field in the data base, remove it from the call's input mapping and set the mapping
	 * in the object
	 * If the use case is that no value should be set in the db field, the function returns null
	 * @return string if the received mapping contains a value in the default language, return it. if no return null.
	 * null can be returned if a value in the mapping needs to be updated but the default value should be kept as is
	 * @param IMultiLingual $object multilingual supported object
	 * @param string $field multilingual supported field
	 * @param array $newValue new mapping received in the api call
	 * @throws KalturaAPIException when handling multi language input, the context language must be MULTI
	 */
	public static function handleMultiLanguageInput(&$object, $field, &$newValue)
	{
		$multiLangMapping = json_decode(self::getMultiLanguageMapping($object), true);
		$defaultValue = null;
		$defaultLanguage = null;
		$contextLanguage = kCurrentContext::getLanguage();
		
		self::getDefaultLangFromNewMapping($object, $defaultLanguage, $newValue, $contextLanguage, $multiLangMapping);
		
		if (is_array($newValue))
		{
			$defaultValue = self::getDefaultValueFromNewMapping($newValue, $field, $defaultLanguage, $multiLangMapping, $object);
			self::addFieldMappingToMultiLangMapping($multiLangMapping, $field, $newValue, $object);
			self::setMultiLanguageMapping($object, $multiLangMapping ? json_encode($multiLangMapping) : null);
		}
		
		$defaultValue = $defaultValue ? $defaultValue : null;
		
		return $object->alignFieldValue($field, $defaultValue);
	}
	
	protected static function getDefaultLangFromNewMapping(&$object, &$defaultLanguage, $newValue, $contextLanguage, $multiLangMapping)
	{
		$languageOfFirstItem = array_keys($newValue)[0];
		if (!$contextLanguage || $contextLanguage === self::MULTI)
		{
			if($languageOfFirstItem)
			{
				$defaultLanguage = $languageOfFirstItem;
				self::setDefaultLanguage($object, $defaultLanguage);
			}
			else
			{
				$defaultLanguage = null;
			}
		}
		else
		{
			if(!empty($languageOfFirstItem))
			{
				throw new KalturaAPIException(KalturaErrors::INVALID_FIELD_VALUE, "Language must be set to MULTI when adding MultiLingualString");
			}
			$defaultLanguage = $contextLanguage;
			if (!self::getDefaultLanguage($object) || !$multiLangMapping)
			{
				self::setDefaultLanguage($object, $defaultLanguage);
			}
		}
	}
	
	//Returns the default value from the mapping and removes it to eliminate duplicity
	protected static function getDefaultValueFromNewMapping(&$multiLangMapping, $field, $defaultLanguage, $objectMultiLangMapping, $object)
	{
		$value = $multiLangMapping[$defaultLanguage];
		unset($multiLangMapping[$defaultLanguage]);
		//result can be null if there is no default in the given mapping or mapping of single string
		if(!$value)
		{
			if (!$objectMultiLangMapping[$field] || kCurrentContext::$language === self::getDefaultLanguage($object))
			{
				return $multiLangMapping[''];
			}
			return null;
		}
		
		return $value;
	}
	
	public static function addFieldMappingToMultiLangMapping(&$multiLangMapping, $field, $value, $object)
	{
		if (empty(array_keys($value)[0]) && !empty($value))
		{
			if($multiLangMapping && kCurrentContext::$language !== self::getDefaultLanguage($object))
			{
				$multiLangMapping[$field][kCurrentContext::$language] = $value[""];
			}
			return;
		}
		foreach ($value as $languageKey => $languageValue)
		{
			if ($multiLangMapping[$field][$languageKey] != $languageValue)
			{
				$multiLangMapping[$field][$languageKey] = $languageValue;
			}
		}
	}
	
	public static function getFieldValueByLanguage($multiLangMapping, $field, $language)
	{
		foreach ($multiLangMapping[$field] as $languageKey => $languageValue)
		{
			if ($language === $languageKey)
			{
				return $languageValue;
			}
		}
		return null;
	}
	
	public static function getDefaultLanguage($object)
	{
		return $object->getFromCustomData(self::DEFAULT_LANGUAGE, null, null);
	}
	
	public static function setDefaultLanguage(&$object, $value)
	{
		$object->putInCustomData(self::DEFAULT_LANGUAGE, $value);
	}
	
	public static function getMultiLanguageMapping($object)
	{
		return $object->getFromCustomData(self::MULTI_LANGUAGE_MAPPING, null, null);
	}
	
	public static function setMultiLanguageMapping(&$object, $value)
	{
		$object->putInCustomData(self::MULTI_LANGUAGE_MAPPING, $value);
	}
	
	public static function setCorrectLanguageValuesInResponse(&$kObject, $sourceObject, $requestLanguage = null)
	{
		$multiLanguageMap = json_decode(self::getMultiLanguageMapping($sourceObject), true);
		if ($requestLanguage == self::MULTI)
		{
			
			self::setMultiLanguageStringInField($kObject, $sourceObject, $multiLanguageMap);
		}
		else
		{
			self::setRequestedLanguageStringInField($kObject, $multiLanguageMap, $sourceObject, $requestLanguage);
		}
	}
	
	protected static function setMultiLanguageStringInField(&$kObject, $sourceObject, $multiLanguageMap)
	{
		if($multiLanguageMap)
		{
			$defaultLanguage = self::getDefaultLanguage($sourceObject);
			$supportedFields = $sourceObject->getMultiLingualSupportedFields();
			foreach ($supportedFields as $fieldName)
			{
				if ($kObject->$fieldName)
				{
					$multiLanguageMap[$fieldName][$defaultLanguage] = $sourceObject->getDefaultFieldValue($fieldName);
				}
				$kObject->$fieldName = KalturaKeyValueArray::fromKeyValueArray($multiLanguageMap[$fieldName]);
			}
		}
	}
	
	protected static function setRequestedLanguageStringInField(&$kObject, $multiLangMapping, $sourceObject, $requestLanguage = null)
	{
		$language = $requestLanguage ? $requestLanguage : self::getDefaultLanguage($sourceObject);
		$supportedFields = $sourceObject->getMultiLingualSupportedFields();
		$supportedFieldsInRequestedLang = array();
		foreach ($supportedFields as $fieldName)
		{
			$supportedFieldsInRequestedLang[$fieldName] = $sourceObject->alignFieldValue($fieldName, self::getFieldValueByLanguage($multiLangMapping, $fieldName, $language));
			
			$kObject->$fieldName = ($supportedFieldsInRequestedLang[$fieldName] && $supportedFieldsInRequestedLang[$fieldName] !== '') ?
				$supportedFieldsInRequestedLang[$fieldName] : $sourceObject->getDefaultFieldValue($fieldName);
		}
	}
	
	/**
	 * @param $object
	 * @param &$params
	 * @return bool
	 * If needed this function fixes the params and returns if they should be re deserialized or not
	 */
	public static function shouldResetParamsAndDeserialize($object, &$params)
	{
		$skipDeserializer = true;
		$supportedFields = $object::getMultiLingualSupportedFields();
		foreach ($params as $key => $param)
		{
			foreach ($supportedFields as $fieldName)
			{
				if (isset($param[self::MULTI_LINGUAL . '_' . $fieldName]))
				{
					$params[$key][$fieldName] = $param[self::MULTI_LINGUAL . '_' . $fieldName];
					$skipDeserializer = false;
				}
			}
		}
		return $skipDeserializer;
	}
}