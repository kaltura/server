<?php 
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_NewDeliveryProfile extends Infra_Form
{
	public function init()
	{
		$this->setAttrib('id', 'addNewDeliveryProfile');
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
		
		$newType =
			new Kaltura_Form_Element_EnumSelect(
					'newType',
					array(
						'enum' => 'Kaltura_Client_Enum_DeliveryProfileType',
					)
				);
		$newType->setLabel('Type:');
		$this->addElements(array($newType));

		// submit button
		$this->addElement('button', 'newDeliveryProfile', array(
			'label'		=> 'Create New',
			'onclick'		=> "doAction('newDeliveryProfile', $('#newPartnerId').val(), $('#newType').val())",
			'decorators'	=> array('ViewHelper'),
		));
	}
	
}