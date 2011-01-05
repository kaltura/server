<?php
class myPartnerRegistration
{
	private $partnerParentId = null;

	public function __construct( $partnerParentId = null )
	{
		$this->partnerParentId = $partnerParentId;	
	}
	
	const KALTURA_SUPPORT = "wikisupport@kaltura.com";
	  
	private function str_makerand ($minlength, $maxlength, $useupper, $usespecial, $usenumbers)
	{
		/*
		Description: string str_makerand(int $minlength, int $maxlength, bool $useupper, bool $usespecial, bool $usenumbers)
		returns a randomly generated string of length between $minlength and $maxlength inclusively.

		Notes:
		- If $useupper is true uppercase characters will be used; if false they will be excluded.
		- If $usespecial is true special characters will be used; if false they will be excluded.
		- If $usenumbers is true numerical characters will be used; if false they will be excluded.
		- If $minlength is equal to $maxlength a string of length $maxlength will be returned.
		- Not all special characters are included since they could cause parse errors with queries.
		*/

		$charset = "abcdefghijklmnopqrstuvwxyz";
		if ($useupper) $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		if ($usenumbers) $charset .= "0123456789";
		if ($usespecial) $charset .= "~@#$%^*()_+-={}|]["; // Note: using all special characters this reads: "~!@#$%^&*()_+`-={}|\\]?[\":;'><,./";
		if ($minlength > $maxlength) $length = mt_rand ($maxlength, $minlength);
		else $length = mt_rand ($minlength, $maxlength);
		$key = "";
		for ($i=0; $i<$length; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
		return $key;
	}

	const KALTURAS_CMS_REGISTRATION_CONFIRMATION = 50;
	const KALTURAS_DEFAULT_REGISTRATION_CONFIRMATION = 54;
	const KALTURAS_EXISTING_USER_REGISTRATION_CONFIRMATION = 55;
	const KALTURAS_DEFAULT_EXISTING_USER_REGISTRATION_CONFIRMATION = 56;
	
	public function sendRegistrationInformationForPartner ($partner, $skip_emails, $existingUser )
	{
		// email the client with this info
		$adminKuser = kuserPeer::retrieveByPK($partner->getAccountOwnerKuserId());
		$this->sendRegistrationInformation($partner, $adminKuser, $existingUser, null, $partner->getType());
											
		if ( !$skip_emails && kConf::hasParam("report_partner_registration") && kConf::get("report_partner_registration")) 
		{											
			// email the wikisupport@kaltura.com  with this info
			$this->sendRegistrationInformation($partner, $adminKuser, $existingUser, self::KALTURA_SUPPORT );

			// if need to hook into SalesForce - this is the place
			if ( include_once ( "mySalesForceUtils.class.php" ) )
			{
				mySalesForceUtils::sendRegistrationInformationToSalesforce($partner);
			}
			
			// if need to hook into Marketo - this is the place
			if ( include_once ( "myMarketoUtils.class.php" ) )
			{
				myMarketoUtils::sendRegistrationInformation($partner);
			}
		}
	}
	


	private  function sendRegistrationInformation(Partner $partner, kuser $adminKuser, $existingUser, $recipient_email = null , $partner_type = 1 )
	{
		$mailType = null;
		$bodyParams = array();
		$partnerId = $partner->getId();
		$userName = $adminKuser->getFullName();
		if (!$userName) { $userName = $adminKuser->getPuserId(); }
		$loginEmail = $adminKuser->getEmail();
		$loginData = $adminKuser->getLoginData();
		$hashKey = $loginData->getNewHashKeyIfCurrentInvalid();
		$resetPasswordLink = UserLoginDataPeer::getPassResetLink($hashKey);
		$kmcLink = trim(kConf::get('apphome_url'), '/').'/kmc';
		$contactLink = kConf::get('contact_url');
		$contactPhone = kConf::get('contact_phone_number');		
		$beginnersGuideLink = kConf::get('beginners_tutorial_url');
		$quickStartGuideLink = kConf::get('quick_start_guide_url');
		$forumsLink = kConf::get('forum_url');
		if ( $recipient_email == null ) $recipient_email = $loginEmail;
		$unsubscribeLink = kConf::get('unsubscribe_mail_url').$recipient_email;
		
	 	// send the $cms_email,$cms_password, TWICE !
	 	if(kConf::get('kaltura_installation_type') == 'CE')	{
			$partner_type = 1;
		}
		
		switch($partner_type) { // send different email for different partner types
			case 1: // KMC signup
				if ($existingUser) {
					$mailType = self::KALTURAS_EXISTING_USER_REGISTRATION_CONFIRMATION;
					$bodyParams = array($userName, $loginEmail, $partnerId, $contactLink, $contactPhone, $beginnersGuideLink, $quickStartGuideLink, $forumsLink, $unsubscribeLink);
				}
				else {
					$mailType = self::KALTURAS_CMS_REGISTRATION_CONFIRMATION;
					$bodyParams = array($userName, $loginEmail, $partnerId, $resetPasswordLink, $kmcLink, $contactLink, $contactPhone, $beginnersGuideLink, $quickStartGuideLink, $forumsLink, $unsubscribeLink);
				}
				break;
			default: // all others
			 	if ($existingUser) {
					$mailType = self::KALTURAS_DEFAULT_EXISTING_USER_REGISTRATION_CONFIRMATION;
					$bodyParams = array($userName, $loginEmail, $partnerId, $contactLink, $contactPhone, $beginnersGuideLink, $quickStartGuideLink, $forumsLink, $unsubscribeLink);
				}
				else {
					$mailType = self::KALTURAS_DEFAULT_REGISTRATION_CONFIRMATION;
					$bodyParams = array($userName, $loginEmail, $partnerId, $resetPasswordLink, $kmcLink, $contactLink, $contactPhone, $beginnersGuideLink, $quickStartGuideLink, $forumsLink, $unsubscribeLink);
				}
				break;
		}
		
		kJobsManager::addMailJob(
			null, 
			0, 
			$partnerId, 
			$mailType, 
			kMailJobData::MAIL_PRIORITY_NORMAL, 
			kConf::get ("partner_registration_confirmation_email" ), 
			kConf::get ("partner_registration_confirmation_name" ), 
			$recipient_email, 
			$bodyParams);
	}

	private function createNewPartner( $parnter_name , $contact, $email, $ID_is_for, $SDK_terms_agreement, $description, $website_url , $password = null , $partner = null )
	{
		$secret = md5($this->str_makerand(5,10,true, false, true));
		$admin_secret = md5($this->str_makerand(5,10,true, false, true));

		$newPartner = new Partner();
		$newPartner->setAdminSecret($admin_secret);
		$newPartner->setSecret($secret);
		$newPartner->setAdminName($contact);
		$newPartner->setAdminEmail($email);
		$newPartner->setUrl1($website_url);
		if ($ID_is_for === "commercial_use" || $ID_is_for === CommercialUseType::COMMERCIAL_USE)
			$newPartner->setCommercialUse(true);
		else //($ID_is_for == "non-commercial_use") || $ID_is_for === CommercialUseType::NON_COMMERCIAL_USE)
			$newPartner->setCommercialUse(false);
		$newPartner->setDescription($description);
		$newPartner->setKsMaxExpiryInSeconds(86400);
		$newPartner->setModerateContent(false);
		$newPartner->setNotify(false);
		$newPartner->setAppearInSearch(mySearchUtils::DISPLAY_IN_SEARCH_KALTURA_NETWORK);
		$newPartner->setIsFirstLogin(true);
		/* fix drupal5 module partner type */
		//var_dump($description);
		
		if ( $this->partnerParentId )
		{
			// this is a child partner of some VAR/partner GROUP
			$newPartner->setPartnerParentId( $this->partnerParentId );
			$newPartner->setMonitorUsage(0);
		}
		
		if(substr_count($description, 'Drupal module|'))
		{
			$newPartner->setType(102);
			if($partner) $partner->setType(102);
		}
		
		if ( $partner )
		{
			if ( $partner->getType() ) $newPartner->setType ( $partner->getType() );
			if ( $partner->getContentCategories() ) $newPartner->setContentCategories( $partner->getContentCategories() );
			if ( $partner->getPhone() ) $newPartner->setPhone( $partner->getPhone() );
			if ( $partner->getDescribeYourself() ) $newPartner->setDescribeYourself( $partner->getDescribeYourself() );
			if ( $partner->getAdultContent() ) $newPartner->setAdultContent( $partner->getAdultContent() );
			if ( $partner->getDefConversionProfileType() ) $newPartner->setDefConversionProfileType( $partner->getDefConversionProfileType() );
		}
		$newPartner->save();

		// if name was left empty - which should not happen - use id as name
		if ( ! $parnter_name ) $parnter_name = $newPartner->getId();
		$newPartner->setPartnerName( $parnter_name );
		$newPartner->setPrefix($newPartner->getId());
		$newPartner->setPartnerAlias(md5($newPartner->getId().'kaltura partner'));

		// set default conversion profile for trial accounts
		if ($newPartner->getType() == Partner::PARTNER_TYPE_KMC)
		{
			$newPartner->setDefConversionProfileType( ConversionProfile::DEFAULT_TRIAL_COVERSION_PROFILE_TYPE );
		}
		
		$newPartner->save();

		$partner_id = $newPartner->getId();
		widget::createDefaultWidgetForPartner( $partner_id , $this->createNewSubPartner ( $newPartner ) );
		
		$fromPartner = PartnerPeer::retrieveByPK(kConf::get("template_partner_id"));
	 	if (!$fromPartner)
	 		KalturaLog::log("Template content partner was not found!");
 		else
	 		myPartnerUtils::copyTemplateContent($fromPartner, $newPartner, true);
		
		$newPartner->setKmcVersion('3');
		$newPartner->save();
		
		return $newPartner;
	}

	private function createNewSubPartner($newPartner)
	{
		$pid = $newPartner->getId();
		$subpid = ($pid*100);

		// TODO: save this, when implementation is ready

		return $subpid;
	}

	// if the adminKuser already exists - use his password - it should always be the same one for a given email !!
	private function createNewAdminKuser($newPartner , $existing_password )
	{
		// generate a new password if not given
		if ( $existing_password != null ) {
			$password = $existing_password;
		}
		else {
			$password = UserLoginDataPeer::generateNewPassword();
		}
		
		// create the user
		$kuser = new kuser();
		$kuser->setEmail($newPartner->getAdminEmail());
		
		list($firstName, $lastName) = kString::nameSplit($newPartner->getAdminName());
		$kuser->setFirstName($firstName);
		$kuser->setLastName($lastName);

		$kuser->setPartnerId($newPartner->getId());
		$kuser->setIsAdmin(true);
		$kuser->setPuserId($newPartner->getAdminEmail());

		$kuser = kuserPeer::addUser($kuser, $password, false, false); //this also saves the kuser and adds a user_login_data record
		
		$loginData = UserLoginDataPeer::retrieveByPK($kuser->getLoginDataId());
	
		return array($password, $loginData->getPasswordHashKey(), $kuser->getId());
	}

	public function initNewPartner($partner_name , $contact, $email, $ID_is_for, $SDK_terms_agreement, $description, $website_url , $password = null , $partner = null )
	{
		// Validate input fields
		if( $partner_name == "" )
			throw new SignupException("Please fill in the Partner's name" , SignupException::INVALID_FIELD_VALUE);
			
		if ($contact == "")
			throw new SignupException('Please fill in Administrator\'s details', SignupException::INVALID_FIELD_VALUE);

		if ($email == "")
			throw new SignupException('Please fill in Administrator\'s Email Address', SignupException::INVALID_FIELD_VALUE);
		
			
		if(!kString::isEmailString($email))
			throw new SignupException('Invalid email address', SignupException::INVALID_FIELD_VALUE);

		if ($description == "")
			throw new SignupException('Please fill in description', SignupException::INVALID_FIELD_VALUE);

		if ( ($ID_is_for !== CommercialUseType::COMMERCIAL_USE) && ($ID_is_for !== CommercialUseType::NON_COMMERCIAL_USE) &&
			 ($ID_is_for !== "commercial_use") && ($ID_is_for !== "non-commercial_use") ) //string values left for backward compatibility
			throw new SignupException('Invalid field value.\nSorry.', SignupException::UNKNOWN_ERROR);

		if ($SDK_terms_agreement != "yes")
			throw new SignupException('You haven`t approved Terms & Conds.', SignupException::INVALID_FIELD_VALUE);
						
		// TODO: log request
		$newPartner = NULL;
		$newSubPartner = NULL;
		try {
			// create the new partner
			$newPartner = $this->createNewPartner($partner_name , $contact, $email, $ID_is_for, $SDK_terms_agreement, $description, $website_url , $password , $partner );

			// create the sub partner
			// TODO: when ready, add here the saving of this value, currently it will be only
			// a random value, being passed to the user, and never saved
			$newSubPartnerId = $this->createNewSubPartner($newPartner);

			// create a new admin_kuser for the user,
			// so he will be able to login to the system (including permissions)
			list($newAdminKuserPassword, $newPassHashKey, $kuserId) = $this->createNewAdminKuser($newPartner , $password );
			$newPartner->setAccountOwnerKuserId($kuserId);
			
			$this->setAllTemplateEntriesToAdminKuser($newPartner->getId(), $kuserId);

			return array($newPartner->getId(), $newSubPartnerId, $newAdminKuserPassword, $newPassHashKey);
		}
		catch (Exception $e) {
			//TODO: revert all changes, depending where and why we failed

			throw $e;
		}
	}
	
	private function setAllTemplateEntriesToAdminKuser($partnerId, $kuserId)
	{
		$c = new Criteria();
		$c->addAnd(entryPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
		entryPeer::setUseCriteriaFilter(false);
		$allEntries = entryPeer::doSelect($c);
		entryPeer::setUseCriteriaFilter(true);
		foreach ($allEntries as $entry)
		{
			$entry->setKuserId($kuserId);
			$entry->save();
		}
	}
}


class SignupException extends Exception
{
    const UNKNOWN_ERROR = 500;
    const INVALID_FIELD_VALUE = 501;
    const EMAIL_ALREADY_EXISTS = 502;
    const PASSWORD_STRUCTURE_INVALID = 503;

    // Redefine the exception so message/code isn't optional
    public function __construct($message, $code) {
        // some code

        // make sure everything is assigned properly
        parent::__construct($message, $code);

    }
}
