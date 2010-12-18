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
	
	abstract protected function addProviderElements();
	
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
	}
}