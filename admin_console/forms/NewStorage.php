<?php 
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_NewStorage extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'addNewStorage');
		$this->setDecorators(array(
			'FormElements', 
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		$this->addElement('text', 'newPartnerId', array(
			'label'			=> 'Publisher ID:',
			'filters'		=> array('StringTrim'),
			'value'         => $this->filer_input,
		));
		
		$this->addElement('select', 'newProtocolType', array(
			'label'			=> 'Protocol:',
			'filters'		=> array('StringTrim'),
		));
		
		// submit button
		$this->addElement('button', 'newStorage', array(
			'label'		=> 'Create New',
			'onclick'		=> "doAction('newStorage', $('#newPartnerId').val(), $('#newProtocolType').val())",
			'decorators'	=> array('ViewHelper'),
		));
		
		$element = $this->getElement('newProtocolType');
		$reflect = new ReflectionClass('Kaltura_Client_Enum_StorageProfileProtocol');
		$types = $reflect->getConstants();
		foreach($types as $constName => $value)
		{
			$name = ucfirst(str_replace('_', ' ', $constName));
			$element->addMultiOption($value, $name);
		}
	}
	
}