<?php 
class Form_PartnerCreate extends Kaltura_Form
{
	public function init()
	{
		// Set the method for the display form to POST
		$this->setMethod('post');
		$this->setName('new_account'); // form id
		$this->setAttrib('class', 'inline-form');
		
		$this->addElement('text', 'name', array(
			'label' => 'partner-create form name',
			'required' => true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'company', array(
			'label' => 'partner-create form company',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'admin_email', array(
			'label' => 'partner-create form admin email',
			'required' => true,
			'validators' => array('EmailAddress'),
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'phone', array(
			'label' => 'partner-create form admin phone',
			'required' => true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('select', 'describe_yourself', array(
			'label' => 'partner-create form description',
			'required' => true,
			'filters'		=> array('StringTrim'),
			'multiOptions' => array(
				'' => 'Please select...',
				'Integrator/Web developer' => 'Integrator/Web developer',
				'Ad Agency' => 'Ad Agency',
				'Kaltura Plugin Distributor' => 'Kaltura Plugin Distributor',
				'Social Network' => 'Social Network',
				'Personal Site' => 'Personal Site',
				'Corporate Site' => 'Corporate Site',
				'E-Commerce' => 'E-Commerce',
				'E-Learning' => 'E-Learning',
				'Media Company/ Producer' => 'Media Company/ Producer',
				'Other' => 'Other',
			)
		));
		
		$this->addElement('select', 'partner_package', array(
			'label'			=> 'partner-create form package',
			'filters'		=> array('StringTrim'),
			
		));
		
		$this->addElement('text', 'website', array(
			'label' => 'partner-create form url',
			'required' => true,
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('multiselect', 'content_categories', array(
			'label' => 'partner-create form content',
			'size' => 5,
			'required' => true,
			'filters'		=> array('StringTrim'),
			'multiple' => true,
			'multiOptions' => array(
				'Select all that apply' => array(
					'Arts & Literature' => 'Arts & Literature',
					'Automotive' => 'Automotive',
					'Business' => 'Business',
					'Comedy' => 'Comedy',
					'Education' => 'Education',
					'Entertainment' => 'Entertainment',
					'Film & Animation' => 'Film & Animation',
					'Gaming' => 'Gaming',
					'Howto & Style' => 'Howto & Style',
					'Lifestyle' => 'Lifestyle',
					'Men' => 'Men',
					'Music' => 'Music',
					'News & Politics' => 'News & Politics',
					'Nonprofits & Activism' => 'Nonprofits & Activism',
					'People & Blogs' => 'People & Blogs',
					'Pets & Animals' => 'Pets & Animals',
					'Science & Technology' => 'Science & Technology',
					'Sports' => 'Sports',
					'Travel & Events' => 'Travel & Events',
					'Women' => 'Women',
					'N/A' => 'N/A',
				)
			)
		));
		
		$this->addElement('radio', 'adult_content', array(
			'label' => 'partner-create form adult',
			'required' => true,
			'filters'		=> array('StringTrim'),
			'multiOptions' => array(
				'1' => 'Yes',
				'0' => 'No'
			),
			'separator' => '&nbsp;'
		));
		
		$this->addDisplayGroup(array('name', 'company', 'admin_email', 'phone', 'describe_yourself', 'partner_package'), 'partner_info', array(
			'legend' => 'Publisher Info',
			'decorators' => array(
				'Description', 
				'FormElements', 
				array('Fieldset'),
			)
		));
		
		$this->addDisplayGroup(array('website', 'content_categories', 'adult_content'), 'website_info', array(
			'legend' => 'Website Info',
			'decorators' => array(
				'Description', 
				'FormElements', 
				array('Fieldset'),
			)
		));
		
		$this->addElement('button', 'submit', array(
			'label' => 'partner-create form create',
			'type' => 'submit',
			'decorators' => array('ViewHelper')
		));
		
		
		$this->addDisplayGroup(array('submit'), 'buttons1', array(
			'decorators' => array(
				'FormElements', 
				array('HtmlTag', array('tag' => 'div', 'class' => 'buttons')),
			)
		));
	}
}