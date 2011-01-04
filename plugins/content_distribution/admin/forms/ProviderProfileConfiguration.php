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
	
	public function saveProviderAdditionalObjects(KalturaDistributionProfile $distributionProfile)
	{
		// called after the profile element is saved
	}
	
	abstract protected function addProviderElements();
	
	public function resetUnUpdatebleAttributes(KalturaDistributionProfile $distributionProfile)
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
		
		$this->addElement('hidden', 'crossLine1', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
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
		$element->addMultiOption(KalturaDistributionProfileActionStatus::DISABLED, 'Disabled');
		$element->addMultiOption(KalturaDistributionProfileActionStatus::MANUAL, 'Manual');
		$element->addMultiOption(KalturaDistributionProfileActionStatus::AUTOMATIC, 'Automatic');
			
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
}