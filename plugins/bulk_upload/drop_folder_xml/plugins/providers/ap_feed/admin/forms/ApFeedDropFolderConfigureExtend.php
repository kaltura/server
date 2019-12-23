<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_ApFeedDropFolderConfigureExtend_SubForm extends Form_FeedDropFolderConfigureExtend_SubForm
{
	public function getTitle()
	{
		return 'Feed settings';
	}
	
	public function init()
	{
		$this->addElement('text', 'apApiKey', array(
			'label'			=> 'AP Feed API Key:',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addSubForm(new Form_FeedItemInfoConfigureExtend(), 'feedItemInfo');
	}
	
	public function populateFromObject($dropFolder, $add_underscore = true)
	{
		parent::populateFromObject($dropFolder, false);
		
		$this->addItemXpathsToExtend($dropFolder->itemsToExpand);
	}
	
	public function getObject($object, $objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($objectType, $objectType, $properties, $add_underscore, $include_empty_fields);
		
		$itemXpathsToExtend = isset($properties['itemXpathsToExtend']) && is_array($properties['itemXpathsToExtend']) ? $properties['itemXpathsToExtend'] : array();
		$object->itemXpathsToExtend = array();
		foreach($itemXpathsToExtend as $key => $val)
		{
			$temp = new Kaltura_Client_Type_StringValue();
			$temp->value = $val;
			$object->itemsToExpand [] = $temp;
		}
		
		return $object;
	}
	
	protected function addItemXpathsToExtend($itemXpathsToExtend)
	{
		if (count($itemXpathsToExtend) == 0)
			$itemXpathsToExtend = array();
		
		$mainSubForm = new Zend_Form_SubForm();
		$mainSubForm->setLegend('Item XPaths To Expand');
		$this->getView()->addBasePath(realpath(dirname(__FILE__)));
		$mainSubForm->setDecorators(array(
			'FormElements',
			array('ViewScript', array(
				'viewScript' => 'ap-feed-item-xpath-to-extend.phtml',
				'placement' => 'APPEND'
			)),
			'Fieldset'
		));
		
		$i = 1;
		
		foreach($itemXpathsToExtend as $itemXPath)
		{
			$subForm = new Zend_Form_SubForm(array('disableLoadDefaultDecorators' => true));
			$subForm->setDecorators(array(
				'FormElements',
			));
			$subForm->addElement('text', 'itemXpathsToExtend', array(
				'decorators' => array('ViewHelper', array('HtmlTag', array('tag' => 'div'))),
				'isArray' => true,
				'value' => $itemXPath->value
			));
			
			$mainSubForm->addSubForm($subForm, 'itemXpathsToExtend_subform_'.$i++);
		}
		
		$this->addSubForm($mainSubForm, 'itemXpathsToExtend_group');
	}
	
}