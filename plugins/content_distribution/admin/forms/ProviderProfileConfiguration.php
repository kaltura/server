<?php 
abstract class Form_ProviderProfileConfiguration extends Form_DistributionConfiguration
{
	protected $partnerId;
	protected $providerType;
	
	public function __construct($partnerId, $providerType)
	{
		$this->partnerId = $partnerId;
		$this->providerType = $providerType;
		
		parent::__construct();
	}
	
	public function saveProviderAdditionalObjects(Kaltura_Client_ContentDistribution_Type_DistributionProfile $distributionProfile)
	{
		// called after the profile element is saved
	}
	
	abstract protected function addProviderElements();
	
	public function resetUnUpdatebleAttributes(Kaltura_Client_ContentDistribution_Type_DistributionProfile $distributionProfile)
	{
		// reset readonly attributes
		$distributionProfile->id = null;
		$distributionProfile->partnerId = null;
		$distributionProfile->createdAt = null;
		$distributionProfile->updatedAt = null;
		$distributionProfile->providerType = null;
	}
	
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmDistributionProfileConfig');

		$this->setDescription('provider-profile-configure intro text');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));

		$this->addElement('text', 'name', array(
			'label'			=> 'Name:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'partner_id', array(
			'label'			=> 'Publisher ID:',
			'value'			=> $this->partnerId,
			'readonly'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('hidden', 'provider_type', array(
			'value'			=> $this->providerType,
		));
		
		$this->addElement('hidden', 'crossLine01', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
//		TODO - redefine the UI
//		
//		$this->addElement('text', 'sunrise_default_offset', array(
//			'label'			=> 'Sunrise Default Offset (seconds since entry creation):',
//			'filters'		=> array('StringTrim'),
//		));
//		
//		$this->addElement('text', 'sunset_default_offset', array(
//			'label'			=> 'Sunset Default Offset (seconds since entry creation):',
//			'filters'		=> array('StringTrim'),
//		));
//		
//		$this->addElement('hidden', 'crossLine02', array(
//			'lable'			=> 'line',
//			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
//		));
		
		$this->addProviderElements();
		$this->addProfileAction('submit');
		$this->addProfileAction('update');
		$this->addProfileAction('delete');
		$this->addProfileAction('report');
	}
	
	/**
	 * @param string $action
	 * @return Zend_Form_DisplayGroup
	 */
	protected function addProfileAction($action)
	{
		$this->addElement('select', "{$action}_enabled", array(
			'label'	  =>  'Enabled',
			'onchange'		=> "actionEnabledChanged('$action')",
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt', 'class' => "action-enabled $action-enabled")))
		));
		
		$element = $this->getElement("{$action}_enabled");
		$element->addMultiOption(Kaltura_Client_ContentDistribution_Enum_DistributionProfileActionStatus::DISABLED, 'Disabled');
		$element->addMultiOption(Kaltura_Client_ContentDistribution_Enum_DistributionProfileActionStatus::MANUAL, 'Manual');
		$element->addMultiOption(Kaltura_Client_ContentDistribution_Enum_DistributionProfileActionStatus::AUTOMATIC, 'Automatic');
			
		$this->addDisplayGroup(
			array(
				"{$action}_enabled", 
			), 
			"{$action}_action_group",
			array(
				'legend' => ucfirst($action) . ' Action',
				'decorators' => array('FormElements', 'Fieldset', array('HtmlTag', array('class' => "{$action}-action-group"))),
			)
		);
		
		return $this->getDisplayGroup("{$action}_action_group");
	}
	
	protected function addMetadataProfile($enableNone = false)
	{
		$metadataProfiles = null;
		try
		{
			$metadataProfileFilter = new Kaltura_Client_Metadata_Type_MetadataProfileFilter();
			$metadataProfileFilter->metadataObjectTypeEqual = Kaltura_Client_Metadata_Enum_MetadataObjectType::ENTRY;
			
			$client = Infra_ClientHelper::getClient();
			$metadataPlugin = Kaltura_Client_Metadata_Plugin::get($client);
			Infra_ClientHelper::impersonate($this->partnerId);
			$metadataProfileList = $metadataPlugin->metadataProfile->listAction($metadataProfileFilter);
			Infra_ClientHelper::unimpersonate();
			
			$metadataProfiles = $metadataProfileList->objects;
		}
		catch (Kaltura_Client_Exception $e)
		{
			$metadataProfiles = null;
		}
		
		if(count($metadataProfiles))
		{
			$this->addElement('select', 'metadata_profile_id', array(
				'label'			=> 'Metadata Profile ID:',
				'filters'		=> array('StringTrim'),
			));
			
			$element = $this->getElement('metadata_profile_id');
			
			if($enableNone)
				$element->addMultiOption('', 'None');
			
			foreach($metadataProfiles as $metadataProfile)
				$element->addMultiOption($metadataProfile->id, $metadataProfile->name);
		}
		else 
		{
			$this->addElement('hidden', 'metadata_profile_id', array(
				'value'			=> 0,
			));
		}
	}
}