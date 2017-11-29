<?php 
/**
 * @package plugins.reach
 * @subpackage Admin
 */
class Form_CreateCatalogItem extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'frmCreateCatalogItem');
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));

		$this->addElement('text', 'newPartnerId', array(
			'label' => 'Publisher ID:',
//			'onkeypress' => "return supressFormSubmit(event)",
			'filters' => array('StringTrim'),
		));

		$newServiceFeature = new Kaltura_Form_Element_EnumSelect('cloneTemplateServiceFeature', array(
			'enum' => 'Kaltura_Client_Reach_Enum_VendorServiceFeature',
			'label'			=> 'Service Feature:',
			'onchange'		=> "switchTemplatesBoxForServiceFeature()",
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		), true);
		$this->addElements(array($newServiceFeature));

		$newServiceType = new Kaltura_Form_Element_EnumSelect('cloneTemplateServiceType', array(
			'enum' => 'Kaltura_Client_Reach_Enum_VendorServiceType',
			'label'			=> 'Service Type:',
			'onchange'		=> "switchTemplatesBoxByServiceType()",
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		), true);
		$this->addElements(array($newServiceType));

		$newTurnAround = new Kaltura_Form_Element_EnumSelect('cloneTemplateTurnAround', array(
			'enum' => 'Kaltura_Client_Reach_Enum_VendorServiceTurnAroundTime',
			'label'			=> 'Service Turn Around Time:',
			'onchange'		=> "switchTemplatesBoxByTurnAround()",
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		), true);
		$this->addElements(array($newTurnAround));

		$element = $this->addElement('select', 'cloneTemplateId', array(
			'label'			=> 'Template:',
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		));

		$this->addElement('button', 'newCatalogItemTemplate', array(
			'label'		=> 'Add from template',
			'onclick'		=> "cloneCatalogItemTemplate($('#newPartnerId').val(), $('#cloneTemplateId').val())",
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		));

		// submit button
		$this->addElement('button', 'newCatalogItem', array(
			'ignore' => true,
			'label' => 'Create New',
			'onclick' => "addNewCatalogItem($('#newPartnerId').val(), $('#cloneTemplateServiceFeature').val())",
			'decorators' => array('ViewHelper'),
		));
	}
}