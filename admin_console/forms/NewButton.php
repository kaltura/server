<?php
/**
 * @package Admin
 * @subpackage Partners
 */
class Form_NewButton extends Infra_Form
{
	protected $showPartnerId = true;
	
    /**
     * @param array $options
     * @see Zend_Form::__construct()
     */
    public function __construct($options = null)
    {
    	if(isset($options['showPartnerId']))
    		$this->showPartnerId = $options['showPartnerId'];
    		
    	parent::__construct($options);
    }
    
	public function init()
	{
		$this->setAttrib('id', 'addNewForm');
		$this->setDecorators(array(
			'FormElements',
			array('HtmlTag', array('tag' => 'fieldset')),
			array('Form', array('class' => 'simple')),
		));
		
		if($this->showPartnerId)
		{
			$this->addElement('text', 'newPartnerId', array(
				'label'			=> 'Publisher ID:',
				'filters'		=> array('StringTrim'),
				'value'			=> $this->byid,
			));
		}
		else
		{
			$this->addElement('hidden', 'newPartnerId', array(
				'value'			=> 0,
			));
		}
		
		// submit button
		$this->addElement('button', 'new_button', array(
			'label'		=> 'Create New',
			'decorators'	=> array('ViewHelper'),
			'onclick'		=> "doAction('newForm', $('#newPartnerId').val())",
		));
	}
}