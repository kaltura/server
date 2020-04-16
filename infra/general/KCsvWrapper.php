<?php

/**
 * A wrapper for fputcsv command - will make sure that the values inserted to the csv file are not formulas.
 *
 */
class KCsvWrapper
{
	const CHAR_PLUS 	= '+';
	const CHAR_EQUALS 	= '=';
	const CHAR_HYPHEN 	= '-';
	const CHAR_AT 		= '@';
	const CHAR_SEMICOLON 	= ',';
	const CHAR_APOSTROPHE 	= "'";
	const CHAR_APOSTROPHES 	= '"';

	const FIELD_MAX_PRINT_LENGTH = 64;

	public static function sanitizedFputCsv($file, array $fields)
	{
		$sanitizedFields = self::validateCsvFields($fields);
		fputcsv($file, $sanitizedFields);
	}

	public static function validateCsvFields(array $fields)
	{
		foreach ($fields as &$csvField)
		{
			$csvField = self::handleInvalidChars($csvField);
		}
		return $fields;
	}

	public static function handleInvalidChars($csvField)
	{
		$formulaInjectionChars = array(self::CHAR_PLUS, self::CHAR_EQUALS, self::CHAR_HYPHEN, self::CHAR_AT, self::CHAR_SEMICOLON);
		$cleanCsvField = str_replace(array(self::CHAR_APOSTROPHE, self::CHAR_APOSTROPHES), '', $csvField);
		$cleanCsvField = trim($cleanCsvField);

		/* ensure that no cells begin with any of the following characters: =, +, -, @
		 if so we are adding apostrophe (‘) in the beginning of the cell containing such characters
		 Adding apostrophe (‘) tells excel that the cell doesn’t contain formula so it won't run automatically
		*/
		if(in_array(substr($cleanCsvField, 0, 1), $formulaInjectionChars))
		{
			$csvField = self::CHAR_APOSTROPHE . $cleanCsvField;
			$trimmedValue = substr($csvField, 0, self::FIELD_MAX_PRINT_LENGTH);
			//KalturaLog::debug("CSV field starts with invalid char. Field value: " . $trimmedValue);
		}

		return $csvField;
	}

	public static function contains($needle, $str)
	{
		$valArray = explode(',', $str);
		$lowerValArray = array_map('strtolower', $valArray);
		if(in_array($needle, $lowerValArray))
		{
			return true;
		}
		return false;
	}

}
