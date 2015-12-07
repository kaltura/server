<?php

/**
 * Subclass for representing a row from the 'email_ingestion_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class EmailIngestionProfile extends BaseEmailIngestionProfile
{
	const EMAIL_INGESTION_PROFILE_STATUS_INACTIVE = 0;
	const EMAIL_INGESTION_PROFILE_STATUS_ACTIVE = 1;
	
	public function getDefaultCategory() { return $this->getFromCustomData("defaultCategory", null); }
	public function setDefaultCategory( $v ) { $this->putInCustomData("defaultCategory", $v); } 

	public function getDefaultUserId() { return $this->getFromCustomData("defaultUserId", null); }
	public function setDefaultUserId( $v ) { $this->putInCustomData("defaultUserId", $v); }
	
	public function getDefaultTags() { return $this->getFromCustomData("defaultTags", null); }
	public function setDefaultTags( $v ) { $this->putInCustomData("defaultTags", $v); }
	
	public function getDefaultAdminTags() { return $this->getFromCustomData("defaultAdminTags", null); }
	public function setDefaultAdminTags( $v ) { $this->putInCustomData("defaultAdminTags", $v); }
	
	public function getMaxAttachmentSizeKbytes() { return $this->getFromCustomData("maxAttachmentSizeKbytes", null); }
	public function setMaxAttachmentSizeKbytes( $v ) { $this->putInCustomData("maxAttachmentSizeKbytes", $v); }
	
	public function getMaxAttachmentsPerMail() { return $this->getFromCustomData("maxAttachmentsPerMail", null); }
	public function setMaxAttachmentsPerMail( $v ) { $this->putInCustomData("maxAttachmentsPerMail", $v); }
}
