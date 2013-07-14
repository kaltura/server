<?php 
/**
 * @package plugins.synacorHboDistribution
 * @subpackage admin
 */
class Form_SynacorHboProfileConfiguration extends Form_ConfigurableProfileConfiguration
{	
	protected function addProviderElements()
	{
		$this->setDescription(null);
		
		$this->addElement('text', 'feed_title', array(
			'label'			=> 'Feed Title:',
			'filters'		=> array('StringTrim'),
		    'required'		=> true,
		));
		
		$this->addElement('text', 'feed_subtitle', array(
			'label'			=> 'Feed Subtitle:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'feed_link', array(
			'label'			=> 'Feed Link:',
			'filters'		=> array('StringTrim'),
		    'required'		=> true,
		));
		
		$this->addElement('text', 'feed_author_name', array(
			'label'			=> 'Feed Author Name:',
			'filters'		=> array('StringTrim'),
		));		
		
		$this->addDisplayGroup(
			array('feed_title', 'feed_subtitle', 'feed_link', 'feed_author_name'), 
			'feed_default_values', 
			array('legend' => 'Default Feed Values', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		
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