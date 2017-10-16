<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_FeedDropFolderConfigureExtend_SubForm extends Form_DropFolderConfigureExtend_SubForm
{
	public function getTitle()
	{
	    return 'Feed settings';
	}    
    
    public function init()
	{
        $this->addElement('text', 'itemHandlingLimit', array(
			'label'			=> 'Limit of handled items (default 10000):',
			'filters'		=> array('StringTrim'),
		));
		
		$this->addSubForm(new Form_FeedItemInfoConfigureExtend(), 'feedItemInfo');
	}
	
	public function populateFromObject($dropFolder, $add_underscore = true)
	{
		parent::populateFromObject($dropFolder, false);
		
		/* @var $dropFolder Kaltura_Client_DropFolder_Type_FeedDropFolder */
		$feedItemInfoForm = $this->getSubForm('feedItemInfo');
		/* @var $feedItemInfoForm Form_FeedItemInfoConfigureExtend */
		$feedItemInfoForm->populateFromObject($dropFolder->feedItemInfo, false);
		
	}
	
	public function getObject($object, $objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		/* @var $object Kaltura_Client_DropFolder_Type_FeedDropFolder */
		$feedItemInfoForm = $this->getSubForm('feedItemInfo');
		
		/* @var $feedItemInfoForm Form_FeedItemInfoConfigureExtend */
		$object->feedItemInfo = $feedItemInfoForm->getObject ('Kaltura_Client_FeedDropFolder_Type_FeedItemInfo',$properties, false );
		
	    return $object;
	}
	
	
}