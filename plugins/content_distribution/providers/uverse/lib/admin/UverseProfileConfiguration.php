<?php 
/**
 * @package plugins.uverseDistribution
 * @subpackage admin
 */
class Form_UverseProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = true)
	{
		// $include_empty_fields is defaulted to true instead of false, so we can empty fields
		return parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
	}
	protected function addProviderElements()
	{
		$this->setDescription(null);
		
		// Channel Configuration
		$this->addElement('text', 'channel_title', array(
			'label'			=> 'Channel title:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'channel_link', array(
			'label'			=> 'Channel link:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'channel_description', array(
			'label'			=> 'Channel description:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'channel_language', array(
			'label'			=> 'Channel language:',
			'filters'		=> array('StringTrim'),
			'value'			=> 'en-us',
		));
		
		$this->addElement('text', 'channel_copyright', array(
			'label'			=> 'Channel copyright:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'channel_image_title', array(
			'label'			=> 'Channel image title:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'channel_image_url', array(
			'label'			=> 'Channel image url:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'channel_image_link', array(
			'label'			=> 'Channel image link:',
			'filters'		=> array('StringTrim'),
		));

		$this->addDisplayGroup(
			array('channel_title', 'channel_link', 'channel_description', 'channel_language', 'channel_copyright', 'channel_image_title', 'channel_image_url', 'channel_image_link'),
			'general', 
			array('legend' => 'Channel Configuration', 'decorators' => array('FormElements', 'Fieldset'))
		);
				
		// FTP Configuration
		$this->addElement('text', 'ftp_host', array(
			'label'			=> 'FTP Host:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'ftp_login', array(
			'label'			=> 'FTP Login:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'ftp_password', array(
			'label'			=> 'FTP Password:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addDisplayGroup(
			array('ftp_host', 'ftp_login', 'ftp_password'), 
			'sftp', 
			array('legend' => 'FTP Configuration', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		// Feed URL
		$element = new Zend_Form_Element_Hidden('feed_url');
		$element->clearDecorators();
		$element->addDecorator('Callback', array('callback' => array($this, 'renderFeedUrl')));
		$this->addElement($element);
		
		$this->addDisplayGroup(
			array('feed_url'), 
			'feed_url_group', 
			array('legend' => '', 'decorators' => array('FormElements', 'Fieldset'))
		);
	}
	
	public function renderFeedUrl($content)
	{
		$url = $this->getValue('feed_url');
		if (!$url)
			return 'Feed URL will be generated once the feed is saved';
		else
			return '<a href="'.$url.'" target="_blank">Feed URL</a>';
	}
}