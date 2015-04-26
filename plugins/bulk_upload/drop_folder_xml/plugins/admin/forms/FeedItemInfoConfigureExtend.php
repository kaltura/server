<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_FeedItemInfoConfigureExtend extends Infra_Form
{
	public function init()
	{
		$this->addElement('text', 'itemXPath', array(
			'label' 		=> 'Item XPath:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'itemPublishDateXPath', array(
			'label'			=> 'Item publish date xpath (relative to item):',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemUniqueIdentifierXPath', array(
			'label'			=> 'Item unique identifier XPath (relative to item):',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemContentFileSizeXPath', array(
			'label'			=> 'Item unique identifier XPath (relative to item):',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemContentUrlXPath', array(
			'label'			=> 'Item content tag URL XPath (relative to item):',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemHashXPath', array(
			'label'			=> 'Item hash XPath (relative to item):',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemContentXpath', array(
			'label'			=> 'Item content tag XPath (relative to item):',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'contentBitrateAttributeName', array(
			'label'			=> 'Item content tag bitrate attribute name:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemContentBitrateXPath', array(
			'label'			=> 'Item content bitrate XPath (relative to item):',
			'filters'		=> array('StringTrim'),
		));
		
		$this->setDecorators(array(
	        'FormElements',
	        array('HtmlTag', array('tag' => 'span', 'id' => 'frmFeedItemInfo')),
        ));
		
	}

}