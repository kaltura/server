<?php


/**
 * Skeleton subclass for representing a row from the 'metadata_profile_field' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.metadata
 * @subpackage model
 */
class MetadataProfileField extends BaseMetadataProfileField implements IBaseObject 
{
	const STATUS_ACTIVE = 1;
	const STATUS_DEPRECATED = 2;
	const STATUS_NONE_SEARCHABLE = 3;
	
	const CUSTOM_DATA_FIELD_MATCH_TYPE = 'matchType';
	const CUSTOM_DATA_FIELD_TRIM_CHARS = 'trimChars';
	const CUSTOM_DATA_FIELD_EXPLODE_CHARS = 'explodeChars';
	
	public function getCacheInvalidationKeys()
	{
		return array("metadataProfileField:metadataProfileId=".strtolower($this->getMetadataProfileId()));
	}
	
	public function getMatchType()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_MATCH_TYPE, null, MetadataProfileFieldMatchType::TEXT);}
	public function getTrimChars()		{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_TRIM_CHARS, null, " .:");}
	public function getExplodeChars()	{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_EXPLODE_CHARS);}
	
	public function setMatchType($v)	{ $this->putInCustomData(self::CUSTOM_DATA_FIELD_MATCH_TYPE, $v);}
	public function setTrimChars($v)	{ $this->putInCustomData(self::CUSTOM_DATA_FIELD_TRIM_CHARS, $v);}
	public function setExplodeChars($v)	{ $this->putInCustomData(self::CUSTOM_DATA_FIELD_EXPLODE_CHARS, $v);}
	
	private function getTrimUniqueChars()
	{
		$explodeChars = $this->getExplodeChars();
		if(!$explodeChars)
			return $this->getTrimChars();
		
		
		$trimCharsArray = str_split($this->getTrimChars());
		$explodeCharsArray = str_split($this->getExplodeChars());
		$uniqueTrimChars = array_unique(array_diff($trimCharsArray, $explodeCharsArray));
		
		return implode($uniqueTrimChars);
	}
	
	public function getParsedFieldValue($value)
	{
		$parsedFieldValues = array();
		$trimChars = $this->getTrimUniqueChars();
		$value = $trimChars ? strtr($value, $trimChars, str_repeat("_", strlen($trimChars))) : $value;
		
		if($this->getExplodeChars())
		{
			$explodeChars = str_split($this->getExplodeChars());
			$explodePattern = implode("|", $explodeChars);
			$values = preg_split( "/($explodePattern)/", $value );
			
			foreach ($values as $value) 
			{
				if(trim($value))
					$parsedFieldValues[] = MetadataPlugin::PLUGIN_NAME . '_' . $this->getId() . '_' . $value;
			}
		}
		else
		{
			$parsedFieldValues[] = MetadataPlugin::PLUGIN_NAME . '_' . $this->getId() . '_' . $value;
		}
		
		return $parsedFieldValues;
	}
	
} // MetadataProfileField
