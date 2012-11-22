<?php
/**
 * @package plugins.httpNotification
 * @subpackage admin
 */ 
class Form_HttpNotificationTemplateConfiguration extends Form_EventNotificationTemplateConfiguration
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
		
		if($object instanceof Kaltura_Client_HttpNotification_Type_HttpNotificationTemplate)
		{
			KalturaLog::debug("Search properties [" . print_r($properties, true) . "]");
			
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
					
					$contentParameter = new Kaltura_Client_EventNotification_Type_EventNotificationParameter();
					$contentParameter->key = $value;
					$contentParameter->value = $field;
					
					$contentParameters[] = $contentParameter;
				}
				
				KalturaLog::debug("Set content parameters [" . print_r($contentParameters, true) . "]");
				if(count($contentParameters))
					$object->contentParameters = $contentParameters;
			}
			
			if(!isset($properties['dataType']) || !$properties['dataType'])
				return $object;
			
			switch ($properties['dataType'])
			{
				case 'object':
					$objectField = new Kaltura_Client_HttpNotification_Type_HttpNotificationObjectField();
					$objectField->apiObjectType = $properties['objectType'];
					$objectField->format = $properties['objectFormat'];
					$objectField->code = $properties['object'];
					
					$object->data = new Kaltura_Client_HttpNotification_Type_HttpNotificationDataText();
					$object->data->content = $objectField;
					break;
					
				case 'map':
					$object->data = new Kaltura_Client_HttpNotification_Type_HttpNotificationDataFields();
					break;
					
				case 'text':
					$stringField = new Kaltura_Client_Type_StringValue();
					$stringField->value = $properties['freeText'];
					
					$object->data = new Kaltura_Client_HttpNotification_Type_HttpNotificationDataText();
					$object->data->content = $stringField;
					break;
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
		
		if(!($object instanceof Kaltura_Client_HttpNotification_Type_HttpNotificationTemplate))
			return;
		
		foreach($object->contentParameters as $parameter)
			$this->addContentParameter($parameter);
			
		if(!$object->data)
			return;
			
		if($object->data instanceof Kaltura_Client_HttpNotification_Type_HttpNotificationDataFields)
		{
			$this->getElement('dataType')->setValue('map');
		}
				
		if($object->data instanceof Kaltura_Client_HttpNotification_Type_HttpNotificationDataText)
		{
			if($object->data->content && $object->data->content instanceof Kaltura_Client_HttpNotification_Type_HttpNotificationObjectField)
			{
				$this->getElement('dataType')->setValue('object');
				$this->getElement('objectType')->setValue($object->data->content->apiObjectType);
				$this->getElement('objectFormat')->setValue($object->data->content->format);
				$this->getElement('object')->setValue($object->data->content->code);
			}
			else
			{
				$this->getElement('dataType')->setValue('text');
				$this->getElement('freeText')->setValue($object->data->content->value);
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see Form_EventNotificationTemplateConfiguration::addTypeElements()
	 */
	protected function addTypeElements()
	{
		$this->addElement('text', 'url', array(
			'label'			=> 'URL:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('select', 'method', array(
			'label'			=> 'Method:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
			'multiOptions' 	=> array(
				Kaltura_Client_HttpNotification_Enum_HttpNotificationMethod::POST => 'POST',
				Kaltura_Client_HttpNotification_Enum_HttpNotificationMethod::GET => 'GET',
				Kaltura_Client_HttpNotification_Enum_HttpNotificationMethod::PUT => 'PUT',
				Kaltura_Client_HttpNotification_Enum_HttpNotificationMethod::DELETE => 'DELETE',
			),
		));
	
		$this->addElement('select', 'dataType', array(
			'label'			=> 'Data Type:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
			'multiOptions' 	=> array(
				'' => 'Select Data Type',
				'object' => 'API Object',
				'map' => 'Fields',
				'text' => 'Free Text',
			),
		));
		
		$this->addElement('text', 'object', array(
			'label'			=> 'Object:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
		));
		
		$this->addElement('text', 'objectType', array(
			'label'			=> 'Object Type (KalturaObject):',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('select', 'objectFormat', array(
			'label'			=> 'Format:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
			'multiOptions' 	=> array(
				Kaltura_Client_Enum_ResponseType::RESPONSE_TYPE_JSON => 'JSON',
				Kaltura_Client_Enum_ResponseType::RESPONSE_TYPE_XML => 'XML',
				Kaltura_Client_Enum_ResponseType::RESPONSE_TYPE_PHP => 'PHP',
			),
		));
			
		$this->addDisplayGroup(array('object', 'objectType', 'objectFormat'), 
			'frmObject', 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'style' => 'display: none', 'id' => 'frmObject'))),
				'legend'		=> 'New content parameter',
		));
		
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
			
		$this->addElement('textarea', 'freeText', array(
			'label'			=> 'Text:',
			'filters'		=> array('StringTrim'),
		));
			
		$this->addDisplayGroup(array('freeText'), 
			'frmFreeText', 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'style' => 'display: none', 'id' => 'frmFreeText'))),
				'legend'		=> 'New content parameter',
		));
		
		$this->addDisplayGroup(array('contentParameterKey', 'contentParameterValue', 'removeContentParameterButton'), 
			'frmContentParameter', 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'style' => 'display: none', 'id' => 'frmContentParameter'))),
				'legend'		=> 'New content parameter',
		));
			
		$this->addDisplayGroup(array('addContentParameterButton'), 
			'frmParameters', 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'style' => 'display: none', 'id' => 'frmParameters'))),
				'legend'		=> 'New content parameter',
		));
	}
	
	/**
	 * @param Kaltura_Client_HttpNotification_Type_HttpNotificationParameter $parameter
	 */
	protected function addContentParameter(Kaltura_Client_HttpNotification_Type_HttpNotificationParameter $parameter)
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