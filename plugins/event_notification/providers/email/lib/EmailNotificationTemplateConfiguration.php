<?php 
class Form_EmailNotificationTemplateConfiguration extends Form_EventNotificationTemplateConfiguration
{
	/* (non-PHPdoc)
	 * @see Infra_Form::getObject()
	 */
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		if($object instanceof Kaltura_Client_EmailNotification_Type_EmailNotificationTemplate)
		{			
			$headerNames = array('to','cc','bcc');

			foreach($headerNames as $headerName)
				$object->$headerName = $this->getHeaderField($headerName,$properties);
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

		$headerNames = array('to','cc','bcc');

		foreach($headerNames as $headerName)
			$this->populateHeaderField($object , $headerName);
	}
	
	/* (non-PHPdoc)
	 * @see Form_EventNotificationTemplateConfiguration::addTypeElements()
	 */
	protected function addTypeElements(Kaltura_Client_EventNotification_Type_EventNotificationTemplate $eventNotificationTemplate)
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

    private function createRecipientsList($recipientsString)
    {
        $pattern = '/(([\"\'][^\"\']*[\"\'])|[^@;,\"\']*)@[^;,]*/';
        $matches = array();
        preg_match_all($pattern, $recipientsString, $matches);
        if (count($matches) > 0) {
            return $matches[0];
        }
        else{
            return array();
        }
    }

	protected function getHeaderField( $headerName , $properties)
	{
		if (isset($properties[$headerName . '_email']))
		{
			$headerEmailProperty = $headerName . '_email';
			$headerNameProperty = $headerName . '_name';

			if (strlen(trim($properties[$headerEmailProperty])))
			{
				$email = new Kaltura_Client_Type_StringValue();
				$email->value = $properties[$headerEmailProperty];

				$name = null;
				if (isset($properties[$headerNameProperty]) && strlen(trim($properties[$headerNameProperty])))
				{
					$name = new Kaltura_Client_Type_StringValue();
					$name->value = $properties[$headerNameProperty];
				}
				$allRecipients = $this->createRecipientsList($email->value);
                $recipientProvider = new Kaltura_Client_EmailNotification_Type_EmailNotificationStaticRecipientProvider();
                $recipientProvider->emailRecipients = array();

				foreach ($allRecipients as $singleRecipient)
                {
                    $singleRecipient = trim($singleRecipient);
                    if (strlen($singleRecipient) > 0)
                    {
                        $recipient = new Kaltura_Client_EmailNotification_Type_EmailNotificationRecipient();
                        $recipientEmail = new Kaltura_Client_Type_StringValue();
                        $recipientEmail->value = $singleRecipient;
                        $recipient->email = $recipientEmail;
                        $recipient->name = $name;
                        $recipientProvider->emailRecipients[] = $recipient;
                    }
                }
				return $recipientProvider;
			}
			else //return special null so we can update to null
			{
				return  Kaltura_Client_ClientBase::getKalturaNullValue();
			}
		}
	}
	
	protected function populateHeaderField($object , $headerName)
	{
        $headerObject = null;
        if ($object->$headerName)
        {
            $fullEmailRecipientsValue = '';
            foreach ($object->$headerName->emailRecipients as $currentRecipient)
            {
                /** @var Kaltura_Client_EmailNotification_Type_EmailNotificationRecipient $headerObject */
                $fullEmailRecipientsValue .= trim($currentRecipient->email->value) . ';';
            }
            $headerObject = new Kaltura_Client_EmailNotification_Type_EmailNotificationRecipient();
            $headerObject->email->value = $fullEmailRecipientsValue;
            if ($object->$headerName->emailRecipients)
            {
                $firstRecipient = $object->$headerName->emailRecipients[0];
                $headerObject->name->value = $firstRecipient->name->value;
            }
        }

        $objectEmailValue = $headerObject ? $headerObject->email->value : '';
        $objectNameValue = $headerObject && $headerObject->name ? $headerObject->name->value : '';

        $headerEmailProperty = $headerName . '_email';
        $headerNameProperty = $headerName . '_name';

        $headerName = strtoupper($headerName);

        $this->addElement('text', $headerEmailProperty, array(
                'label'                 => 'Recipient e-mail (' . $headerName . '):',
                'value'                 => $objectEmailValue,
                'size'                  => 60,
                'filters'               => array('StringTrim'),
                'validators'    => array('EmailAddress'),
        ));

        $this->addElement('text', $headerNameProperty, array(
                'label'                 => 'Recipient name (' . $headerName . '):',
                'value'                 => $objectNameValue,
                'size'                  => 60,
                'filters'               => array('StringTrim'),
        ));
	}	
}