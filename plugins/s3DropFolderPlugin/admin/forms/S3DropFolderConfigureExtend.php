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
				1. User & Password (leave empty to use 'IAM Role')<br><br>
				2. IAM Role:<br>
				2.1. Set Role ARN - will be used to assume that role<br>
				Note: role 'Trust Policy' must allow 'cloud_storage' s3Arn to assume it<br><br>
				2.2. Leave empty - will use 'cloud_storage' s3Arn to access bucket<br>
				Note: Bucket Policy must allow s3Arn to operate it";
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

		$this->addElement('text', 's3IAMRole', array(
			'label'			=> 'IAM Role:',
			'filters'		=> array('StringTrim'),
		));
	}

}
