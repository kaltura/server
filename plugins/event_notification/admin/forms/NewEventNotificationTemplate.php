<?php 
class Form_NewEventNotificationTemplate extends Zend_Form
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
		));
		
		$element = $this->addElement('select', 'newType', array(
			'label'			=> 'Type:',
			'filters'		=> array('StringTrim'),
		));
				
		// submit button
		$this->addElement('button', 'newEventNotificationTemplate', array(
			'label'		=> 'Create New',
			'onclick'		=> "doAction('newEventNotificationTemplate', $('#newPartnerId').val(), $('#newType').val())",
			'decorators'	=> array('ViewHelper'),
		));
		
		$element = $this->getElement('newType');
		$reflect = new ReflectionClass('Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType');
		$types = $reflect->getConstants();
		foreach($types as $constName => $value)
		{
			$name = ucfirst(str_replace('_', ' ', $constName));
			$element->addMultiOption($value, $name);
		}
	}
}