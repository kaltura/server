<?php
require_once KALTURA_ROOT_PATH.'/vendor/google-api-php-client-1.1.2/src/Google/autoload.php';
  
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

		$hasToken = false;
		if (isset($this->distributionProfile) && $this->distributionProfile && $this->distributionProfile->googleTokenData)
			$hasToken = true;
		$this->addElement('text', 'api_authorize_url', array(
			'label'			=> 'Authorize API Access:',
			'decorators' => array(array('ViewScript', array(
				'viewScript' => 'youtube-distribution-api-authorize-field.phtml',

			))),
		'hasToken' => $hasToken
		));
		
		// General
		$this->addElement('text', 'username', array(
			'label'			=> 'YouTube Account:',
			'filters'		=> array('StringTrim'),
		));

		// Privacy Status
		$this->addElement('select', 'privacy_status', array(
			'label' => 'Privacy Status:',
			'multioptions' => array(
				'public' => 'public',
				'private' => 'private',
				'unlisted' => 'unlisted',
			)
		));

		$this->addElement('checkbox', 'assume_success', array(
			'label'			=> 'Assume success (synchronous response)',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'dt')))
		));

		$this->addDisplayGroup(
			array('username', 'assume_success', 'privacy_status'), 
			'general', 
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);
				
		//  Metadata
		$categories = $this->getCategories();
		if($categories)
		{
			$this->addElement('select', 'default_category', array(
				'label' => 'Default Category:',
				'multioptions' => $this->getCategories()
			));
		}
				
		// Community
		$this->addElement('select', 'allow_embedding', array(
			'label' => 'Allow Embedding:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
			)
		));
		
		$this->addElement('select', 'allow_comments', array(
			'label' => 'Allow Comments:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
				'moderated' => 'moderated',
			),
			'default' => 'allowed',
			'disabled' => true,
			'title' => 'Currently not supported by you-tube API v3'
		));
		
		$this->addElement('select', 'allow_ratings', array(
			'label' => 'Allow Ratings:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
			),
			'default' => 'allowed',
			'disabled' => true,
			'title' => 'Currently not supported by you-tube API v3'
		));
		
		$this->addElement('select', 'allow_responses', array(
			'label' => 'Allow Responses:',
			'multioptions' => array(
				'allowed' => 'allowed', 
				'denied' => 'denied',
				'moderated' => 'moderated',
			),
			'default' => 'allowed',
			'disabled' => true,
			'title' => 'Currently not supported by you-tube API v3'
		));
		
		$this->addDisplayGroup(
			array('allow_embedding', 'allow_comments', 'allow_ratings', 'allow_responses'), 
			'community', 
			array('legend' => 'Community', 'decorators' => array('FormElements', 'Fieldset'))
		);
	}
	
	protected function getCategories()
	{
		if(!$this->distributionProfile || !$this->distributionProfile->googleTokenData)
			return null;
			
		$distributionProfile = $this->distributionProfile;
		/* @var $distributionProfile Kaltura_Client_YoutubeApiDistribution_Type_YoutubeApiDistributionProfile */
		
		$client = new Google_Client();
		$client->setClientId($distributionProfile->googleClientId);
		$client->setClientSecret($distributionProfile->googleClientSecret);
		$client->setAccessToken(str_replace('\\', '', $distributionProfile->googleTokenData));
		
		$youtube = new Google_Service_YouTube($client);
		try
		{
			$categoriesListResponse = $youtube->videoCategories->listVideoCategories('id,snippet', array('regionCode' => 'us'));
		}
		catch(Google_Auth_Exception $e)
		{
			KalturaLog::err($e);
			return array();
		}
		
		$categories = array();
		foreach($categoriesListResponse->getItems() as $category)
		{
			$categories[$category['id']] = $category['snippet']['title'];
		}
		return $categories;
	}
}