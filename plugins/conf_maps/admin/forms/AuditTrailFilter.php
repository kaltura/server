<?php
/**
 * @package plugins.audit
 * @subpackage Admin
 */
class Form_AuditTrailFilter extends Form_PartnerIdFilter
{
	public function init()
	{
		parent::init();
		$this->setAttrib('id', 'frmAuditTrailList');

		$this->removeElement("cmdSubmit");

		$filterType = $this->getElement('filter_type');
		$filterType->setMultiOptions(array(
			'auditObjectTypeEqual' => 'Audit Trail Type',
		));

		$this->removeElement("filter_input");
		$this->addElement('select', 'filter_input', array(
			'required'	=> true,
			'decorators' 	=> array(
				'ViewHelper',
				array('HtmlTag', array('tag' => 'div', 'id' => 'filter_text')),
			),
			'multioptions' => array(
				'' => null,
				'accessControl' => 'ACCESS_CONTROL',
				'BatchJob' => 'BATCH_JOB',
				'category' => 'CATEGORY',
				'conversionProfile2' => 'CONVERSION_PROFILE_2',
				'EmailIngestionProfile' => 'EMAIL_INGESTION_PROFILE',
				'entry' => 'ENTRY',
				'FileSync' => 'FILE_SYNC',
				'flavorAsset' => 'FLAVOR_ASSET',
				'thumbAsset' => 'THUMBNAIL_ASSET',
				'flavorParams' => 'FLAVOR_PARAMS',
				'thumbParams' => 'THUMBNAIL_PARAMS',
				'flavorParamsConversionProfile' => 'FLAVOR_PARAMS_CONVERSION_PROFILE',
				'flavorParamsOutput' => 'FLAVOR_PARAMS_OUTPUT',
				'thumbParamsOutput' => 'THUMBNAIL_PARAMS_OUTPUT',
				'kshow' => 'KSHOW',
				'KshowKuser' => 'KSHOW_KUSER',
				'kuser' => 'KUSER',
				'mediaInfo' => 'MEDIA_INFO',
				'moderation' => 'MODERATION',
				'Partner' => 'PARTNER',
				'roughcutEntry' => 'ROUGHCUT',
				'syndicationFeed' => 'SYNDICATION',
				'uiConf' => 'UI_CONF',
				'UploadToken' => 'UPLOAD_TOKEN',
				'widget' => 'WIDGET',
				'Metadata' => 'METADATA',
				'MetadataProfile' => 'METADATA_PROFILE',
				'UserLoginData' => 'USER_LOGIN_DATA',
				'UserRole' => 'USER_ROLE',
				'Permission' => 'PERMISSION',
				'ReachProfile' => 'REACH_PROFILE'
			),
		));

		$this->addElement('text', 'filter_object_id', array(
			'label' => 'Object Id',
			'decorators' => array('ViewHelper', 'Label'),
			'required'	=> true
		));

		// submit button
		$this->addElement('button', 'cmdSubmit', array(
			'type' => 'submit',
			'label'		=> 'Search',
			'decorators' => array('ViewHelper'),
		));
	}

}