<?php
require_once ( "kalturaSystemAction.class.php" );
class emailingestionprofilesAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		$this->pid = $this->getRequestParameter("pid", null);
		if ($this->getRequestParameter("advanced")) {
			$this->getResponse()->setCookie('email-ingestion-advanced', 'yes');
			$this->advanced = true;
		}
		else
		{
			if ($this->getRequest()->getCookie('email-ingestion-advanced') === 'yes')
				$this->advanced = true;
			else
				$this->advanced = false;
		}
		
		myDbHelper::$use_alternative_con = null;
		$this->editEmailIngestionProfile = null;
		$this->formaction = '';
		if ($this->getRequestParameter("id"))
		{
			$this->editEmailIngestionProfile = EmailIngestionProfilePeer::retrieveByPK($this->getRequestParameter("id"));
			
			if ($this->getRequestParameter("delete"))
			{
				if ($this->advanced)
				{
					$this->editEmailIngestionProfile->setStatus(EmailIngestionProfile::EMAIL_INGESTION_PROFILE_STATUS_INACTIVE);
					$this->editEmailIngestionProfile->save();
				}
				$this->redirect("system/emailingestionprofiles?pid=".$this->pid);
			}
			
			if ($this->getRequest()->getMethod() == sfRequest::POST)
			{
				$partnerId = $this->getRequestParameter("partner-id");
				if ($this->advanced)
				{
					$this->editEmailIngestionProfile->setPartnerId($partnerId);
				}
				else
				{
					if ($partnerId != 0)
						$this->editEmailIngestionProfile->setPartnerId($partnerId);
				}
				$this->editEmailIngestionProfile->setName($this->getRequestParameter("name"));
				$this->editEmailIngestionProfile->setDescription($this->getRequestParameter("description"));
				$this->editEmailIngestionProfile->setDefaultTags($this->getRequestParameter("default-tags"));
				$this->editEmailIngestionProfile->setDefaultAdminTags($this->getRequestParameter("default-admintags"));
				$this->editEmailIngestionProfile->setEmailAddress($this->getRequestParameter("email-address", false));
				$this->editEmailIngestionProfile->setMailboxId($this->getRequestParameter("mailbox-id"));
				$conversion_profile = $this->getRequestParameter("conversion-profile2-id", false);
				if($conversion_profile)
				{
					echo 'here'; die;
					$this->editEmailIngestionProfile->setConversionProfile2Id($conversion_profile);
				}
				else
				{
					//$this->editEmailIngestionProfile->setConversionProfile2Id(null);
				}
				$this->editEmailIngestionProfile->setModerationStatus($this->getRequestParameter("moderation-status"));
				$this->editEmailIngestionProfile->setDefaultCategory($this->getRequestParameter("default-category")); 
				$this->editEmailIngestionProfile->setDefaultUserId($this->getRequestParameter("default-userid"));
				$this->editEmailIngestionProfile->setMaxAttachmentSizeKbytes($this->getRequestParameter("max-attachment-size-kbytes"));
				$this->editEmailIngestionProfile->setMaxAttachmentsPerMail($this->getRequestParameter("max-attachments-per-mail"));
				
				$this->editEmailIngestionProfile->save();
				$this->redirect("system/emailingestionprofiles?pid=".$this->editEmailIngestionProfile->getPartnerId());
			}
		}
		elseif($this->getRequestParameter('editing') == 'add')
		{
			$this->editEmailIngestionProfile = new EmailIngestionProfile();
			$this->formaction = 'system/emailingestionprofiles?addingnew=true';
		}
		elseif($this->getRequestParameter('addingnew') == 'true' && $this->getRequest()->getMethod() == sfRequest::POST)
		{
			$this->editEmailIngestionProfile = new EmailIngestionProfile();
			$partnerId = $this->getRequestParameter("partner-id");
			$this->editEmailIngestionProfile->setPartnerId($partnerId);
			$this->editEmailIngestionProfile->setName($this->getRequestParameter("name"));
			$this->editEmailIngestionProfile->setDescription($this->getRequestParameter("description"));
			$this->editEmailIngestionProfile->setDefaultTags($this->getRequestParameter("default-tags"));
			$this->editEmailIngestionProfile->setDefaultAdminTags($this->getRequestParameter("default-admintags"));
			$this->editEmailIngestionProfile->setEmailAddress($this->getRequestParameter("email-address", false));
			$this->editEmailIngestionProfile->setMailboxId($this->getRequestParameter("mailbox-id"));
			$conversion_profile = $this->getRequestParameter("conversion-profile2-id", false);
			if($conversion_profile)
			{
				echo 'here'; die;
				$this->editEmailIngestionProfile->setConversionProfile2Id($conversion_profile);
			}
			else
			{
				//$this->editEmailIngestionProfile->setConversionProfile2Id(null);
			}
			$this->editEmailIngestionProfile->setModerationStatus($this->getRequestParameter("moderation-status"));
			$this->editEmailIngestionProfile->setDefaultCategory($this->getRequestParameter("default-category")); 
			$this->editEmailIngestionProfile->setDefaultUserId($this->getRequestParameter("default-userid"));
			$this->editEmailIngestionProfile->setMaxAttachmentSizeKbytes($this->getRequestParameter("max-attachment-size-kbytes"));
			$this->editEmailIngestionProfile->setMaxAttachmentsPerMail($this->getRequestParameter("max-attachments-per-mail"));
			
			$this->editEmailIngestionProfile->save();
			$this->redirect("system/emailingestionprofiles?pid=".$this->editEmailIngestionProfile->getPartnerId());			
		}
		$c = new Criteria();
		if(!is_null($this->pid))
		{
			$c->add(EmailIngestionProfilePeer::PARTNER_ID, $this->pid );
		}
		$this->EmailIngestionProfiles = EmailIngestionProfilePeer::doSelect($c);
		
		$this->entryModerationStatuses = self::getEnumValues("entry", "ENTRY_MODERATION_STATUS");
	}

	private function getEnumValues($peer, $prefix)
	{
		$reflectionClass = new ReflectionClass($peer);
		$allConsts = $reflectionClass->getConstants();
		$consts = array();
		foreach($allConsts as $key => $value)
		{
			if (strpos($key, $prefix) === 0)
			{
				$consts[str_replace($prefix.'_', '', $key)] = $value;
			}
		}
		return $consts;
	}
}
?>