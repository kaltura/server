<?php 
/**
 * @package plugins.eventNotification
 * @subpackage admin
 */
abstract class Form_EventNotificationTemplateConfiguration extends Infra_Form
{
	/**
	 * @var int
	 */
	protected $partnerId;
	
	/**
	 * @var Kaltura_Client_EventNotification_Enum_EventNotificationTemplateType
	 */
	protected $templateType;
	
	/**
	 * @var int
	 */
	protected $userParametersCount = 0;
	
	public function __construct($partnerId, $templateType)
	{
		$this->partnerId = $partnerId;
		$this->templateType = $templateType;
		
		parent::__construct();
	}
	
	abstract protected function addTypeElements();

	protected function getDescriptionHtml($description)
	{
		$description = preg_replace('/See:([^:]+):([^\s.]+)/', 'See <a target="_tab" href="$2">$1</a>', $description);
		
		return $description;
	}
	
	/**
	 * @param Kaltura_Client_Type_StringValue|Kaltura_Client_Type_BooleanValue $field
	 * @return string
	 */
	protected function getValueDescription($field)
	{
		if($field instanceof Kaltura_Client_Type_EvalStringField || $field instanceof Kaltura_Client_Type_EvalBooleanField)
			return 'Code: ' . $field->code;
	
		if($field->value)
			return $this->getDescriptionHtml($field->value);
		
		return $field->getKalturaObjectType();
	}
	
	protected function getParameterDescription(Kaltura_Client_EventNotification_Type_EventNotificationParameter $parameter)
	{
		$html = "<b>$parameter->key</b> - ";
		if($parameter->description)
			return $this->getDescriptionHtml($html . $parameter->description);
			
		return $this->getDescriptionHtml($html . $this->getValueDescription($parameter->value));
	}
	
	protected function getConditionDescription(Kaltura_Client_Type_Condition $condition)
	{
		if($condition->description)
			return $this->getDescriptionHtml($condition->description);
			
		if($condition instanceof Kaltura_Client_EventNotification_Type_EventFieldCondition)
			return $this->getDescriptionHtml($this->getValueDescription($condition->field));
			
		return $condition->getKalturaObjectType();
	}
	
