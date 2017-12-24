<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kFlavorTypeCondition extends kCondition
{
	/**
	 * @var array
	 */
	private $flavorTypes;
	
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(ConditionType::FLAVOR_TYPE);
		parent::__construct($not);
	}

	/**
	 * @param kScope $scope
	 * @return bool
	 */
	protected function internalFulfilled(kScope $scope)
	{
		// no flavor type defined, the condition is fulfilled
		if (count($this->getFlavorTypes()) == 0)
			return true;

		//get flavor from scope
		$asset = (is_callable(array($scope, 'getAsset')) ? $scope->getAsset() : null);
		if ($asset)
			return in_array($asset->getType(), $this->flavorTypes);
		return false;
	}

	/**
	 * @param array $flavorTypes
	 */
	public function setFlavorTypes($flavorTypes)
	{
		$this->flavorTypes = $flavorTypes;
	}

	/**
	 * @return array
	 */
	public function getFlavorTypes()
	{
		return $this->flavorTypes;
	}

}
