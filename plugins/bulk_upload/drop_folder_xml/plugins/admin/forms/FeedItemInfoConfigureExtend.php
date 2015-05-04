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
			'label' 		=> 'item-xpath',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 'itemPublishDateXPath', array(
			'label'			=> 'item-publish-date',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemUniqueIdentifierXPath', array(
			'label'			=> 'item-unique-id',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemContentFileSizeXPath', array(
			'label'			=> 'item-content-tag-file-size-xpath',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemContentUrlXPath', array(
			'label'			=> 'item-content-tag-url-xpath',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemHashXPath', array(
			'label'			=> 'item-hash-xpath',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemContentXpath', array(
			'label'			=> 'item-content-tag-xpath',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'contentBitrateAttributeName', array(
			'label'			=> 'item-content-tag-bitrate-attribute-name',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addElement('text', 'itemContentBitrateXPath', array(
			'label'			=> 'item-content-tag-bitrate-xpath',
			'filters'		=> array('StringTrim'),
		));
		
		$this->setDecorators(array(
	        'FormElements',
	        array('HtmlTag', array('tag' => 'span', 'id' => 'frmFeedItemInfo')),
        ));
		
	}

}