<?php 
/**
 * @package plugins.uverseClickToOrderDistribution
 * @subpackage admin
 */
class Form_UverseClickToOrderProfileConfiguration extends Form_ConfigurableProfileConfiguration
{	
	protected function addProviderElements()
	{
		$this->setDescription(null);
		
		$element = new Zend_Form_Element_Hidden('feed_url');
		$element->clearDecorators();
		$element->addDecorator('Callback', array('callback' => array($this, 'renderFeedUrl')));
		$this->addElement($element);
		
		$this->addDisplayGroup(
			array('feed_url'), 
			'feed_url_group', 
			array('legend' => '', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$this->addElement('text', 'background_image_wide', array(
			'label'			=> 'Wide Background Image:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'background_image_standard', array(
			'label'			=> 'Standard Background Image:',
			'filters'		=> array('StringTrim'),
		));
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