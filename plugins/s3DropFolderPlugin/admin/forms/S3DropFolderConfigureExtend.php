<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_S3DropFolderConfigureExtend_SubForm extends Form_FtpDropFolderConfigureExtend_SubForm
{
	public function getTitle()
	{
		return 'S3 settings';
	}

	public function init()
	{
		$this->addElement('text', 's3Host', array(
			'label'			=> 'Host:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 's3Region', array(
			'label'			=> 'Region:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 's3UserId', array(
			'label'			=> 'User:',
			'filters'		=> array('StringTrim'),
		));

		$this->addElement('text', 's3Password', array(
			'label'			=> 'Password:',
			'filters'		=> array('StringTrim'),
		));
	}

}
