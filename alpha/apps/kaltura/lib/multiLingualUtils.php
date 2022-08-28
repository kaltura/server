<?php

class multiLingualUtils
{
	const MULTI = 'MULTI';
	const MULTI_LANGUAGE_MAPPING = 'multiLanguageMapping';
	
	public static function handleMultiLanguageInput($object, $field, &$originalValue, $update_db = true)
	{
		$multiLangMapping = json_decode($object->getMultiLanguageMapping(), true);
		$defaultValue = null;
		
		if (is_array($originalValue))
		{
			$defaultValue = self::removeDefaultLanguageFromMapping($originalValue, $object);
			self::mapValueByLanguageInMultiLangMapping($object, $multiLangMapping, $field, $originalValue);
			$object->setMultiLanguageMapping($multiLangMapping ? json_encode($multiLangMapping) : null);
		}
		
		$defaultValue = $defaultValue ? $defaultValue : $originalValue;
		
		return $object->alignFieldValue($field, $defaultValue, $update_db);
	}
	
	protected static function removeDefaultLanguageFromMapping(&$multiLangMapping, $object)
	{
		$entryDefaultLanguage = kCurrentContext::getLanguage() ? kCurrentContext::getLanguage() : array_keys($multiLangMapping)[0];
		$object->setObjectDefaultLanguage($entryDefaultLanguage);
		$result = $multiLangMapping[$entryDefaultLanguage];
		unset($multiLangMapping[$entryDefaultLanguage]);
		return $result;
	}
	
	public static function mapValueByLanguageInMultiLangMapping($object, &$multiLangMapping, $field, $value, $language = null)
	{
		foreach ($value as $languageKey => $languageValue)
		{
			if ($multiLangMapping[$field][$languageKey] != $languageValue)
			{
				$multiLangMapping[$field][$languageKey] = $languageValue;
				
			}
		}
		$object->setMultiLanguageMapping(json_encode($multiLangMapping, true));
	}
	
	public static function getMultiLanguageValue($object, $field, $value)
	{
		$multiLangMapping = json_decode($object->getMultiLanguageMapping(), true);
		if ($multiLangMapping)
		{
			$value = '';
			foreach ($multiLangMapping[$field] as $languageValue)
			{
				$value = $value ? implode(',', array($value, $languageValue)) : $languageValue;
			}
		}
		
		return $value;
	}
	
	public static function extractLanguageValue($multiLangMapping, $field, $language)
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
}