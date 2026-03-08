<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model
 */
class ESearchScoreFunctionParams extends BaseObject
{
	/**
	 * @var ESearchScoreFunctionDecayAlgorithm
	 */
	protected $decayAlgorithm;

	/**
	 * @var ESearchScoreFunctionField
	 */
	protected $functionField;

	/**
	 * @var ESearchScoreFunctionBoostMode
	 */
	protected $boostMode;

	/**
	 * @var ESearchScoreFunctionOrigin
	 */
	protected $origin;

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
	 * @return ESearchScoreFunctionDecayAlgorithm
	 */
	public function getDecayAlgorithm()
	{
		return $this->decayAlgorithm;
	}

	/**
	 * @param ESearchScoreFunctionDecayAlgorithm $scoreFunctionBoostFunction
	 */
	public function setDecayAlgorithm($decayAlgorithm)
	{
		$this->decayAlgorithm = $decayAlgorithm;
	}

	/**
	 * @return ESearchScoreFunctionField
	 */
	public function getFunctionField()
	{
		return $this->functionField;
	}

	/**
	 * @param ESearchScoreFunctionField $functionField
	 */
	public function setFunctionField($functionField)
	{
		$this->functionField = $functionField;
	}

	/**
	 * @return ESearchScoreFunctionBoostMode
	 */
	public function getBoostMode()
	{
		return $this->boostMode;
	}

	/**
	 * @param ESearchScoreFunctionBoostMode $boostMode
	 */
	public function setBoostMode($boostMode)
	{
		$this->boostMode = $boostMode;
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
