<?php 
class Form_NewDistributionProfile extends Zend_Form
{
	public function init()
	{
		$this->setAttrib('id', 'addNewDistributionProfile');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		$this->addElement('text', 'newPartnerId', array(
			'label'			=> 'Publisher ID:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('select', 'newProviderType', array(
			'label'			=> 'Provider:',
			'filters'		=> array('StringTrim'),
		));
				
		// submit button
		$this->addElement('button', 'newDistributionProfile', array(
			'label'		=> 'Create New',
			'onclick'		=> "doAction('newDistributionProfile', $('#newPartnerId').val(), $('#newProviderType').val())",
			'decorators'	=> array('ViewHelper'),
		));
	}
	
	public function setProviders(array $providers)
	{
		$element = $this->getElement('newProviderType');
		foreach($providers as $type => $name)
			$element->addMultiOption($type, $name);
	}
}