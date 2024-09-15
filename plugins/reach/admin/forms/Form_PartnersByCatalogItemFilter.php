<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_PartnersByCatalogItemFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();

		$this->removeElement("cmdSubmit");
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'catalogItemIdEqual' => 'Catalog Item ID',
		));

		$this->addElement('button', 'cmdSubmit', array(
			'type' => 'submit',
			'label'		=> 'Search',
			'decorators' => array('ViewHelper'),
		));
	}
}
