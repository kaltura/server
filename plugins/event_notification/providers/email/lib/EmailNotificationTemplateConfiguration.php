<?php 
class Form_EmailNotificationTemplateConfiguration extends Form_EventNotificationTemplateConfiguration
{
	protected function addTypeElements()
	{
		$this->addElement('select', 'format', array(
			'label'			=> 'Format:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
		));
		
		$this->addElement('text', 'subject', array(
			'label'			=> 'Subject:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('textarea', 'body', array(
			'label'			=> 'Body:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'from_email', array(
			'label'			=> 'Sender e-mail:',
			'filters'		=> array('StringTrim'),
			'validators'	=> array('EmailAddress'),
		));
		
		$this->addElement('text', 'from_name', array(
			'label'			=> 'Sender name:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'to_email', array(
			'label'			=> 'Receipient e-mail:',
			'filters'		=> array('StringTrim'),
			'validators'	=> array('EmailAddress'),
		));
		
		$this->addElement('text', 'to_name', array(
			'label'			=> 'Receipient name:',
			'filters'		=> array('StringTrim'),
		));
		
		$element = $this->getElement('format');
		$reflect = new ReflectionClass('Kaltura_Client_EmailNotification_Enum_EmailNotificationFormat');
		$types = $reflect->getConstants();
		foreach($types as $constName => $value)
		{
			$name = ucfirst(str_replace('_', ' ', $constName));
			$element->addMultiOption($value, $name);
		}
	}
}