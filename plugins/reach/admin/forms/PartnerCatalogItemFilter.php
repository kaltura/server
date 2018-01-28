<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_PartnerCatalogItemFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();

		$this->removeElement("cmdSubmit");
		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'partnerIdEqual' => 'Partner ID',
		));

		$newServiceFeature = new Kaltura_Form_Element_EnumSelect('templateServiceFeature', array(
			'enum' => 'Kaltura_Client_Reach_Enum_VendorServiceFeature',
			'label'			=> 'Service Feature:',
			'onchange'		=> "switchAllTemplates()",
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		), array(null => "Service Feature"));
		$this->addElements(array($newServiceFeature));

		$newServiceType = new Kaltura_Form_Element_EnumSelect('templateServiceType', array(
			'enum' => 'Kaltura_Client_Reach_Enum_VendorServiceType',
			'label'			=> 'Service Type:',
			'onchange'		=> "switchAllTemplates()",
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		), array(null => "Service Type"));
		$this->addElements(array($newServiceType));

		$newTurnAround = new Kaltura_Form_Element_EnumSelect('templateTurnAround', array(
			'enum' => 'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime',
			'label'			=> 'Service Turn Around Time:',
			'onchange'		=> "switchAllTemplates()",
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		), array(null => "Turn Around Time"));
		$this->addElements(array($newTurnAround));

		// submit button
		$this->addElement('button', 'cmdSubmit', array(
			'type' => 'submit',
			'label'		=> 'Search',
			'decorators' => array('ViewHelper'),
		));

		$this->addElement('button', 'configureCatalogItemsButton', array(
			'ignore' => true,
			'label' => 'Configure',
			'onclick' => "configureCatalogItems($('#filter_type').val(), $('#templateServiceFeature').val(), $('#templateServiceType').val(), $('#templateTurnAround').val())",
			'decorators' => array('ViewHelper'),
		));
	}
}