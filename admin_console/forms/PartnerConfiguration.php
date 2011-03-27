<?php 
class Form_PartnerConfiguration extends Kaltura_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');

		$this->setDescription('partner-configure intro text');
		$this->loadDefaultDecorators();
		$this->addDecorator('Description', array('placement' => 'prepend'));

//		$this->addElement('text', 'account_name', array(
//			'label' => 'Publisher Name:',
//			'decorators' 	=> array('Label', 'Description')
//		));
		 
		$this->addElement('text', 'partner_name', array(
			'label'			=> 'Publisher Name:',
			'filters'		=> array('StringTrim'),
		));
		 
		$this->addElement('text', 'description', array(
			'label'			=> 'Description:',
			'filters'		=> array('StringTrim'),
		));
		
		// change to read only
		$this->addElement('text', 'admin_name', array(
			'label'			=> 'Administrator Name:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
			'ignore' 		=> true,
			'disable'       => 'disable',
		));
		
		// change to read only		 
		$this->addElement('text', 'admin_email', array(
			'label'			=> 'Administrator E-Mail:',
			'filters'		=> array('StringTrim'),
			'readonly'		=> true,
			'ignore' 		=> true,
			'disable'       => 'disable',
		));
		 
		$this->addElement('text', 'host', array(
			'label'			=> 'Publisher Specific Host:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'cdn_host', array(
			'label'			=> 'Publisher Specific CDN Host:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'rtmp_url', array(
			'label'			=> 'RTMP URL:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'admin_login_users_quota', array(
			'label'			=> 'Number of KMC admin users:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('select', 'partner_package', array(
			'label'			=> 'Usage Package:',
			'filters'		=> array('StringTrim'),
		));
				
		$this->addElement('text', 'def_thumb_offset', array(
			'label'	  => 'Default Thumbnail Offset',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'live_stream_enabled')))
		));
				
		$this->addElement('checkbox', 'monitor_usage', array(
			'label'	  => 'Monitor Usage',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'live_stream_enabled')))
		));
		
		$this->addElement('hidden', 'crossLine', array(
			'lable'			=> 'line',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'hr', 'class' => 'crossLine')))
		));
		
		$element = new Zend_Form_Element_Hidden('setPublisherFunctionality');
		$element->setLabel('Set Publisher Functionality');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b', 'class' => 'setPublisherFunctionality'))));
		
		$this->addElements(array($element));
		

		$this->addElement('checkbox', 'live_stream_enabled', array(
			'label'	  => 'Live workFlow',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'live_stream_enabled')))
		));
		
		$this->addElement('checkbox', 'enable_silver_light', array(
			'label'	  => 'Silverlight',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'enable_silver_light'))),
		));
		
		$this->addElement('checkbox', 'enable_vast', array(
			'label'	  => 'Vast',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'enable_vast'))),
		));
		
		$this->addElement('checkbox', 'enable508_players', array(
			'label'	  => '508 players',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'enable_508_players'))),
		));
		
		$this->addElement('checkbox', 'enable_ps2_permission_validation', array(
			'label'	  => 'PS2 permission validation',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'enable_ps2_permission_validation'))),
		));
		
		$this->addElement('checkbox', 'enable_metadata', array(
			'label'	  => 'Metadata',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'enable_metadata'))),
		));
		
		$this->addElement('checkbox', 'enable_content_distribution', array(
			'label'	  => 'Content Distribution',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'enable_content_distribution'))),
		));
		
		$this->addElement('checkbox', 'enable_audit_trail', array(
			'label'	  => 'Audit trail',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'enable_audit_trail'))),
		));
		
		$this->addElement('checkbox', 'enable_annotation', array(
			'label'	  => 'Aannotation',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'enable_annotation'))),
		));
		
		$this->addElement('checkbox', 'enable_analytics_tab', array(
			'label'	  => 'Analytics tab',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'enable_analytics_tab'))),
		));
		
		$this->addElement('checkbox', 'moderate_content', array(
			'label'	  => 'Content Moderation',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'moderate_content')))
		));
		
		$this->addElement('checkbox', 'storage_delete_from_kaltura', array(
			'label'	  => 'Delete exported storage from Kaltura',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'storage_delete_from_kaltura')))
		));
		
		$this->addElement('checkbox', 'enable_entry_replacement', array(
			'label'	  => 'Enable entry replacement',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'enable_entry_replacement')))
		));
		
		$this->addElement('checkbox', 'enable_entry_replacement_approval', array(
			'label'	  => 'Enable entry replacement approval',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt', 'class' => 'enable_entry_replacement_approval')))
		));
				
		$storageServP = new Kaltura_Form_Element_EnumSelect('storage_serve_priority', array('enum' => 'KalturaStorageServePriority'));
		$storageServP->setLabel('Delivery Policy:');
		$this->addElements(array($storageServP));
	}
}