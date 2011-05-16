<?php


class ComcastImageValue extends ComcastFieldValue
{				
	public function getType()
	{
		return 'ImageValue';
	}
	
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
	/**
	 * @var string
	 **/
	public $altText;
				
	/**
	 * @var string
	 **/
	public $hyperlinkURL;
				
	/**
	 * @var string
	 **/
	public $imageURL;
				
	/**
	 * @var string
	 **/
	public $mimeType;
				
	/**
	 * @var string
	 **/
	public $target;
				
}


