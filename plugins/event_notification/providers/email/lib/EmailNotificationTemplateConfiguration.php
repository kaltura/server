<?php 
class Form_EmailNotificationTemplateConfiguration extends Form_EventNotificationTemplateConfiguration
{
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
				if (kString::isEmailString($properties['to_email']) || $properties['to_email']=='{creator_email}' || $properties['to_email']=='{owner_email}')
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
				else
					throw new Exception(KalturaEventNotificationErrors::INVALID_TO_EMAIL);
			}
			if(isset($properties['cc_email']) && strlen(trim($properties['cc_email'])))
			{
				if (kString::isEmailString($properties['cc_email']) || $properties['cc_email']=='{creator_email}' || $properties['cc_email']=='{owner_email}')
				{
				$CCemail = new Kaltura_Client_Type_StringValue();
				$CCemail->value = $properties['cc_email'];
		
				$CCname = null;
				if(isset($properties['cc_name']) && strlen(trim($properties['cc_name'])))
				{
					$CCname = new Kaltura_Client_Type_StringValue();
					$CCname->value = $properties['cc_name'];
				}
		
				$CCrecipient = new Kaltura_Client_EmailNotification_Type_EmailNotificationRecipient();
				$CCrecipient->email = $CCemail;
				$CCrecipient->name = $CCname;
		
				$CCrecipientProvider = new Kaltura_Client_EmailNotification_Type_EmailNotificationStaticRecipientProvider();
				$CCrecipientProvider->emailRecipients = array();
				$CCrecipientProvider->emailRecipients[] = $CCrecipient;
		
				$object->cc = $CCrecipientProvider;
				}
				else
					throw new Exception(KalturaEventNotificationErrors::INVALID_CC_EMAIL);
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
		
		if($object->to && $object->to instanceof Kaltura_Client_EmailNotification_Type_EmailNotificationStaticRecipientProvider)
		{
			if(count($object->to->emailRecipients) > 1)
			{
				$this->addError("Multiple recipients is not supported in admin console, saving the configuration will remove the existing recipients list.");
			}
			elseif(count($object->to->emailRecipients))
			{
				$to = reset($object->to->emailRecipients);
				/* @var $to Kaltura_Client_EmailNotification_Type_EmailNotificationRecipient */
				
				$this->addElement('text', 'to_email', array(
					'label'			=> 'Head Recipient e-mail:',
					'value'			=> $to->email->value,
					'filters'		=> array('StringTrim'),
					'validators'	=> array('EmailAddress'),
				));
				
				$this->addElement('text', 'to_name', array(
					'label'			=> 'Head Recipient name:',
					'value'			=> $to->name->value,
					'filters'		=> array('StringTrim'),
				));
			}
		}
		
		if($object->cc && $object->cc instanceof Kaltura_Client_EmailNotification_Type_EmailNotificationStaticRecipientProvider)
		{	
			if(count($object->cc->emailRecipients) > 1)
			{
				$this->addError("Multiple recipients is not supported in admin console, saving the configuration will remove the existing recipients list.");
			}
			elseif(count($object->cc->emailRecipients))
			{	
				$cc = reset($object->cc->emailRecipients);
				/* @var $cc Kaltura_Client_EmailNotification_Type_EmailNotificationRecipient */
		
				$this->addElement('text', 'cc_email', array(
						'label'                 => 'CC Recipient e-mail:',
						'value'                 => $cc->email->value,
						'filters'               => array('StringTrim'),
						'validators'    => array('EmailAddress'),
				));
		
				$this->addElement('text', 'cc_name', array(
						'label'                 => 'CC Recipient name:',
						'value'                 => $cc->name->value,
						'filters'               => array('StringTrim'),
				));
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see Form_EventNotificationTemplateConfiguration::addTypeElements()
	 */
	protected function addTypeElements()
	{
		$format = new Kaltura_Form_Element_EnumSelect('format', array(
			'enum' => 'Kaltura_Client_EmailNotification_Enum_EmailNotificationFormat',
			'label'			=> 'Format:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
		));
		$this->addElements(array($format));
		
		$this->addElement('text', 'subject', array(
			'label'			=> 'Subject:',
			'size'			=> 60,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('textarea', 'body', array(
			'label'			=> 'Body:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'from_email', array(
			'label'			=> 'Sender e-mail:',
			'size'			=> 60,
			'filters'		=> array('StringTrim'),
			'validators'	=> array('EmailAddress'),
		));
		
		$this->addElement('text', 'from_name', array(
			'label'			=> 'Sender name:',
			'size'			=> 60,
			'filters'		=> array('StringTrim'),
		));
	}
}