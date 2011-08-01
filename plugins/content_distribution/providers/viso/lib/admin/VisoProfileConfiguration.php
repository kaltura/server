<?php 
/**
 * @package plugins.visoDistribution
 * @subpackage admin
 */
class Form_VisoProfileConfiguration extends Form_ConfigurableProfileConfiguration
{
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Viso Provider Configuration');
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
		
		$element = new Zend_Form_Element_Hidden('feed_url');
		$element->clearDecorators();
		$element->addDecorator('Callback', array('callback' => array($this, 'renderFeedUrl')));
		$this->addElement($element);
		
		$this->addDisplayGroup(
			array('feed_title', 'feed_link', 'feed_description'), 
			'feed', 
			array('legend' => 'Feed Configuration', 'decorators' => array('FormElements', 'Fieldset'))
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
