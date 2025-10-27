<?php

use enum\KalturaESearchScoreFunctionType;

/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class KalturaESearchScoreFunctionParams extends KalturaObject
{
	/**
	 * @var KalturaESearchScoreFunctionType
	 */
	public $scoreFunctionBoostType;  //exp

	/**
	 * @var KalturaESearchScoreFunctionField
	 */
	public $scoreFunctionBoostField;

	/**
	 * @var KalturaESearchScoreFunctionMode
	 */
	public $scoreFunctionBoostMode; //multiply, sum

	/**
	 * @var float
	 */
	public $weight;

	/**
	 * @var string
	 */
	public $scale;

	/**
	 * @var float
	 */
	public $decay;

	/**
	 * @var string
	 */
	public $origin;

	protected static $mapBetweenObjects = array
	(
		"scoreFunctionBoostType", "scoreFunctionBoostField", "scoreFunctionBoostMode", "weight", "scale", "decay", "origin"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchScoreFunctionParams();
		}

		self::validateParams($this->decay, $this->origin, $this->scale, $this->scoreFunctionBoostField, $this->scoreFunctionBoostType, $this->scoreFunctionBoostMode, $this->weight);

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	protected static function validateParams($decay, $origin, $scale, $scoreFunctionBoostField, $scoreFunctionBoostType, $scoreFunctionBoostMode, $weight)
	{
		if (!$decay || $decay < 0 || $decay > 1)
		{
			throw new KalturaAPIException(KalturaESearchErrors::INVALID_DECAY_VALUE_IN_BOOST_SCORE_FUNCTION);
		}
		//TODO validate values with util function
		if (!$origin || !elasticSearchUtils::isValidUTCDateOrDuration($origin))
		{
			throw new KalturaAPIException(KalturaESearchErrors::INVALID_ORIGIN_VALUE_IN_BOOST_SCORE_FUNCTION);
		}

		if (!$scale || !elasticSearchUtils::isValidUTCDateOrDuration($scale))
		{
			throw new KalturaAPIException(KalturaESearchErrors::INVALID_SCALE_VALUE_IN_BOOST_SCORE_FUNCTION);
		}

		if (!$weight)
		{
			throw new KalturaAPIException(KalturaESearchErrors::INVALID_WEIGHT_VALUE_IN_BOOST_SCORE_FUNCTION);
		}

		if (!$scoreFunctionBoostField)
		{
			throw new KalturaAPIException(KalturaESearchErrors::INVALID_FIELD_VALUE_IN_BOOST_SCORE_FUNCTION);
		}

		if (!$scoreFunctionBoostType)
		{
			throw new KalturaAPIException(KalturaESearchErrors::INVALID_FUNCTION_VALUE_IN_BOOST_SCORE_FUNCTION);
		}

		if (!$scoreFunctionBoostMode)
		{
			throw new KalturaAPIException(KalturaESearchErrors::INVALID_FUNCTION_MODE_VALUE_IN_BOOST_SCORE_FUNCTION);
		}
	}
}