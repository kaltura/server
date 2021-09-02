<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_S3DropFolderConfigure extends Form_DropFolderConfigure
{
	public function init()
	{
		parent::init();
		$this->removeElement('path');
		$this->addElement('text', 'path', array(
			'label' 		=> 'Bucket: (for a path inside the Bucket, insert /Bucket/Path)',
			'required'		=> true,
			'filters'		=> array('StringTrim'),
		));
	}
}