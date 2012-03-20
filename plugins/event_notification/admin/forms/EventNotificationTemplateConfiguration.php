<?php 
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
	protected $conditionsCount = 0;
	
	public function __construct($partnerId, $templateType)
	{
		$this->partnerId = $partnerId;
		$this->templateType = $templateType;
		
		parent::__construct();
	}
	
	abstract protected function addTypeElements();
	
	/* (non-PHPdoc)
	 * @see Infra_Form::populateFromObject()
	 */
	public function populateFromObject($object, $add_underscore = true)
	{
		if(!($object instanceof Kaltura_Client_EventNotification_Type_EventNotificationTemplate))
			return;
			
		parent::populateFromObject($object, $add_underscore);
		
		foreach($object->eventConditions as $condition)
			$this->addCondition($condition);
			
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
			
			$conditions = $object->eventConditions;
			if(!$conditions || !is_array($conditions))
				$conditions = array();
				
			foreach($properties as $property => $value)
			{
				if(preg_match('/condition_(\d+)$/', $property))
				{
					$field = new Kaltura_Client_Type_EvalBooleanField();
					$field->code = $value;
					
					$condition = new Kaltura_Client_EventNotification_Type_EventFieldCondition();
					$condition->field = $field;
					
					$conditions[] = $condition;
				}
			}
			
			if(isset($properties['condition']))
			{
				foreach($properties['condition'] as $value)
				{
					$field = new Kaltura_Client_Type_EvalBooleanField();
					$field->code = $value;
					
					$condition = new Kaltura_Client_EventNotification_Type_EventFieldCondition();
					$condition->field = $field;
					
					$conditions[] = $condition;
				}
				
				KalturaLog::debug("Set conditions [" . print_r($conditions, true) . "]");
				if(count($conditions))
					$object->eventConditions = $conditions;
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
			'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt'))),
		));
		
		$this->addElement('checkbox', 'automatic_dispatch_enabled', array(
			'label'			=> 'Automatic dispatch enabled:',
			'decorators' 	=> array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt'))),
			'onclick'		=> 'automaticEnabled(this.checked)',
		));
		
		$this->addElement('select', 'event_type', array(
			'label'			=> 'Event type:',
		));
		
		$this->addElement('select', 'event_object_type', array(
			'label'			=> 'Event object type:',
		));
		
		$this->addElement('button', 'addConditionButton', array(
			'label'			=> 'Add Condition',
			'onclick'		=> "newCondition()",
			'decorators'	=> array('ViewHelper'),
		));
		
		$this->addDisplayGroup(array('event_type', 'event_object_type', 'addConditionButton'), 
			'automatic_config', 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => 'frmAutomaticConfig'))),
				'legend'		=> 'Automatic dispatch configuration',
		));
		
		$this->addElement('text', 'condition', array(
			'label'			=> 'Condition:',
			'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
		));
		
		$this->addElement('button', 'removeConditionButton', array(
			'label'			=> 'Remove',
			'decorators'	=> array('ViewHelper'),
		));
			
		$this->addDisplayGroup(array('condition', 'removeConditionButton'), 
			'frmCondition', 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'style' => 'display: none', 'id' => 'frmCondition'))),
				'legend'		=> 'New condition',
		));
		
		$this->addElement('hidden', 'type', array(
			'value'			=> $this->templateType,
		));
	
		$element = $this->getElement('event_type');
		$reflect = new ReflectionClass('Kaltura_Client_EventNotification_Enum_EventNotificationEventType');
		$types = $reflect->getConstants();
		foreach($types as $constName => $value)
		{
			$name = ucfirst(str_replace('_', ' ', strtolower($constName)));
			$element->addMultiOption($value, $name);
		}
	
		$element = $this->getElement('event_object_type');
		$reflect = new ReflectionClass('Kaltura_Client_EventNotification_Enum_EventNotificationEventObjectType');
		$types = $reflect->getConstants();
		foreach($types as $constName => $value)
		{
			$name = ucfirst(str_replace('_', ' ', strtolower($constName)));
			$element->addMultiOption($value, $name);
		}
	}
	
	/**
	 * @param Kaltura_Client_EventNotification_Type_EventCondition $condition
	 */
	protected function addCondition(Kaltura_Client_EventNotification_Type_EventCondition $condition)
	{
		if($condition instanceof Kaltura_Client_EventNotification_Type_EventFieldCondition && $condition->field instanceof Kaltura_Client_Type_EvalBooleanField)
		{
			$this->addElement('text', "condition_{$this->conditionsCount}", array(
				'label'			=> 'Condition:',
				'value'			=> $condition->field->code,
				'decorators'	=> array('ViewHelper', array('Label', array('placement' => 'prepend'))),
			));
			
			$this->addElement('button', "removeConditionButton_{$this->conditionsCount}", array(
				'label'			=> 'Remove',
				'onclick'			=> "removeCondition({$this->conditionsCount})",
				'decorators'	=> array('ViewHelper'),
			));
				
			$this->addDisplayGroup(array("condition_{$this->conditionsCount}", "removeConditionButton_{$this->conditionsCount}"), 
				"frmCondition_{$this->conditionsCount}", 
				array(
					'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'id' => "frmCondition_{$this->conditionsCount}"))),
					'legend'		=> 'Code Field Condition',
			));
			
			$this->conditionsCount++;
		}
		else
		{
			$this->addError("Unable to load condition type [" . get_class($condition) . "]");
		}
	}
}