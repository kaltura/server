<?php


class ComcastAssetTypeSort extends SoapObject
{				
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			case 'field':
				return 'ComcastAssetTypeField';
			case 'tieBreaker':
				return 'ComcastAssetTypeSort';
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var ComcastAssetTypeField
	 **/
	public $field;
				
	/**
	 * @var boolean
	 **/
	public $descending;
				
	/**
	 * @var ComcastAssetTypeSort
	 **/
	public $tieBreaker;
				
}


