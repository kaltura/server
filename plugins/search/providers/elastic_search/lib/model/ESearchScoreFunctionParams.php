<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class ESearchScoreFunctionParams extends BaseObject
{
	/**
	 * @var ESearchScoreFunctionType
	 */
	protected $scoreFunctionBoostType;

	/**
	 * @var ESearchScoreFunctionField
	 */
	protected $scoreFunctionBoostField;

	/**
	 * @var ESearchScoreFunctionMode
	 */
	protected $scoreFunctionBoostMode;

	/**
	 * @var float
	 */
	protected $weight;

	/**
	 * @var string
	 */
	protected $scale;

	/**
	 * @var float
	 */
	protected $decay;

	/**
	 * @var string
	 */
	protected $origin;


	/**
	 * @return ESearchScoreFunctionType
	 */
	public function getScoreFunctionBoostType()
	{
		return $this->scoreFunctionBoostType;
	}

	/**
	 * @param ESearchScoreFunctionType $scoreFunctionBoostFunction
	 */
	public function setScoreFunctionBoostType($scoreFunctionBoostType)
	{
		$this->scoreFunctionBoostType = $scoreFunctionBoostType;
	}

	/**
	 * @return ESearchScoreFunctionField
	 */
	public function getScoreFunctionBoostField()
	{
		return $this->scoreFunctionBoostField;
	}

	/**
	 * @param ESearchScoreFunctionField $scoreFunctionBoostField
	 */
	public function setScoreFunctionBoostField($scoreFunctionBoostField)
	{
		$this->scoreFunctionBoostField = $scoreFunctionBoostField;
	}

	/**
	 * @return ESearchScoreFunctionMode
	 */
	public function getScoreFunctionBoostMode()
	{
		return $this->scoreFunctionBoostMode;
	}

	/**
	 * @param ESearchScoreFunctionMode $scoreFunctionBoostMode
	 */
	public function setScoreFunctionBoostMode($scoreFunctionBoostMode)
	{
		$this->scoreFunctionBoostMode = $scoreFunctionBoostMode;
	}

	/**
	 * @param float $weight
	 */
	public function setWeight($weight)
	{
		$this->weight = $weight;
	}

	/**
	 * @return float
	 */
	public function getWeight()
	{
		return $this->weight;
	}

	/**
	 * @return string
	 */
	public function getScale()
	{
		return $this->scale;
	}

	/**
	 * @param string $scale
	 */
	public function setScale($scale)
	{
		$this->scale = $scale;
	}

	/**
	 * @return float
	 */
	public function getDecay()
	{
		return $this->decay;
	}

	/**
	 * @param float $decay
	 */
	public function setDecay($decay)
	{
		$this->decay = $decay;
	}

	/**
	 * @return string
	 */
	public function getOrigin()
	{
		return $this->origin;
	}

	/**
	 * @param string $origin
	 */
	public function setOrigin($origin)
	{
		$this->origin = $origin;
	}
}
