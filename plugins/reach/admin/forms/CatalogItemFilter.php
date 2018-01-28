<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CatalogItemFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();

		$this->removeElement("cmdSubmit");
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'vendorPartnerIdEqual' => 'Vendor Partner ID',
//			'idEqual' => 'Catalog Item ID',
		));

		$filterServiceFeature = new Kaltura_Form_Element_EnumSelect('filterServiceFeature', array(
			'enum' => 'Kaltura_Client_Reach_Enum_VendorServiceFeature',
			'label'			=> 'Service Feature:',
			'onchange'		=> "updateFiltersView()",
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		), array(null => "Service Feature"));
		$this->addElements(array($filterServiceFeature));

		$filterServiceType = new Kaltura_Form_Element_EnumSelect('filterServiceType', array(
			'enum' => 'Kaltura_Client_Reach_Enum_VendorServiceType',
			'label'			=> 'Service Type:',
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		), array(null => "Service Type"));
		$this->addElements(array($filterServiceType));

		$filterTurnAroundTime = new Kaltura_Form_Element_EnumSelect('filterTurnAroundTime', array(
			'enum' => 'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime',
			'label'			=> 'Service Turn Around Time:',
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		), array(null => "Turn Around Time"));
		$this->addElements(array($filterTurnAroundTime));
		
		$filterSourceLanguage = new Kaltura_Form_Element_EnumSelect('filterSourceLanguage', array(
			'enum' => 'Kaltura_Client_Reach_Enum_CatalogItemLanguage',
			'label'			=> 'Source Language:',
			'filters'		=> array('StringTrim'),
			'hidden'		=> true,
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		), array(null => "Source Language"));
		$this->addElements(array($filterSourceLanguage));
		
		$filterTargetLanguage = new Kaltura_Form_Element_EnumSelect('filterTargetLanguage', array(
			'enum' => 'Kaltura_Client_Reach_Enum_CatalogItemLanguage',
			'label'			=> 'Target Language:',
			'hidden'		=> true,
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		), array(null => "Target Language"));
		$this->addElements(array($filterTargetLanguage));

		// submit button
		$this->addElement('button', 'cmdSubmit', array(
			'type' => 'submit',
			'label'		=> 'Search',
			'decorators' => array('ViewHelper'),
		));

		// submit button
		$this->addElement('button', 'newCatalogItem', array(
			'ignore' => true,
			'label' => 'Create New',
			'onclick' => "addNewCatalogItem(0, $('#filterServiceFeature').val(), $('#filterServiceType').val(), $('#filterTurnAroundTime').val())",
			'decorators' => array('ViewHelper'),
		));
	}
}