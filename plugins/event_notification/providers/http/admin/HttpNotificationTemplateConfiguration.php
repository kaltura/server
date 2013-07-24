<?php
/**
 * @package plugins.httpNotification
 * @subpackage admin
 */ 
class Form_HttpNotificationTemplateConfiguration extends Form_EventNotificationTemplateConfiguration
{
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
			
			if(!isset($properties['dataType']) || !$properties['dataType'])
				return $object;
			
			switch ($properties['dataType'])
			{
				case 'object':
					$object->data = new Kaltura_Client_HttpNotification_Type_HttpNotificationObjectData();
					$object->data->apiObjectType = $properties['objectType'];
					$object->data->format = $properties['objectFormat'];
					$object->data->code = $properties['object'];
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
		
		if(!$object->data)
			return;
			
		if($object->data instanceof Kaltura_Client_HttpNotification_Type_HttpNotificationDataFields)
		{
			$this->getElement('dataType')->setValue('map');
		}
		elseif($object->data instanceof Kaltura_Client_HttpNotification_Type_HttpNotificationDataText)
		{
			$this->getElement('dataType')->setValue('text');
			$this->getElement('freeText')->setValue($object->data->content->value);
		}
		elseif($object->data instanceof Kaltura_Client_HttpNotification_Type_HttpNotificationObjectData)
		{
			$this->getElement('dataType')->setValue('object');
			$this->getElement('objectType')->setValue($object->data->apiObjectType);
			$this->getElement('objectFormat')->setValue($object->data->format);
			$this->getElement('object')->setValue($object->data->code);
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
			'readonly'		=> true,
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
		));
		
		$this->addElement('textarea', 'freeText', array(
			'label'			=> 'Text:',
			'filters'		=> array('StringTrim'),
		));
			
		$this->addDisplayGroup(array('freeText'), 
			'frmFreeText', 
			array(
				'decorators' 	=> array('FormElements', 'Fieldset', array('HtmlTag', array('tag' => 'div', 'style' => 'display: none', 'id' => 'frmFreeText'))),
		));
	}
}