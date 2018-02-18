<?php 
/**
 * @package plugins.doubleClickDistribution
 * @subpackage admin
 */
class Form_DoubleClickProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	public function getObject($objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $properties, $add_underscore, $include_empty_fields);
		
		$object->cuePointsProvider = $this->getValue('cue_points_provider');
		
		return $object;
	}
	
	public function init()
	{
		parent::init();
		$this->setDescription('DoubleClick Distribution Profile');
		
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$this->addDecorator('ViewScript', array(
			'viewScript' => 'doubleclick-form.phtml',
			'placement' => 'APPEND'
		));
	}
	
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('DoubleClick Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$element = new Zend_Form_Element_Text('cue_points_provider');
		$element->setLabel('Cue Points Provider:');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('items_per_page');
		$element->setLabel('Items Per Page:');
		$this->addElement($element);
		$this->setDefault('items_per_page', 100);
		
		$this->addDisplayGroup(
			array('cue_points_provider', 'items_per_page'), 
			'general_group', 
			array('legend' => 'General', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$element = new Zend_Form_Element_Text('channel_title');
		$element->setLabel('Channel title:');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_description');
		$element->setLabel('Channel description:');
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('channel_link');
		$element->setLabel('Channel link:');
		$this->addElement($element);

		$element = new Zend_Form_Element_Checkbox('ignore_scheduling_in_feed');
		$element->setLabel('Ignore Scheduling In Feed, As Default Feed Behavior');
		$this->addElement($element);
		
		$this->addDisplayGroup(
			array('channel_title', 'channel_description', 'channel_link', 'ignore_scheduling_in_feed'),
			'channel', 
			array('legend' => 'Feed Configuration', 'decorators' => array('FormElements', 'Fieldset'))
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
			return '<a href="'.$url.'&version=1" target="_blank">Feed URL</a> <a href="'.$url.'&period=86400&version=1" target="_blank">Last 24 Hours Feed URL</a> <a href="'.$url.'" target="_blank">24 Hour Feed (v2)</a>';
	}
}