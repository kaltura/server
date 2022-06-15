<?php

class Form_KafkaNotificationTemplateConfiguration extends Form_EventNotificationTemplateConfiguration
{
	/* (non-PHPdoc)
	 * @see Infra_Form::getObject()
	 */
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		return parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
	}
	
	/* (non-PHPdoc)
	 * @see Infra_Form::populateFromObject()
	 */
	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
	}
	
	/* (non-PHPdoc)
	 * @see Form_EventNotificationTemplateConfiguration::addTypeElements()
	 */
	protected function addTypeElements(Kaltura_Client_EventNotification_Type_EventNotificationTemplate $eventNotificationTemplate)
	{
		$format = new Kaltura_Form_Element_EnumSelect('format', array(
			'enum' => 'Kaltura_Client_KafkaNotification_Enum_KafkaNotificationFormat',
			'label' => 'Format:',
			'filters' => array('StringTrim'),
			'required' => true,
		));
		$this->addElements(array($format));
		
	}
}