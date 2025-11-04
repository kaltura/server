<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class KalturaESearchScoreFunctionParams extends KalturaObject
{
	/**
	 * @var KalturaESearchScoreFunctionType
	 */
	public $scoreFunctionBoostType = KalturaESearchScoreFunctionType::EXP;

	/**
	 * @var KalturaESearchScoreFunctionField
	 */
	public $scoreFunctionBoostField = KalturaESearchScoreFunctionField::CREATED_AT;

	/**
	 * @var KalturaESearchScoreFunctionMode
	 */
	public $scoreFunctionBoostMode = KalturaESearchScoreFunctionMode::MULTIPLY;

	/**
	 * @var float
	 */
	public $weight;

	/**
	 * @var string
	 */
	public $scale = '30d';

	/**
	 * @var float
	 */
	public $decay;

	/**
	 * @var string
	 */
	public $origin = 'now';

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

		return parent::toObject($object_to_fill, $props_to_skip);
	}
}