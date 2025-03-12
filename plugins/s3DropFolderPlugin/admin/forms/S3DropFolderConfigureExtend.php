<?php
/**
 * @package plugins.dropFolder
 * @subpackage Admin
 */
class Form_S3DropFolderConfigureExtend_SubForm extends Form_DropFolderConfigureExtend_SubForm
{
	public function getTitle()
	{
		return 'S3 settings';
	}
	
	public function getDescription()
	{
		return "Authentication precedence:<br>
				1. User & Password (if passed will be used)<br>
				2. Bucket Policy Allows Access<br>
				Note: Bucket policy must allow 'runtime_config' map 's3_drop_folder' section 's3Arn' value role to operate it";
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
		
		$this->addElement('checkbox', 'useS3Arn', array(
			'label'      => 'Bucket Policy Allows Access',
			'decorators' => array('ViewHelper', array('Label', array('placement' => 'append')), array('HtmlTag', array('tag' => 'div', 'class' => 'rememeber'))),
			'uncheckedValue' => false,
			'checkedValue'   => true,
		));
	}
}
