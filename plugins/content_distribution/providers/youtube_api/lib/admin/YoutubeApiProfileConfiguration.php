<?php 
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage admin
 */
class Form_YoutubeApiProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	public function init()
	{
		parent::init();
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'youtube-distribution.phtml',
			'placement' => 'APPEND'
		));
	}
	
	protected function addProviderElements()
	{
	    $this->setDescription(null);
	    
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('YouTube Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));

		$this->addElement('text', 'api_authorize_url', array(
			'label'			=> 'Authorize API Access:',
			'decorators' => array(array('ViewScript', array(
				'viewScript' => 'youtube-distribution-api-authorize-field.phtml',

			)))
		));
		
		// General
		$this->addElement('text', 'username', array(
			'label'			=> 'YouTube Account:',
			'filters'		=> array('StringTrim'),
		));

		// General
		$this->addElement('text', 'password', array(
			'label'			=> 'YouTube Password:',
			'filters'		=> array('StringTrim'),
		));
								
//		$this->addMetadataProfile();
		
		$this->addDisplayGroup(
			array('username', 'password'), 
			'general', 
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);
				
		// taken from http://gdata.youtube.com/schemas/2007/categories.cat
		$youTubeCategories = array(
			'Film' => 'Film & Animation',
			'Autos' => 'Autos & Vehicles',
			'Music' => 'Music',
			'Animals' => 'Pets & Animals',
			'Sports' => 'Sports',
			'Travel' => 'Travel & Events',
			'Games' => 'Gaming',
			'Comedy' => 'Comedy',
			'People' => 'People & Blogs',
			'News' => 'News & Politics',
			'Entertainment' => 'Entertainment',
			'Education' => 'Education',
			'Howto' => 'Howto & Style',
			'Nonprofit' => 'Nonprofits & Activism',
			'Tech' => 'Science & Technology',
		);
		
		//  Metadata
		$this->addElement('select', 'default_category', array(
			'label' => 'Default Category:',
			'multioptions' => $youTubeCategories,
		));
				
		// Community
		$this->addElement('select', 'allow_comments', array(
			'label' => 'Allow Comments:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
				'moderated' => 'moderated',
			)
		));
		
		$this->addElement('select', 'allow_embedding', array(
			'label' => 'Allow Embedding:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
			)
		));
		
		$this->addElement('select', 'allow_ratings', array(
			'label' => 'Allow Ratings:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
			)
		));
		
		$this->addElement('select', 'allow_responses', array(
			'label' => 'Allow Responses:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
				'moderated' => 'moderated',
			)
		));
		
		$this->addDisplayGroup(
			array('allow_comments', 'allow_embedding', 'allow_ratings', 'allow_responses'), 
			'community', 
			array('legend' => 'Community', 'decorators' => array('FormElements', 'Fieldset'))
		);
	}
}