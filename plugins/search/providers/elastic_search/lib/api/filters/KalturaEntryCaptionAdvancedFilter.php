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
	 * @var int
	 */
	public $accuracyGreaterThanOrEqual;

	/**
	 * @var int
	 */
	public $accuracyLessThanOrEqual;

	/**
	 * @var int
	 */
	public $accuracyGreaterThan;

	/**
	 * @var int
	 */
	public $accuracyLessThan;

	/**
	 * @var KalturaCaptionAssetUsage
	 */
	public $usage;

	private static $map_between_objects = array
	(
		"hasCaption", "language", "accuracyGreaterThanOrEqual", "accuracyLessThanOrEqual", "accuracyGreaterThan", "accuracyLessThan", "usage"
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
		$accuracyFields = array(
			'accuracyGreaterThanOrEqual' => $this->accuracyGreaterThanOrEqual,
			'accuracyLessThanOrEqual' => $this->accuracyLessThanOrEqual,
			'accuracyGreaterThan' => $this->accuracyGreaterThan,
			'accuracyLessThan' => $this->accuracyLessThan
		);

		$nonNullValues = array();
		foreach ($accuracyFields as $fieldName => $value)
		{
			if ($value !== null)
			{
				$nonNullValues[$fieldName] = $value;
			}
		}

		if (empty($nonNullValues))
		{
			return;
		}

		foreach ($nonNullValues as $fieldName => $value)
		{
			if ($value < 0 || $value > 100)
			{
				throw new KalturaAPIException(kESearchException::RANGE_VALUE_FOR_ACCURACY_IS_ILLEGAL);
			}
		}

		$this->validateRangeConsistency();
	}

	private function validateRangeConsistency()
	{
		if ($this->accuracyGreaterThanOrEqual !== null && $this->accuracyLessThanOrEqual !== null &&
			$this->accuracyGreaterThanOrEqual > $this->accuracyLessThanOrEqual)
		{
			throw new KalturaAPIException(kESearchException::RANGE_VALUE_FOR_ACCURACY_IS_ILLEGAL);
		}

		if ($this->accuracyGreaterThan !== null && $this->accuracyLessThan !== null &&
			$this->accuracyGreaterThan >= $this->accuracyLessThan)
		{
			throw new KalturaAPIException(kESearchException::RANGE_VALUE_FOR_ACCURACY_IS_ILLEGAL);
		}

		if ($this->accuracyGreaterThan !== null && $this->accuracyLessThanOrEqual !== null &&
			$this->accuracyGreaterThan >= $this->accuracyLessThanOrEqual)
		{
			throw new KalturaAPIException(kESearchException::RANGE_VALUE_FOR_ACCURACY_IS_ILLEGAL);
		}

		if ($this->accuracyGreaterThanOrEqual !== null && $this->accuracyLessThan !== null &&
			$this->accuracyGreaterThanOrEqual >= $this->accuracyLessThan)
		{
			throw new KalturaAPIException(kESearchException::RANGE_VALUE_FOR_ACCURACY_IS_ILLEGAL);
		}
	}
}
