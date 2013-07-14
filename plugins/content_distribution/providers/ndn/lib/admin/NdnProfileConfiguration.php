<?php 
/**
 * @package plugins.ndnDistributions
 * @subpackage admin
 */
class Form_NdnProfileConfiguration extends Form_ConfigurableProfileConfiguration
{	
	protected function addProviderElements()
	{
		$this->setDescription(null);
		
		$element = new Zend_Form_Element_Hidden('feed_url');
		$element->clearDecorators();
		$element->addDecorator('Callback', array('callback' => array($this, 'renderFeedUrl')));
		$this->addElement($element);
				
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
		
		$this->addElement('text', 'item_media_rating', array(
			'label'			=> 'Item media rating:',
			'filters'		=> array('StringTrim'),
		));
		
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