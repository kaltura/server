<?php 
/**
 * @package plugins.eventNotification
 * @subpackage admin
 */
class Form_NewEventNotificationTemplate extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'addNewEventNotificationTemplate');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		$this->addElement('text', 'newPartnerId', array(
			'label'			=> 'Publisher ID:',
			'onkeypress'	=> "return supressFormSubmit(event)",
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		));
		
		$newType = new Kaltura_Form_Element_EnumSelect('cloneTemplateType', array(
			'enum' => 'Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType',
			'label'			=> 'Type:',
			'onchange'		=> "switchTemplatesBox()",
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		));
		$this->addElements(array($newType));
		
		$element = $this->addElement('select', 'cloneTemplateId', array(
			'label'			=> 'Template:',
			'filters'		=> array('StringTrim'),
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		));
				
		// submit button
		$this->addElement('button', 'newEventNotificationTemplate', array(
			'label'		=> 'Add from template',
			'onclick'		=> "cloneEventNotificationTemplate()",
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		));
	}
}