<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class KalturaESearchScoreFunctionParams extends KalturaObject
{
	/**
	 * @var KalturaESearchScoreFunctionDecayAlgorithm
	 */
	public $decayAlgorithm;

	/**
	 * @var KalturaESearchScoreFunctionField
	 */
	public $functionField;

	/**
	 * @var KalturaESearchScoreFunctionBoostMode
	 */
	public $boostMode;

	/**
	 * @var KalturaESearchScoreFunctionOrigin
	 */
	public $origin;

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

	protected static $mapBetweenObjects = array
	(
		"decayAlgorithm", "functionField", "boostMode", "origin", "weight", "scale", "decay"
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}

	public function toObject($object_to_fill = null, $props_to_skip = array())
	{
		$this->validateScoreFunctionParams();

		if (!$object_to_fill)
		{
			$object_to_fill = new ESearchScoreFunctionParams();
		}

		return parent::toObject($object_to_fill, $props_to_skip);
	}

	public function validateScoreFunctionParams()
	{
		if (!isset($this->decayAlgorithm) || !isset($this->scale) || !isset($this->functionField))
		{
			throw new KalturaAPIException(KalturaESearchErrors::MISSING_MANDATORY_SCORE_FUNCTION_PARAM);
		}

		if (!elasticSearchUtils::isValidDuration($this->scale))
		{
			throw new KalturaAPIException(KalturaESearchErrors::INVALID_SCORE_FUNCTION_FIELD_VALUE, 'scale');
		}

		if (isset($this->decay) && (!is_float($this->decay) || $this->decay <= 0 || $this->decay >= 1))
		{
			throw new KalturaAPIException(KalturaESearchErrors::INVALID_SCORE_FUNCTION_FIELD_VALUE, 'decay');
		}

		if (isset($this->weight) && (!is_float($this->weight) && !is_numeric($this->weight)))
		{
			throw new KalturaAPIException(KalturaESearchErrors::INVALID_SCORE_FUNCTION_FIELD_VALUE, 'weight');
		}
	}
}
