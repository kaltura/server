<?php 
/**
 * @package plugins.msnDistribution
 * @subpackage admin
 */
class Form_MsnProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	/**
	 * @var Kaltura_Client_MsnDistribution_Type_MsnDistributionProfile
	 */
	private $msnDistributionProfile;
	
	public function init()
	{
		parent::init();
		$this->setDescription('MSN Distribution Profile');
		
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'msn-form.phtml',
			'placement' => 'APPEND'
		));
	}
	
	public function isValid($data)
	{
		$valid = parent::isValid($data);
		$flavors = array('source_flavor_params_id', 'wmv_flavor_params_id', 'wmv_flavor_params_id', 'flv_flavor_params_id', 'sl_flavor_params_id', 'sl_hd_flavor_params_id');
		
		$found = false;
		foreach($flavors as $flavor)
		{
			if (isset($data[$flavor]) && $data[$flavor] != -1)
			{
				$found = true;
			}
		}
		if (!$found)
		{
			// the exception will get catched "DistributionProfileConfigureAction"
			throw new Exception('At least one flavor should be selected');
		}
		
		return $valid;
	}
	
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, true);
		
		/* @var $object Kaltura_Client_MsnDistribution_Type_MsnDistributionProfile */
		$requiredFlavorParamsIds = array();
		if ($object->sourceFlavorParamsId != -1)
			$requiredFlavorParamsIds[] = $object->sourceFlavorParamsId;

		if ($object->flvFlavorParamsId != -1)
			$requiredFlavorParamsIds[] = $object->flvFlavorParamsId;

		if ($object->wmvFlavorParamsId != -1)
			$requiredFlavorParamsIds[] = $object->wmvFlavorParamsId;

		if ($object->slFlavorParamsId != -1)
			$requiredFlavorParamsIds[] = $object->slFlavorParamsId;

		if ($object->slHdFlavorParamsId != -1)
			$requiredFlavorParamsIds[] = $object->slHdFlavorParamsId;
		
		$object->requiredFlavorParamsIds = implode(',', $requiredFlavorParamsIds);
		return $object;
	}
	
	public function addFlavorParamsFields(Kaltura_Client_Type_FlavorParamsListResponse $flavorParams, array $optionalFlavorParamsIds = array(), array $requiredFlavorParamsIds = array())
	{
		$this->addFlavorsSelect($flavorParams, 'source_flavor_params_id', 'Source (1001)');
		$this->addFlavorsSelect($flavorParams, 'wmv_flavor_params_id', 'WMV video file (1002)');
		$this->addFlavorsSelect($flavorParams, 'flv_flavor_params_id', 'Flash video file (1003)');
		$this->addFlavorsSelect($flavorParams, 'sl_flavor_params_id', 'SilverLight Smooth Steaming (1004)');
		$this->addFlavorsSelect($flavorParams, 'sl_hd_flavor_params_id', 'HD Silverlight Smooth Streaming (1005)');
		
		$this->addDisplayGroup(
				array('source_flavor_params_id', 'wmv_flavor_params_id', 'wmv_flavor_params_id', 'flv_flavor_params_id', 'sl_flavor_params_id', 'sl_hd_flavor_params_id'), 
				'flavors_config', 
				array('legend' => 'Flavor Params', 'decorators' => array('FormElements', 'Fieldset'))
			);
			
		// repopulate the object
		if ($this->msnDistributionProfile)
			parent::populateFromObject($this->msnDistributionProfile);
	}
	
	public function populateFromObject($object, $add_underscore = true)
	{
		// save the profile on the form instance because addFlavorParamsFields is called after populateFromObject
		$this->msnDistributionProfile = $object;
		parent::populateFromObject($object, $add_underscore);
	}
	
	protected function addFlavorsSelect(Kaltura_Client_Type_FlavorParamsListResponse $flavorParams, $name, $label)
	{
		$element = new Zend_Form_Element_Select($name);
		$element->setLabel($label);
		$element->addMultiOption(-1, '');
		foreach($flavorParams->objects as $flavorParams)
		{
			$element->addMultiOption($flavorParams->id, $flavorParams->name);
		}
		$this->addElement($element);
	}
	
	protected function addProviderElements()
	{
		$this->setDescription(null);
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('MSN Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$this->addElement('text', 'username', array(
			'label'			=> 'Username:',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'password', array(
			'label'			=> 'Password:',
		'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'domain', array(
			'label'			=> 'Domain:',
			'required'		=> true,
			'value'			=> 'catalog.video.msn.com',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addDisplayGroup(
			array('username', 'password', 'domain'), 
			'apiconfig', 
			array('legend' => 'API Configuration', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$this->addElement('text', 'cs_id', array(
			'label'			=> 'CS ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'source', array(
			'label'			=> 'Source:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'source_friendly_name', array(
			'label'			=> 'Source Friendly Name:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'page_group', array(
			'label'			=> 'Page Group:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addDisplayGroup(
			array('cs_id', 'source', 'source_friendly_name', 'page_group'), 
			'general', 
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$this->addElement('text', 'msnvideo_cat', array(
			'label'			=> 'MSNVideo_Cat:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'msnvideo_top', array(
			'label'			=> 'MSNVideo_Top:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'msnvideo_top_cat', array(
			'label'			=> 'MSNVideo_Top_Cat:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addDisplayGroup(
			array('msnvideo_cat', 'msnvideo_top', 'msnvideo_top_cat'), 
			'default_metadata', 
			array('legend' => 'Default Metadata', 'decorators' => array('FormElements', 'Fieldset'))
		);
	}
}