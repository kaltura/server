<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_CatalogItemNullPricing extends Form_CatalogItemPricing
{
	public function init()
	{
 		parent::init();
 		
 		$this->removeElement("PricePerUnit");
 		$this->removeElement("PriceFunction");
	}
}