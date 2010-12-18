<?php 
class Form_GenericProviderProfileConfiguration extends Form_ProviderProfileConfiguration
{
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Generic Provider Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$client = Kaltura_ClientHelper::getClient();
		Kaltura_ClientHelper::impersonate($this->partnerId);
		$genericDistributionProviderList = $client->genericDistributionProvider->listAction();
		Kaltura_ClientHelper::unimpersonate();
		
		$this->addElement('select', 'generic_provider_id', array(
			'label'	  =>  'Provider',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'prepend')), array('HtmlTag',  array('tag' => 'dt')))
		));
		
		$element = $this->getElement('generic_provider_id');
		
		if($genericDistributionProviderList && $genericDistributionProviderList->totalCount)
		{
			foreach($genericDistributionProviderList->objects as $genericDistributionProvider)
				$element->addMultiOption($genericDistributionProvider->id, $genericDistributionProvider->name);
		}
	}
}