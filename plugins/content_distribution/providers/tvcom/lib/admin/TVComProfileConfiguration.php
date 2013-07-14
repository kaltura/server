<?php 
/**
 * @package plugins.tvComDistribution
 * @subpackage admin
 */
class Form_TVComProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('TV.com Provider Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('feed_title');
		$element->setLabel('Feed title:');
		$element->addValidator(new Zend_Validate_StringLength(0, 128));
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('feed_link');
		$element->setLabel('Feed link:');
		$element->addValidator(new Zend_Validate_StringLength(0, 255));
		$this->addElement($element);
		
		$element = new Zend_Form_Element_Text('feed_description');
		$element->setLabel('Feed description:');
		$element->addValidator(new Zend_Validate_StringLength(0, 255));
		$this->addElement($element);
		
		$this->addElement('Text', 'feed_language', array('label' => 'Feed language:', 'value' => 'en-us'));
		$this->addElement('Text', 'feed_copyright', array('label' => 'Feed copyright:'));
		$this->addElement('Text', 'feed_image_title', array('label' => 'Feed image title:'));
		$this->addElement('Text', 'feed_image_url', array('label' => 'Feed image url:'));
		$this->addElement('Text', 'feed_image_link', array('label' => 'Feed image link:'));
		$this->addElement('Text', 'feed_image_width', array('label' => 'Feed image width:'));
		$this->addElement('Text', 'feed_image_height', array('label' => 'Feed image height:'));
		
		$element = new Zend_Form_Element_Hidden('feed_url');
		$element->clearDecorators();
		$element->addDecorator('Callback', array('callback' => array($this, 'renderFeedUrl')));
		$this->addElement($element);
		
		$this->addDisplayGroup(
			array('feed_title', 'feed_link', 'feed_description', 'feed_language', 'feed_copyright'), 
			'feed', 
			array('legend' => 'Feed Configuration', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
		$this->addDisplayGroup(
			array('feed_image_title', 'feed_image_url', 'feed_image_link', 'feed_image_width', 'feed_image_height'), 
			'feed_image', 
			array('legend' => 'Feed Image Configuration', 'decorators' => array('FormElements', 'Fieldset'))
		);
		
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
	
	public function render(Zend_View_Interface $view = null)
	{
		$this->disableTriggerUpdateFieldConfig();
		
		return parent::render($view);
	}
	
	public function disableTriggerUpdateFieldConfig()
	{
		$subForm = $this->getSubForm('fieldConfigArray');
		if ($subForm)
		{
			$fieldsSubForms = $subForm->getSubForms();
			foreach($fieldsSubForms as $fieldSubForm)
			{
				$updateOnChange = $fieldSubForm->getElement('updateOnChange');
				if ($updateOnChange)
				{
					$updateOnChange->setAttrib('disabled', 'disabled');
				}
			}
		}
	}
}
