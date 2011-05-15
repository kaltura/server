<?php 
/**
 * @package plugins.comcastDistribution
 * @subpackage admin
 */
class Form_ComcastProfileConfiguration extends Form_ProviderProfileConfiguration
{
	protected function addProviderElements()
	{
		$element = new Zend_Form_Element_Hidden('providerElements');
		$element->setLabel('Comcast Specific Configuration');
		$element->setDecorators(array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag',  array('tag' => 'b'))));
		$this->addElements(array($element));
		
		$this->addMetadataProfile();
		
		$this->addElement('text', 'email', array(
			'label'			=> 'E-Mail:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'password', array(
			'label'			=> 'Password:',
			'filters'		=> array('StringTrim'),
		));
	
		$this->addElement('text', 'account', array(
			'label'			=> 'Account:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'keywords', array(
			'label'			=> 'Keywords:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'author', array(
			'label'			=> 'Author:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'album', array(
			'label'			=> 'Album:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'copyright', array(
			'label'			=> 'Copyright:',
			'title'			=> 'Use {year} to specify year',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'link_href', array(
			'label'			=> 'Link Href:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'link_text', array(
			'label'			=> 'Link Text:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'notes_to_comcast', array(
			'label'			=> 'Notes to Comcast:',
			'filters'		=> array('StringTrim'),
		));
	}
}