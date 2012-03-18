<?php 
abstract class Form_EventNotificationTemplateConfiguration extends Infra_Form
{
	protected $partnerId;
	protected $templateType;
	
	public function __construct($partnerId, $templateType)
	{
		$this->partnerId = $partnerId;
		$this->templateType = $templateType;
		
		parent::__construct();
	}
	
	abstract protected function addTypeElements();
	
	public function resetUnUpdatebleAttributes(Kaltura_Client_EventNotification_Type_EventNotificationTemplate $eventNotificationTemplate)
	{
		// reset readonly attributes
		$eventNotificationTemplate->id = null;
		$eventNotificationTemplate->partnerId = null;
		$eventNotificationTemplate->createdAt = null;
		$eventNotificationTemplate->updatedAt = null;
		$eventNotificationTemplate->type = null;
		$eventNotificationTemplate->status = null;
	}
	
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setAttrib('id', 'frmEventNotificationTemplateConfig');

		$this->setDescription('event notification templates configure intro text');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));

		$this->addElement('text', 'name', array(
			'label'			=> 'Name:',
			'filters'		=> array('StringTrim'),
			'required'		=> true,
		));
		
		$this->addElement('text', 'system_name', array(
			'label'			=> 'System name:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'description', array(
			'label'			=> 'Description:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'partner_id', array(
			'label'			=> 'Publisher ID:',
			'value'			=> $this->partnerId,
			'readonly'		=> true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('checkbox', 'manual_dispatch_enabled', array(
			'label'			=> 'Manual dispatch enabled:',
		));
		
		$this->addElement('checkbox', 'automatic_dispatch_enabled', array(
			'label'			=> 'Automatic dispatch enabled:',
		));
		
		$this->addElement('hidden', 'type', array(
			'value'			=> $this->templateType,
		));
		
		$this->addElement('hidden', 'crossLine01', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$this->addTypeElements();
	}
}