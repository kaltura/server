<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_Partner_BaseStorageConfiguration extends Infra_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmStorageConfig');
		
		$this->addElement('text', 'storageId', array(
				'label' 		=> 'Storage ID:',
				'filters' 		=> array('StringTrim'),
				'validators' 	=> array(),
				'readonly'		=> true,
		));

		$this->addElement('text', 'partnerId', array(
			'label' 		=> 'Related Publisher ID*:',
			'required'		=> true,
			'filters' 		=> array('StringTrim'),
			'validators' 	=> array(),
		));
		
		$this->addElement('text', 'name', array(
			'label' 		=> 'Remote Storage Name*:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'systemName', array(
			'label' 		=> 'System Name:',
			'filters'		=> array('StringTrim'),
		));
		
		 
		$deliveryStatus = new Kaltura_Form_Element_EnumSelect('deliveryStatus', array('enum' => 'Kaltura_Client_Enum_StorageProfileDeliveryStatus'));
		$deliveryStatus->setLabel('Delivery Status:');
		$this->addElements(array($deliveryStatus));	
		
		$this->addElement('text', 'deliveryPriority', array(
			'label' 		=> 'Delivery Priority:',
			'required'		=> false,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('textarea', 'desciption', array(
			'label'			=> 'Description:',
			'cols'			=> 60,
			'rows'			=> 3,
			'filters'		=> array('StringTrim'),
		));
		 
		 
		$this->addElement('text', 'storageUrl', array(
			'label'			=> 'Storage URL*:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'allowAutoDelete', array(
			'label'			=> 'Allow auto-deletion of files:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'minFileSize', array(
			'label'			=> 'Export only files bigger than:',
			'filters'		=> array('Digits'),
			
		));
		 
		$this->addElement('text', 'maxFileSize', array(
			'label'			=> 'Export only files smaller than:',
			'filters'		=> array('Digits'),
		));
		
		$this->addElement('select', 'trigger', array(
			'label'			=> 'Trigger:',
			'filters'		=> array('StringTrim'),
			'multiOptions'  => array(3 => 'Flavor Ready',
									2 => 'Moderation Approved',
							  ),	
		));
		
		$readyBehavior = new Kaltura_Form_Element_EnumSelect('readyBehavior', array('enum' => 'Kaltura_Client_Enum_StorageProfileReadyBehavior'));
		$readyBehavior->setLabel('Ready Behavior:');
		$this->addElements(array($readyBehavior));
		
		$this->addDisplayGroup(array('storageId', 'partnerId', 'name', 'systemName', 'deliveryStatus', 'deliveryPriority', 'desciption'), 'general_info', array(
			'legend' => 'General',
		));
		
		$this->addElement('hidden', 'crossLine1', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		
		$this->addDisplayGroup(array('storageUrl', 'allowAutoDelete'), 'storage_info', array(
			'legend' => 'Export Details',

		));
		
		$this->addElement('hidden', 'crossLine2', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$this->addDisplayGroup(array('minFileSize', 'maxFileSize', 'trigger', 'readyBehavior'), 'export_policy', array(
			'legend' => 'Export Policy',

		));
				
		$this->addElement('hidden', 'crossLine3', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$this->addElement('text', 'delivery_profile_ids', array(
				'label'			=> "Delivery profile ids (JSON)",
				'filters'		=> array('StringTrim'),
				'readonly'		=> true,
		));
		
		$element = $this->addElement('select', 'deliveryFormat', array(
				'filters'		=> array('StringTrim'),
				'registerInArrayValidator' => false,
		));
		
		$this->addElement('button', 'editDeliveryProfiles', array(
				'label'		=> 'Add',
				'decorators'	=> array('ViewHelper'),
		));
		
		$this->getElement('editDeliveryProfiles')->setAttrib('onClick', 'addDeliveryProfile()');

		$this->addElement('checkbox', 'exportPeriodically', array(
			'label'			=> 'Export all assets periodically:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'pathPrefix', array(
			'label'			=> 'Delivery path prefix:',
			'filters'		=> array('StringTrim'),
		));

		$this->addDisplayGroup ( array ('exportPeriodically', 'pathPrefix', 'delivery_profile_ids', 'deliveryFormat', 'editDeliveryProfiles' ), 'playback_info', array ('legend' => 'Delivery Details' ) );

		$this->addElement('hidden', 'crossLine4', array(
				'lable'			=> 'line',
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$displayGroups = $this->getDisplayGroups();
		foreach ($displayGroups as $displayGroup)
		{
			$displayGroup->removeDecorator ('label');
	  		$displayGroup->removeDecorator('DtDdWrapper');
		}
		
		$element = new Infra_Form_Html ( 'place_holder1', array ('content' => '<span/>' ) );
		$this->addElement ( $element );
		$this->addDisplayGroup ( array ('place_holder1' ), 'advanced', array ('legend' => 'Advanced' ) );
		
		$openLeftDisplayGroup = $this->getDisplayGroup('general_info');
		$openLeftDisplayGroup->setDecorators(array(
				'FormElements',
				'Fieldset',
				array('HtmlTag',array('tag'=>'div','openOnly'=>true,'class'=> 'storageConfigureFormPanel'))
		));
		
    	$closeLeftDisplayGroup = $this->getDisplayGroup('advanced');
    	$closeLeftDisplayGroup->setDecorators(array(
             'FormElements',
             'Fieldset',
              array('HtmlTag',array('tag'=>'div','closeOnly'=>true))
     	));
    	
	}

	public function addFlavorParamsFields(Kaltura_Client_Type_FlavorParamsListResponse $flavorParams, array $selectedFlavorParams = array())
	{
		$flavorParamsElementNames = array();
		foreach($flavorParams->objects as $index => $flavorParamsItem)
		{
			$checked = in_array($flavorParamsItem->id, $selectedFlavorParams);
			$this->addElement('checkbox', 'flavorParamsId_' . $flavorParamsItem->id, array(
				'label'			=> "Flavor Params {$flavorParamsItem->name} ({$flavorParamsItem->id})",
				'checked'		=> $checked,
			    'indicator'		=> 'dynamic',
				'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt')))
			));
			
			$this->addElementToDisplayGroup('advanced', 'flavorParamsId_' . $flavorParamsItem->id);
		}

	}
	
	protected function insertObject(&$res, $key, $value) {
		if(strpos($key, ".") === FALSE) {
			$res[$key] = intval($value);
			return;
		}
		
		list($key, $newKey) = explode(".", $key, 2);
		if(!array_key_exists($key, $res))
			$res[$key] = array();
		$this->insertObject($res[$key], $newKey, $value);
	}
	
	public function populateFromObject($object, $add_underscore = true) {
		
		parent::populateFromObject($object, $add_underscore);
		
		if(empty($object->deliveryProfileIds)) {
			$this->getElement('delivery_profile_ids')->setValue("{}");
		} else {
			$res = array();
			foreach($object->deliveryProfileIds as $keyValue) {
				$this->insertObject($res, $keyValue->key, $keyValue->value);
			}
			$this->getElement('delivery_profile_ids')->setValue(json_encode($res));
		}
		
	}
	
	protected function toKeyValue(array $pairs, $prefix = '')
	{
		$res = array();		
		foreach($pairs as $key => $value)
		{
			if(is_array($value))
			{
				$res = array_merge($res, $this->toKeyValue($value, "$key."));
				continue;
			}
				
			$pairObject = new Kaltura_Client_Type_KeyValue();
			$pairObject->key = $prefix . $key;
			$pairObject->value = $value;
			$res[] = $pairObject;
		}
		return $res;
	}
	
	// Creates the KalturaStorageProfile
	public function loadObject($object, array $properties, $add_underscore = true, $include_empty_fields = false) {
		$object = parent::loadObject($object, $properties, $add_underscore, $include_empty_fields);
		
		// Input is json, output is key-value array
		$deliveryProfileIds = $this->getElement('delivery_profile_ids')->getValue();
		if(!empty($deliveryProfileIds))
			$object->deliveryProfileIds = $this->toKeyValue(json_decode($deliveryProfileIds, true));
		
		return $object;
	}
}
