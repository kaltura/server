<?php 
class Form_EmailNotificationTemplateConfiguration extends Form_EventNotificationTemplateConfiguration
{
	/**
	 * @var int
	 */
	protected $contentParametersCount = 0;

	/* (non-PHPdoc)
	 * @see Infra_Form::getObject()
	 */
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		KalturaLog::debug("Loading object type [" . get_class($object) . "] for type [$objectType]");
		
		if($object instanceof Kaltura_Client_EmailNotification_Type_EmailNotificationTemplate)
		{
			KalturaLog::debug("Search properties [" . print_r($properties, true) . "]");

			if(isset($properties['to_email']) && strlen(trim($properties['to_email'])))
			{
				$email = new Kaltura_Client_Type_StringValue();
				$email->value = $properties['to_email'];
				
				$name = null;
				if(isset($properties['to_name']) && strlen(trim($properties['to_name'])))
				{
					$name = new Kaltura_Client_Type_StringValue();
					$name->value = $properties['to_name'];
				}
				
				$recipient = new Kaltura_Client_EmailNotification_Type_EmailNotificationRecipient();
				$recipient->email = $email; 
				$recipient->name = $name; 
				
				$recipientProvider = new Kaltura_Client_EmailNotification_Type_EmailNotificationStaticRecipientProvider();
				$recipientProvider->emailRecipients = array();
				$recipientProvider->emailRecipients[] = $recipient;
				
				$object->to = $recipientProvider;
			}
			
			$contentParameters = $object->contentParameters;
			if(!$contentParameters || !is_array($contentParameters))
				$contentParameters = array();
				
			foreach($properties as $property => $value)
			{
				$matches = null;
				if(preg_match('/contentParameterKey_(\d+)$/', $property, $matches))
				{
					$index = $matches[1];
					$field = new Kaltura_Client_Type_EvalStringField();
					$field->code = $properties["contentParameterValue_{$index}"];
					
					$contentParameter = new Kaltura_Client_EventNotification_Type_EventNotificationParameter();
					$contentParameter->key = $value;
					$contentParameter->value = $field;
					
					$contentParameters[] = $contentParameter;
				}
			}
			
			if(isset($properties['contentParameterKey']) && is_array($properties['contentParameterKey']))
			{
				foreach($properties['contentParameterKey'] as $index => $value)
				{
					$field = new Kaltura_Client_Type_EvalStringField();
					$field->code = $properties['contentParameterValue'][$index];
					
					$contentParameter = new Kaltura_Client_EmailNotification_Type_EmailNotificationParameter();
					$contentParameter->key = $value;
					$contentParameter->value = $field;
					
					$contentParameters[] = $contentParameter;
				}
				
				KalturaLog::debug("Set content parameters [" . print_r($contentParameters, true) . "]");
				if(count($contentParameters))
					$object->contentParameters = $contentParameters;
			}
		}
		
		return $object;
	}
	
	/* (non-PHPdoc)
	 * @see Infra_Form::populateFromObject()
	 */
	public function populateFromObject($object, $add_underscore = true)
	{
		parent::populateFromObject($object, $add_underscore);
		
		if(!($object instanceof Kaltura_Client_EmailNotification_Type_EmailNotificationTemplate))
			return;
		
		foreach($object->contentParameters as $parameter)
			$this->addContentParameter($parameter);
			
		if(count($object->to->emailRecipients) > 1)
			$this->addError("Multiple recipients is not supported in admin console, saving the configuration will remove the existing recipients list.");
			
		if(count($object->to->emailRecipients))
		{
			$to = reset($object->to->emailRecipients);
			/* @var $to KalturaEmailNotificationRecipient */
			
			$this->setDefault('to_email', $to->email->value);
			$this->setDefault('to_name', $to->name->value);
		}
	}
	
	/* (non-PHPdoc)
	 * @see Form_EventNotificationTemplateConfiguration::addTypeElements()
	 */
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
			'label'			=> 'Recipient e-mail:',
			'filters'		=> array('StringTrim'),
			'validators'	=> array('EmailAddress'),
		));
		
		$this->addElement('text', 'to_name', array(
			'label'			=> 'Recipient name:',
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
		
		$this->addElement('button', 'addContentParameterButton', array(
			'label'			=> 'Add Content Parameter',
			'onclick'		=> "newContentParameter()",
			'decorators'	=> array('ViewHelper'),
		));
		
		$this->addElement('text', 'contentParameterKey', array(
			'label'			=> 'Key:',
			'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
		));
		
		$this->addElement('text', 'contentParameterValue', array(
			'label'			=> 'Value:',
			'readonly'		=> true,
			'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
		));
		
		$this->addElement('button', 'removeContentParameterButton', array(
			'label'			=> 'Remove',
			'decorators'	=> array('ViewHelper'),
		));
			
		$this->addDisplayGroup(array('contentParameterKey', 'contentParameterValue', 'removeContentParameterButton'), 
			'frmContentParameter', 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'style' => 'display: none', 'id' => 'frmContentParameter'))),
				'legend'		=> 'New content parameter',
		));
	}
	
	/**
	 * @param Kaltura_Client_EmailNotification_Type_EmailNotificationParameter $parameter
	 */
	protected function addContentParameter(Kaltura_Client_EventNotification_Type_EventNotificationParameter $parameter)
	{
		if($parameter->value instanceof Kaltura_Client_Type_EvalStringField)
		{
			$this->addElement('text', "contentParameterKey_{$this->contentParametersCount}", array(
				'label'			=> 'Key:',
				'value'			=> $parameter->key,
				'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
			));
		
			$this->addElement('text', "contentParameterValue_{$this->contentParametersCount}", array(
				'label'			=> 'Value:',
				'readonly'		=> true,
				'value'			=> $parameter->value->code,
				'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
			));
			
			$this->addElement('button', "removeContentParameterButton_{$this->contentParametersCount}", array(
				'label'			=> 'Remove',
				'onclick'			=> "removeContentParameter({$this->contentParametersCount})",
				'decorators'	=> array('ViewHelper'),
			));
				
			$this->addDisplayGroup(array("contentParameterKey_{$this->contentParametersCount}", "contentParameterValue_{$this->contentParametersCount}", "removeContentParameterButton_{$this->contentParametersCount}"), 
				"frmContentParameter_{$this->contentParametersCount}", 
				array(
					'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => "frmContentParameter_{$this->contentParametersCount}"))),
					'legend'		=> 'Content Parameter',
			));
			
			$this->contentParametersCount++;
		}
	}
}