	/* (non-PHPdoc)
	 * @see Infra_Form::populateFromObject()
	 */
	public function populateFromObject($object, $add_underscore = true)
	{
		if(!($object instanceof Kaltura_Client_EventNotification_Type_EventNotificationTemplate))
			return;
			
		parent::populateFromObject($object, $add_underscore);
		
		if($object->contentParameters && count($object->contentParameters))
		{
			$contentParameters = array();		
			foreach($object->contentParameters as $index => $parameter)
				$contentParameters[] = $this->getParameterDescription($parameter);
				
			$contentParametersList = new Infra_Form_HtmlList('contentParameters', array(
				'legend'		=> 'Content Parameters',
				'list'			=> $contentParameters,
			));
			$this->addElements(array($contentParametersList));
		}
		
		if($object->eventConditions && count($object->eventConditions))
		{
			$eventConditions = array();
			foreach($object->eventConditions as $condition)
				$eventConditions[] = $this->getConditionDescription($condition);
				
			$eventConditionsList = new Infra_Form_HtmlList('eventConditions', array(
				'legend'		=> 'Conditions',
				'list'			=> $eventConditions,
			));
			$this->addElements(array($eventConditionsList));
		}
			
		foreach($object->userParameters as $parameter)
			$this->addUserParameter($parameter);
			
		$this->addElement('button', 'addUserParameterButton', array(
			'label'			=> 'Add User Parameter',
			'onclick'		=> "newUserParameter()",
			'decorators'	=> array('ViewHelper'),
		));
		
		$this->addElement('text', 'userParameterKey', array(
			'label'			=> 'Key:',
			'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
		));
		
		$this->addElement('text', 'userParameterValue', array(
			'label'			=> 'Value:',
			'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
		));
		
		$this->addElement('button', 'removeUserParameterButton', array(
			'label'			=> 'Remove',
			'decorators'	=> array('ViewHelper'),
		));
		
		$this->addDisplayGroup(array('userParameterKey', 'userParameterValue', 'removeUserParameterButton'), 
			'frmUserParameter', 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'style' => 'display: none', 'id' => 'frmUserParameter'))),
		));
			
		$this->addDisplayGroup(array('addUserParameterButton'), 
			'frmParameters', 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'frmParameters'))),
		));
		
		$this->finit();
		
		parent::populateFromObject($object, $add_underscore);
	}
	
	/* (non-PHPdoc)
	 * @see Infra_Form::getObject()
	 */
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		KalturaLog::debug("Loading object type [" . get_class($object) . "] for type [$objectType]");
		
		if($object instanceof Kaltura_Client_EventNotification_Type_EventNotificationTemplate)
		{
			KalturaLog::debug("Search properties [" . print_r($properties, true) . "]");
			
			$userParameters = $object->userParameters;
			if(!$userParameters || !is_array($userParameters))
				$userParameters = array();
				
			foreach($properties as $property => $value)
			{
				$matches = null;
				if(preg_match('/userParameterKey_(\d+)$/', $property, $matches))
				{
					$index = $matches[1];
					$field = new Kaltura_Client_Type_StringValue();
					$field->value = $properties["userParameterValue_{$index}"];
					
					$userParameter = new Kaltura_Client_EventNotification_Type_EventNotificationParameter();
					$userParameter->key = $value;
					$userParameter->value = $field;
					
					$userParameters[] = $userParameter;
				}
			}
			
			if(isset($properties['userParameterKey']) && is_array($properties['userParameterKey']))
			{
				foreach($properties['userParameterKey'] as $index => $value)
				{
					$field = new Kaltura_Client_Type_StringValue();
					$field->value = $properties['userParameterValue'][$index];
					
					$userParameter = new Kaltura_Client_EventNotification_Type_EventNotificationParameter();
					$userParameter->key = $value;
					$userParameter->value = $field;
					
					$userParameters[] = $userParameter;
				}
				
				KalturaLog::debug("Set user parameters [" . print_r($userParameters, true) . "]");
				if(count($userParameters))
					$object->userParameters = $userParameters;
			}
		}
		
		return $object;
	}
	
	/**
	 * Set to null all the attributes that shouldn't be updated
	 * @param Kaltura_Client_EventNotification_Type_EventNotificationTemplate $eventNotificationTemplate
	 */
	public function resetUnUpdatebleAttributes(Kaltura_Client_EventNotification_Type_EventNotificationTemplate $eventNotificationTemplate)
	{
		// reset readonly attributes
		$eventNotificationTemplate->id = null;
		$eventNotificationTemplate->partnerId = null;
		$eventNotificationTemplate->createdAt = null;
		$eventNotificationTemplate->updatedAt = null;
		$eventNotificationTemplate->type = null;
		$eventNotificationTemplate->status = null;
	}
	
	public function finit()
	{
		$this->addElement('hidden', 'crossLine01', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$this->addTypeElements();
	}
	
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmEventNotificationTemplateConfig');

		$this->setDescription('event notification templates configure intro text');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));

		$this->addElement('text', 'name', array(
			'label'			=> 'Name:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
		));
		
		$this->addElement('text', 'system_name', array(
			'label'			=> 'System name:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'description', array(
			'label'			=> 'Description:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'partner_id', array(
			'label'			=> 'Publisher ID:',
			'value'			=> $this->partnerId,
			'readonly'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'manual_dispatch_enabled', array(
			'label'			=> 'Manual dispatch enabled:',
			'disabled'		=> true,
			'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt'))),
		));
		
		$this->addElement('checkbox', 'automatic_dispatch_enabled', array(
			'label'			=> 'Automatic dispatch enabled:',
			'disabled'		=> true,
			'decorators' 	=> array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt'))),
			'onclick'		=> 'automaticEnabled(this.checked)',
		));
		
		$eventType = new Kaltura_Form_Element_EnumSelect('event_type', array(
			'enum' => 'Kaltura_Client_EventNotification_Enum_EventNotificationEventType',
			'disabled'		=> true,
			'label'			=> 'Event type:',
		));
		
		$eventObjectType = new Kaltura_Form_Element_EnumSelect('event_object_type', array(
			'enum' => 'Kaltura_Client_EventNotification_Enum_EventNotificationEventObjectType',
			'disabled'		=> true,
			'label'			=> 'Event object type:',
		));
		
		$this->addElements(array($eventType, $eventObjectType));
		
		$this->addDisplayGroup(array('event_type', 'event_object_type'), 
			'automatic_config', 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'frmAutomaticConfig'))),
				'legend'		=> 'Automatic dispatch configuration',
		));
				
		$this->addElement('hidden', 'type', array(
			'value'			=> $this->templateType,
		));
	}
	
	/**
	 * @param Kaltura_Client_HttpNotification_Type_EventNotificationParameter $parameter
	 */
	protected function addUserParameter(Kaltura_Client_EventNotification_Type_EventNotificationParameter $parameter)
	{
		$this->addElement('text', "userParameterKey_{$this->userParametersCount}", array(
			'label'			=> 'Key:',
			'readonly'		=> true,
			'value'			=> $parameter->key,
			'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
		));
	
		$this->addElement('text', "userParameterValue_{$this->userParametersCount}", array(
			'label'			=> 'Value:',
			'value'			=> $parameter->value->value,
			'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
		));
		
		$elements = array();
		
		if($parameter->description)
		{
			$element = new Infra_Form_Html("userParameterDesciption_{$this->userParametersCount}", array(
				'content' => '<div>' . $this->getDescriptionHtml($parameter->description) . '</div>',
			));
			$this->addElements(array($element));
			
			$elements[] = "userParameterDesciption_{$this->userParametersCount}";
		}
		
		$elements[] = "userParameterKey_{$this->userParametersCount}";
		$elements[] = "userParameterValue_{$this->userParametersCount}"; 
		$elements[] = "removeUserParameterButton_{$this->userParametersCount}";
		
		$this->addDisplayGroup($elements, 
			"frmUserParameter_{$this->userParametersCount}", 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => "frmUserParameter_{$this->userParametersCount}"))),
				'legend'		=> 'User Parameter',
		));
		
		$this->userParametersCount++;
	}
}