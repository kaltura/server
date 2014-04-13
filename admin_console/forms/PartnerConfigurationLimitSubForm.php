<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_PartnerConfigurationLimitSubForm extends Zend_Form_SubForm
{
	protected $limitType;
	protected $label;
	protected $withOverage;
	protected $requiredPartnerPermissions = array();
		
	public function __construct($limitType, $label, $withOverage = true)
	{
		$this->limitType = $limitType;
		$this->label = $label;
		$this->withOverage = $withOverage;
		parent::__construct();
	}
	
	public function requirePartnerPermission($permissionName)
	{
		$this->requiredPartnerPermissions[] = $permissionName;
	}
	
	public function init()
	{
		$this->addElementsToForm($this);		
	}
	
	public function addElementsToForm($form)
	{
		$form->addElement('hidden', $this->limitType.'_type', array(
			'filters'		=> array('StringTrim'),
			'value' 		=> $this->limitType,
		));
		$form->getElement($this->limitType.'_type')->setBelongsTo($this->limitType);
		$form->getElement($this->limitType.'_type')->removeDecorator('label');

		$form->addElement('text',  $this->limitType.'_max', array(
			'label'			=> $this->label,
			'filters'		=> array('StringTrim'),
			//'decorators'	=> array('Label', 'ViewHelper', array('HtmlTag',array('tag'=>'div','openOnly'=>true, 'class' =>'includeUsage'))),
		));				
		$element = $form->getElement($this->limitType.'_max');
		$element->setBelongsTo($this->limitType);
		
		if($this->withOverage)
		{
			$element->addDecorators(array(
	              'ViewHelper',
	              array('Label'),
	              array(array('row' => 'HtmlTag'), array('tag' => 'div','class'=>'includeUsageFloatLeft')),
			));
			
			$form->addElement('text',  $this->limitType.'_overagePrice', array(
				'label'			=> 'Overage Fee:',
				'filters'		=> array('StringTrim'),
				//'decorators'	=> array('Label', 'ViewHelper', array('HtmlTag',array('tag'=>'div','closeOnly'=>true, 'class' =>'includeUsage'))),
			));
			$element = $form->getElement($this->limitType.'_overagePrice');
			$element->setBelongsTo($this->limitType);
			
			$element->addDecorators(array(
	              'ViewHelper',
	              array('Label'),
	              array(array('row' => 'HtmlTag'), array('tag' => 'div','class'=>'includeUsageFloatRight',)),
			));
			
			$form->addElement('text',  $this->limitType.'_overageUnit', array(
				'label'			=> 'Overage Unit:',
				'filters'		=> array('StringTrim'),
				//'decorators'	=> array('Label', 'ViewHelper', array('HtmlTag',array('tag'=>'div','closeOnly'=>true, 'class' =>'includeUsage'))),
			));
			$element = $form->getElement($this->limitType.'_overageUnit');
			$element->setBelongsTo($this->limitType);
		
			$element->addDecorators(array(
	              'ViewHelper',
	              array('Label'),
	              array(array('row' => 'HtmlTag'), array('tag' => 'div','class'=>'includeUsageFloatRight',)),
			));
		}
	}
	
	public function populateFromObject(Form_PartnerConfiguration $form, Kaltura_Client_SystemPartner_Type_SystemPartnerConfiguration $partnerConfiguration, Kaltura_Client_SystemPartner_Type_SystemPartnerLimit $object, $add_underscore = true)
	{
		$isPermitted = true;
		if(count($this->requiredPartnerPermissions))
		{
			$isPermitted = false;
			$requiredPartnerPermissions = array_flip($this->requiredPartnerPermissions);
			if($partnerConfiguration->permissions && count($partnerConfiguration->permissions))
			{
				foreach($partnerConfiguration->permissions as $permission)
				{
					if(isset($requiredPartnerPermissions[$permission->name]) && $permission->status == Kaltura_Client_Enum_PermissionStatus::ACTIVE)
					{
						unset($requiredPartnerPermissions[$permission->name]);
					}
				}
			}
			if(!count($requiredPartnerPermissions))
			{
				$isPermitted = true;
			}
		}
		
		$props = $object;
		if(is_object($object))
			$props = get_object_vars($object);
			
		foreach($props as $prop => $value)
		{
			if($add_underscore)
			{
				$pattern = '/(.)([A-Z])/'; 
				$replacement = '\1_\2'; 
				$prop = strtolower(preg_replace($pattern, $replacement, $prop));
			}
			$elementName = $this->limitType.'_'.$prop;
			$form->setDefault($elementName, $value);
			
			if(!$isPermitted)
			{
				$element = $form->getElement($elementName);
				$element->setOptions(array('disabled' => true));
			}
		}
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = null;
		if($this->withOverage)
			$object = new Kaltura_Client_SystemPartner_Type_SystemPartnerOveragedLimit();
		else
			$object = new Kaltura_Client_SystemPartner_Type_SystemPartnerLimit();
		
		foreach($properties as $prop => $value)
		{
			if($add_underscore)
			{
				$parts = explode('_', strtolower($prop));
				$prop = '';
				foreach ($parts as $part) 
					$prop .= ucfirst(trim($part));
				$prop[0] = strtolower($prop[0]);
			}

			if ($value !== '' || $include_empty_fields)
			{
				try{
					$objectProp = str_ireplace($this->limitType.'_', '', $prop);
					$object->$objectProp = $value;
				}catch(Exception $e){}
			}
		}
		
		return $object;
	}
	
}