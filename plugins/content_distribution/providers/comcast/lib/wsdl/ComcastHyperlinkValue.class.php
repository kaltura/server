<?php


class ComcastHyperlinkValue extends ComcastFieldValue
{				
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
	public $mimeType;
				
	/**
	 * @var string
	 **/
	public $target;
				
}


