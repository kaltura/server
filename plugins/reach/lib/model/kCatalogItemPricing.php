<?php

/**
 * Catalog Item pricing calac definition
 *
 * @package Core
 * @subpackage model
 *
 */
class kCatalogItemPricing
{
	/**
	 * @var int
	 */
	protected $pricePerUnit;
	
	/**
	 * @var string
	 */
	protected $priceFunction;
	
	/**
	 * @return the $pricePerUnit
	 */
	public function getPricePerUnit() 
	{
		return $this->pricePerUnit;
	}
	
	/**
	 * @param int $pricePerUnit
	 */
	public function setPricePerUnit($pricePerUnit) 
	{
		$this->pricePerUnit = $pricePerUnit;
	}
	
	/**
	 * @return the $pricePerUnit
	 */
	public function getPriceFunction() 
	{
		return $this->priceFunction;
	}
	
	/**
	 * @param string $priceFunction
	 */
	public function setPriceFunction($priceFunction) 
	{
		$this->priceFunction = $priceFunction;
	}
}