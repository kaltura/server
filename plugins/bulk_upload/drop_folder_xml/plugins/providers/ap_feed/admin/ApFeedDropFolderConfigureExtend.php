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
	
}
