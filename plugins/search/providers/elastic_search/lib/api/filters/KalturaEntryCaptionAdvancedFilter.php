<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.filters
 */
class KalturaEntryCaptionAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var KalturaNullableBoolean
	 */
	public $hasCaption;

	/**
	 * @var KalturaLanguage
	 */
	public $language;

	/**
	 * @var KalturaESearchRange
	 */
	public $accuracy;

	/**
	 * @var KalturaCaptionAssetUsage
	 */
	public $usage;

	private static $map_between_objects = array
	(
		"hasCaption", "language", "accuracy", "usage"
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		if(!$object_to_fill)
		{
			$object_to_fill = new kEntryCaptionAdvancedFilter();
		}

		$this->validateAccuracy();
		return parent::toObject($object_to_fill, $props_to_skip);
	}

	protected function validateAccuracy()
	{
		if (!isset($this->accuracy)) {
			return;
		}

		if ($this->accuracy->greaterThanOrEqual === null &&
			$this->accuracy->lessThanOrEqual === null &&
			$this->accuracy->greaterThan === null &&
			$this->accuracy->lessThan === null)
		{
			throw new KalturaAPIException(kESearchException::INVALID_CAPTION_ACCURACY_VALUES);
		}

		$rangeValues = array();
		if ($this->accuracy->greaterThanOrEqual !== null)
		{
			$rangeValues['greaterThanOrEqual'] = $this->accuracy->greaterThanOrEqual;
		}
		if ($this->accuracy->lessThanOrEqual !== null)
		{
			$rangeValues['lessThanOrEqual'] = $this->accuracy->lessThanOrEqual;
		}
		if ($this->accuracy->greaterThan !== null)
		{
			$rangeValues['greaterThan'] = $this->accuracy->greaterThan;
		}
		if ($this->accuracy->lessThan !== null)
		{
			$rangeValues['lessThan'] = $this->accuracy->lessThan;
		}

		foreach ($rangeValues as $field => $value)
		{
			if ($value < 0 || $value > 100)
			{
				throw new KalturaAPIException(kESearchException::INVALID_CAPTION_ACCURACY_VALUES_NOT_IN_RANG, $field);
			}
		}
	}

}
