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

			if(isset($properties['to_email']))
			{
				if (strlen(trim($properties['to_email'])))
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
				else //return special null so we can update to null
				{
					$object->to =  Kaltura_Client_ClientBase::getKalturaNullValue();
				}
			}
			
			if(isset($properties['cc_email']))
			{
				if (strlen(trim($properties['cc_email'])))
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
				else //return special null so we can update to null
				{
					$object->cc =  Kaltura_Client_ClientBase::getKalturaNullValue();
				}
			}

			if(isset($properties['bcc_email']))
			{
				if (strlen(trim($properties['bcc_email'])))
				{
					$BCCemail = new Kaltura_Client_Type_StringValue();
					$BCCemail->value = $properties['bcc_email'];

					$BCCname = null;
					if(isset($properties['bcc_name']) && strlen(trim($properties['bcc_name'])))
					{
						$BCCname = new Kaltura_Client_Type_StringValue();
						$BCCname->value = $properties['bcc_name'];
					}

					$BCCrecipient = new Kaltura_Client_EmailNotification_Type_EmailNotificationRecipient();
					$BCCrecipient->email = $BCCemail;
					$BCCrecipient->name = $BCCname;

					$BCCrecipientProvider = new Kaltura_Client_EmailNotification_Type_EmailNotificationStaticRecipientProvider();
					$BCCrecipientProvider->emailRecipients = array();
					$BCCrecipientProvider->emailRecipients[] = $BCCrecipient;

					$object->bcc = $BCCrecipientProvider;
				}
				else //return special null so we can update to null
				{
					$object->bcc =  Kaltura_Client_ClientBase::getKalturaNullValue();
				}
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
		
		
		if (!$object->to || ($object->to instanceof Kaltura_Client_EmailNotification_Type_EmailNotificationStaticRecipientProvider &&  count($object->to->emailRecipients) == 1 &&  get_class($object->to->emailRecipients[0]->email) == 'Kaltura_Client_Type_StringValue'))
		{
			$to = null;	
			if($object->to)
			{
				if(count($object->to->emailRecipients) > 1)
				{
					$this->addError("Multiple recipients is not supported in admin console, saving the configuration will remove the existing recipients list.");
				}
				elseif(count($object->to->emailRecipients))
				{
					$to = reset($object->to->emailRecipients);
					/* @var $to Kaltura_Client_EmailNotification_Type_EmailNotificationRecipient */
				}
			}
		
			$TOEmailValue = $to ? $to->email->value : '';
			$TONameValue = $to && $to->name ? $to->name->value : '';
		
			$this->addElement('text', 'to_email', array(
					'label'			=> 'Recipient e-mail (TO):',
					'value'			=> $TOEmailValue,
					'size'                  => 60,
					'filters'		=> array('StringTrim'),
					'validators'	=> array('EmailAddress'),
			));
				
			$this->addElement('text', 'to_name', array(
					'label'			=> 'Recipient name (TO):',
					'value'			=> $TONameValue,
					'size'                  => 60,
					'filters'		=> array('StringTrim'),
			));
		}
		
		if (!$object->cc || ($object->cc instanceof Kaltura_Client_EmailNotification_Type_EmailNotificationStaticRecipientProvider &&  count($object->cc->emailRecipients) == 1 &&  get_class($object->cc->emailRecipients[0]->email) == 'Kaltura_Client_Type_StringValue'))
		{	
			$cc = null;
			if($object->cc)
			{
				if(count($object->cc->emailRecipients) > 1)
				{
					$this->addError("Multiple recipients is not supported in admin console, saving the configuration will remove the existing recipients list.");
				}
				elseif(count($object->cc->emailRecipients))
				{
					$cc = reset($object->cc->emailRecipients);
					/* @var $cc Kaltura_Client_EmailNotification_Type_EmailNotificationRecipient */
				}
			}
		
			$CCEmailValue = $cc ? $cc->email->value : '';
			$CCNameValue = $cc && $cc->name ? $cc->name->value : '';
		
			$this->addElement('text', 'cc_email', array(
					'label'                 => 'Recipient e-mail (CC):',
					'value'                 => $CCEmailValue,
					'size'                  => 60,
					'filters'               => array('StringTrim'),
					'validators'    => array('EmailAddress'),
			));
		
			$this->addElement('text', 'cc_name', array(
					'label'                 => 'Recipient name (CC):',
					'value'                 => $CCNameValue,
					'size'                  => 60,
				'filters'               => array('StringTrim'),
			));
		}

		if (!$object->bcc || ($object->bcc instanceof Kaltura_Client_EmailNotification_Type_EmailNotificationStaticRecipientProvider &&  count($object->bcc->emailRecipients) == 1 &&  get_class($object->bcc->emailRecipients[0]->email) == 'Kaltura_Client_Type_StringValue'))
		{
			$bcc = null;
			if($object->bcc)
			{
				if(count($object->bcc->emailRecipients) > 1)
				{
					$this->addError("Multiple recipients is not supported in admin console, saving the configuration will remove the existing recipients list.");
				}
				elseif(count($object->bcc->emailRecipients))
				{
					$bcc = reset($object->bcc->emailRecipients);
					/* @var $bcc Kaltura_Client_EmailNotification_Type_EmailNotificationRecipient */
				}
			}

			$BCCEmailValue = $bcc ? $bcc->email->value : '';
			$BCCNameValue = $bcc && $bcc->name ? $bcc->name->value : '';

			$this->addElement('text', 'bcc_email', array(
					'label'                 => 'Recipient e-mail (BCC):',
					'value'                 => $BCCEmailValue,
					'size'                  => 60,
					'filters'               => array('StringTrim'),
					'validators'    => array('EmailAddress'),
			));

			$this->addElement('text', 'bcc_name', array(
					'label'                 => 'Recipient name (BCC):',
					'value'                 => $BCCNameValue,
					'size'                  => 60,
					'filters'               => array('StringTrim'),
			));
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