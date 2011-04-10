<?php 
class Form_GenericProviderConfiguration extends Form_DistributionConfiguration
{
	private $properties = null;

	public function populateActions(KalturaGenericDistributionProvider $object)
	{
		$this->addProviderActions();
		
		$client = Kaltura_ClientHelper::getClient();
		$filter = new KalturaGenericDistributionProviderActionFilter();
		$filter->genericDistributionProviderIdEqual = $object->id;
		
		$actionsList = null;
		try
		{
			$actionsList = $client->genericDistributionProviderAction->listAction($filter);
		}
		catch(Exception $e)
		{
			return;
		}

		$fields = array(
			"protocol",
			"serverAddress", 
			"remotePath", 
			"remoteUsername", 
			"remotePassword", 
		);
		
		$files = array(
			"mrssTransformer",
			"mrssValidator", 
			"resultsTransformer", 
		);
		
		foreach($actionsList->objects as $actionObject)
		{
			$action = null;
			switch ($actionObject->action)
			{
				case KalturaDistributionAction::SUBMIT:
					$action = 'submit';
					break;
				case KalturaDistributionAction::UPDATE:
					$action = 'update';
					break;
				case KalturaDistributionAction::DELETE:
					$action = 'delete';
					break;
				case KalturaDistributionAction::FETCH_REPORT:
					$action = 'fetchReport';
					break;
			}
			
			$element = $this->getElement("{$action}-enabled");
			$element->setValue(true);
			
			foreach($fields as $field)
			{
				$element = $this->getElement("{$field}-{$action}");
				$element->setValue($actionObject->$field);
			}
			
			foreach($files as $file)
			{
				if($actionObject->$file)
				{
					$this->addElement('hidden', "{$file}-{$action}-data", array(
						'class' => "{$file}-{$action}-data action-data",
						'value' => $actionObject->$file,
					));
				}
			}
		}
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$this->properties = $properties;
		
		return parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
	}
	
	/**
	 * @param KalturaGenericDistributionProviderAction $object
	 * @param string $action
	 * @param int $actionType
	 * @return KalturaGenericDistributionProviderAction
	 */
	public function getActionObject(KalturaGenericDistributionProviderAction $object, $action, $actionType)
	{
		$object->action = $actionType;
		
		if(!$this->properties || !isset($this->properties["{$action}enabled"]) || !$this->properties["{$action}enabled"])
			return null;
			
		foreach($this->properties as $property => $value)
		{
			$matches = null;
			if(preg_match("/(.+){$action}$/", $property, $matches))
			{
				$propertyName = $matches[1];
				$object->$propertyName = $value;
			}
		}
		
		return $object;
	}
	
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmGenericProviderConfig');

		//$this->setDescription('generic-provider-configure intro text');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));

		$this->addElement('text', 'name', array(
			'label'			=> 'Name:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('checkbox', 'is_default', array(
			'label'	  => 'Set as Default',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'div', 'class' => 'set_default')))
		));
		
		$this->addElement('text', 'partner_id', array(
			'label'			=> 'Publisher ID:',
			'filters'		=> array('StringTrim'),
		));
	}
	
	public function addProviderActions()
	{
		$this->addProviderAction('submit');
		$this->addProviderAction('update');
		$this->addProviderAction('delete');
		$this->addProviderAction('fetchReport');
	}
	
	public function addProviderAction($action)
	{
		$this->addElement('checkbox', "$action-enabled", array(
			'label'	  =>  'Enabled',
			'onchange'		=> "actionEnabledChanged('$action')",
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => "action-enabled $action-enabled")))
		));
			
		$this->addElement('select', "protocol-$action", array(
			'label'	  =>  'Protocol',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$element = $this->getElement("protocol-$action");
		$element->addMultiOption(KalturaDistributionProtocol::FTP, 'FTP');
		$element->addMultiOption(KalturaDistributionProtocol::SFTP, 'SFTP');
		$element->addMultiOption(KalturaDistributionProtocol::SCP, 'SCP');
		$element->addMultiOption(KalturaDistributionProtocol::HTTP, 'HTTP');
		$element->addMultiOption(KalturaDistributionProtocol::HTTPS, 'HTTPS');
			
		$this->addElement('text', "serverAddress-$action", array(
			'label'	  =>  'Server Address',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('text', "remotePath-$action", array(
			'label'	  =>  'Remote Path',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('text', "remoteUsername-$action", array(
			'label'	  =>  'Remote Username',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('text', "remotePassword-$action", array(
			'label'	  =>  'Remote Password',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('file', "mrssTransformer-$action", array(
			'label'	  =>  'MRSS Transformer (XSL)',
			'decorators' => array('File', array('Label', array('placement' => 'prepend', 'class' => "mrssTransformer-$action-label")), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('file', "mrssValidator-$action", array(
			'label'	  =>  'MRSS Validator (XSD)',
			'decorators' => array('File', array('Label', array('placement' => 'prepend', 'class' => "mrssValidator-$action-label")), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addElement('file', "resultsTransformer-$action", array(
			'label'	  =>  'Results Transformer (XSL)',
			'decorators' => array('File', array('Label', array('placement' => 'prepend', 'class' => "resultsTransformer-$action-label")), array('HtmlTag',  array('tag' => 'dt', 'style' => 'display: none', 'class' => "action-fields-$action")))
		));
		
		$this->addDisplayGroup(
			array(
				"$action-enabled", 
				"protocol-$action",
				"serverAddress-$action", 
				"remotePath-$action", 
				"remoteUsername-$action", 
				"remotePassword-$action", 
				"mrssTransformer-$action",
				"mrssValidator-$action",
				"resultsTransformer-$action",
			), 
			"actionGroup-$action",
			array(
				'legend' => ucfirst($action) . ' Action',
				'decorators' => array('FormElements', 'Fieldset', array('HtmlTag',array('class' => "actionGroup-$action"))),
			)
		);
	}
}