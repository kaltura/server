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

	const FIELD_MAX_LENGTH 	= 256;

	public static function sanitizedFPutCsv($file, array $fields)
	{
		$sanitizedFields = self::validateCsvFields($fields);
		fputcsv($file, $sanitizedFields);
	}


	private static function validateCsvFields(array $fields)
	{
		foreach ($fields as &$csvField)
		{
			$csvField = self::handleInvalidChars($csvField);
			if(strlen($csvField) > self::FIELD_MAX_LENGTH)
				$csvField = substr($csvField,  self::FIELD_MAX_LENGTH);
		}

		return $fields;
	}

	public static function handleInvalidChars($csvField)
	{
		$formulaInjectionChars = array(self::CHAR_PLUS, self::CHAR_EQUALS, self::CHAR_HYPHEN, self::CHAR_AT, self::CHAR_SEMICOLON);

		$cleanCsvField = str_replace(array(self::CHAR_APOSTROPHE, self::CHAR_APOSTROPHES), '', $csvField);
		if(in_array(substr($cleanCsvField, 0, 1), $formulaInjectionChars))
		{
			KalturaLog::debug("CSV field starts with invalid char" );
			$csvField = self::CHAR_APOSTROPHE . $cleanCsvField;
		}

		return $csvField;
	}

}