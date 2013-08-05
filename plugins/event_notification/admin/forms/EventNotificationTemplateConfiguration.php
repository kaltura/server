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
			
		foreach($object->userParameters as $parameter)
			$this->addUserParameter($parameter);
			
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
				$subMatches = null;
				if(preg_match('/^userParameterKey_(\d+)$/', $property, $matches))
				{
					$index = $matches[1];
					
					$userParameter = null;
					if(isset($properties["userParameterValue_{$index}"]))
					{
						$field = new Kaltura_Client_Type_StringValue();
						$field->value = $properties["userParameterValue_{$index}"];
						
						$userParameter = new Kaltura_Client_EventNotification_Type_EventNotificationParameter();
						$userParameter->value = $field;
					}
					else
					{
						$userParameter = new Kaltura_Client_EventNotification_Type_EventNotificationArrayParameter();
						$userParameter->allowedValues = array();
						$userParameter->values = array();
						
						foreach($properties as $subProperty => $subValue)
						{
							if($subValue && preg_match("/^userParameterItem_{$index}_(\d+)$/", $subProperty))
							{
								$string = new Kaltura_Client_Type_String();
								$string->value = $subValue;
								$userParameter->values[] = $string;
							}
							elseif(preg_match("/^userParameterAllowedValue_{$index}_(\d+)$/", $subProperty, $subMatches))
							{
								$subIndex = $subMatches[1];
								
								$description = null;
								if(isset($properties["userParameterAllowedDescription_{$index}_$subIndex"]))
									$description = $properties["userParameterAllowedDescription_{$index}_$subIndex"];
									
								$string = new Kaltura_Client_Type_StringValue();
								$string->value = $subValue;
								$string->description = $description;
								$userParameter->allowedValues[] = $string;
							}
						}
					}
					
					$description = null;
					if(isset($properties["userParameterDescription_{$index}"]))
						$description = $properties["userParameterDescription_{$index}"];
						
					$userParameter->key = $value;
					$userParameter->description = $description;
					$userParameters[] = $userParameter;
				}
			}
			
			$object->userParameters = $userParameters;
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
			'size'			=> 60,
			'filters'		=> array('StringTrim'),
			'required'		=> true,
		));
		
		$this->addElement('text', 'system_name', array(
			'label'			=> 'System name:',
			'size'			=> 60,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'description', array(
			'label'			=> 'Description:',
			'size'			=> 60,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'partner_id', array(
			'label'			=> 'Publisher ID:',
			'size'			=> 60,
			'value'			=> $this->partnerId,
			'readonly'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'manual_dispatch_enabled', array(
			'label'			=> 'Manual dispatch enabled:',
			'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt'))),
		));
		
		$this->addElement('checkbox', 'automatic_dispatch_enabled', array(
			'label'			=> 'Automatic dispatch enabled:',
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
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		));
	}
	
	/**
	 * @param Kaltura_Client_HttpNotification_Type_EventNotificationParameter $parameter
	 */
	protected function addUserParameter(Kaltura_Client_EventNotification_Type_EventNotificationParameter $parameter)
	{
		$elements = array();
	
		if($parameter->description)
		{
			$element = new Infra_Form_Html("userParameterDesciption_{$this->userParametersCount}", array(
				'content' => '<div>' . $this->getDescriptionHtml($parameter->description) . '</div>',
			));
			$this->addElements(array($element));
			
			$elements[] = "userParameterDesciption_{$this->userParametersCount}";
		}
		
		$this->addElement('hidden', "userParameterDescription_{$this->userParametersCount}", array(
			'value'			=> $parameter->description,
			'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
		));
		
		$this->addElement('text', "userParameterKey_{$this->userParametersCount}", array(
			'label'			=> 'Key:',
			'readonly'		=> true,
			'value'			=> $parameter->key,
			'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
		));
		$elements[] = "userParameterKey_{$this->userParametersCount}";
	
		if($parameter instanceof Kaltura_Client_EventNotification_Type_EventNotificationArrayParameter)
		{
			$values = array();
			foreach($parameter->values as $value)
			{
				/* @var $value Kaltura_Client_Type_String */
				$values[] = $value->value;
			}
			
			foreach($parameter->allowedValues as $index => $allowedValue)
			{
				/* @var $allowedValue Kaltura_Client_Type_StringValue */
				
				$this->addElement('checkbox', "userParameterItem_{$this->userParametersCount}_$index", array(
					'label'			=> $allowedValue->description,
					'checkedValue'	=> $allowedValue->value,
					'checked'		=> in_array($allowedValue->value, $values),
					'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt'))),
				));
				$elements[] = "userParameterItem_{$this->userParametersCount}_$index";
				
				$this->addElement('hidden', "userParameterAllowedValue_{$this->userParametersCount}_$index", array(
					'value'	=> $allowedValue->value,
					'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
				));
				$this->addElement('hidden', "userParameterAllowedDescription_{$this->userParametersCount}_$index", array(
					'value'	=> $allowedValue->description,
					'decorators'	=> array('ViewHelper', array('HtmlTag',  array('tag' => 'span'))),
				));
			}
		}
		else
		{
			$this->addElement('text', "userParameterValue_{$this->userParametersCount}", array(
				'label'			=> 'Value:',
				'value'			=> $parameter->value->value,
				'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
			));
			$elements[] = "userParameterValue_{$this->userParametersCount}"; 
		}
		
		$this->addDisplayGroup($elements, 
			"frmUserParameter_{$this->userParametersCount}", 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => "frmUserParameter_{$this->userParametersCount}"))),
				'legend'		=> 'User Parameter',
		));
		
		$this->userParametersCount++;
	}
}