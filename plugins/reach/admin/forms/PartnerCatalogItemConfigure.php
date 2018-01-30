<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_PartnerCatalogItemConfigure extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();

		$this->setAttrib('id', 'frmPartnerCatalogItemConfigure');
		$this->setMethod('post');

		$this->removeElement("cmdSubmit");
		$this->removeElement("filter_type");
		$this->removeElement("filter_input");
	}
}