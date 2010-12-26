<?php

require_once( 'myContentStorage.class.php');
require_once( 'dateUtils.class.php');
require_once( 'model/ktagword.class.php');
require_once ( "myStatisticsMgr.class.php");

/**
 * Subclass for representing a row from the 'kuser' table.
 *
 * 
 *
 * @package lib.model
 */ 
class kuser extends Basekuser
{
	const ANONYMOUS_PUSER_ID = "KALANONYM";
	
	const MINIMUM_ID_TO_DISPLAY = 8999;
		
	const KUSER_KALTURA = 0;
	  
	// different sort orders for browsing kswhos
	const KUSER_SORT_MOST_VIEWED = 1;  
	const KUSER_SORT_MOST_RECENT = 2;  
	const KUSER_SORT_NAME = 3;
	const KUSER_SORT_AGE = 4;
	const KUSER_SORT_COUNTRY = 5;
	const KUSER_SORT_CITY = 6;
	const KUSER_SORT_GENDER = 7;
	const KUSER_SORT_MOST_FANS = 8;
	const KUSER_SORT_MOST_ENTRIES = 9;
	const KUSER_SORT_PRODUCED_KSHOWS = 10;
	
	private $roughcut_count = -1;
	
	public static function getColumnNames()	{	return array ( 
		"screen_name" , "full_name" , "url_list" , "tags" , 
		"about_me" , "network_highschool" , "network_college" ,"network_other") ; 
	}
	public static function getSearchableColumnName () { return "search_text" ; }

	public function save(PropelPDO $con = null)
	{
		myPartnerUtils::setPartnerIdForObj( $this );
		
		mySearchUtils::setDisplayInSearch( $this );
		
		if (!$this->getIsAdmin()) {
			$this->setIsAdmin(false);
		}
					
		return parent::save( $con );	
	}
	

	/* (non-PHPdoc)
	 * @see lib/model/om/Basekuser#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$objectDeleted = false;
		if($this->isColumnModified(kuserPeer::STATUS) && $this->getStatus() == KuserStatus::DELETED) {
			$objectDeleted = true;
		}
			
		$oldLoginDataId = null;
		if ($this->isColumnModified(kuserPeer::LOGIN_DATA_ID)) {
			$oldLoginDataId = $this->oldColumnsValues[kuserPeer::LOGIN_DATA_ID];
		}
		
		if ($this->isColumnModified(kuserPeer::EMAIL) && $this->isRootUser() && !is_null($kuser->oldColumnsValues[kuserPeer::EMAIL])) {
			myPartnerUtils::emailChangedEmail($this->getPartnerId(), $this->oldColumnsValues[kuserPeer::EMAIL], $this->getEmail(), $this->getPartner()->getName() , PartnerPeer::KALTURAS_PARTNER_EMAIL_CHANGE );
		}
					
		$ret = parent::postUpdate($con);
		
		if ($objectDeleted)
		{
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			// if user is deleted - check if shoult also delete login data
			UserLoginDataPeer::notifyOneLessUser($this->getLoginDataId());
		}
		else if (!is_null($oldLoginDataId) && is_null($this->getLoginDataId()))
		{
			// if login was disabled - check if should also delete login data
			UserLoginDataPeer::notifyOneLessUser($oldLoginDataId);
		}
			
		return $ret;
	}
		
	public function setRoughcutCount ( $count )
	{
		$this->roughcut_count = $count ;
	}
	
	// TODO - move implementation to kuserPeer - i'm not doing so now because there are changes i don't want to commit 
	public function getRoughcutCount ()
	{
		if ( $this->roughcut_count == -1  )
		{
			$c = new Criteria();
			$c->add ( entryPeer::TYPE , entryType::MIX );
			$c->add ( entryPeer::KUSER_ID , $this->getId() );
			$this->roughcut_count = entryPeer::doCount( $c );
		}
		return $this->roughcut_count;
	}

	
	static public function getKuserById ( $id )
	{
		$c = new Criteria();
		$c->add(kuserPeer::ID, $id );
		return kuserPeer::doSelectOne($c);
	}

	// TODO - PERFORMANCE DB - move to use cache !!
	// will increment the views by 1
	public function incViews ( $should_save = true  )
	{
		myStatisticsMgr::incKuserViews( $this );
/*		
		$v = $this->getViews ( );
		if ( ! is_numeric( $v ) ) $v=0;
		$this->setViews( $v + 1 );
		
		if ( $should_save) $this->save();
*/
	}

	// TODO - PERFORMANCE DB - move to use cache !!
	// will update the number of fans of kuser according the fans table
	// this should not be called ! - it is handled via myStatisticsMgr
	private  function updateFans ( $should_save = true )
	{
		// select all the people who favor me (ignore privacy)
		$c = new Criteria();
		$c->add(favoritePeer::SUBJECT_ID, $this->getId());
		$c->add(favoritePeer::SUBJECT_TYPE, favorite::SUBJECT_TYPE_USER);
//		$c->add(favoritePeer::PRIVACY, $privacy);
		$c->setDistinct();		
		$fan_count = favoritePeer::doCount( $c );
		$this->setFans( $fan_count );
		
		if ( $should_save) $this->save();
		
		return $fan_count;
	}

/*	
	// TODO - PERFORMANCE DB - move to use cache !!
	// will update the number of entries of kuser 
	// should be called every time this kuser contributes
	public function incEntries ( $should_save = true )
	{
		$v = $this->getEntries();
		if ( ! is_numeric( $v ) ) $v=0;
		$this->setEntries( $v + 1 );
		
		if ( $should_save) $this->save();
	}

	// TODO - PERFORMANCE DB - move to use cache !!
	// will update the number of produced_kshows of kuser 
	// should be called every time this kuser publishes a kshow
	public function incProducedKshows ( $should_save = true )
	{
		$v = $this->getProducedKshows();
		if ( ! is_numeric( $v ) ) $v=0;
		$this->setProducedKshows( $v + 1 );
		
		if ( $should_save) $this->save();
	}

*/

	public function getBlockEmailStr()
	{
		return myBlockedEmailUtils::createBlockEmailStr ( $this->getEmail() );	
	}

	public function getBlockEmailUrl()
	{
		return myBlockedEmailUtils::createBlockEmailUrl ( $this->getEmail() );	
	}
		
	public function getPicturePath() 
	{ 
		$picfile = $this->getPicture();
		$picfile = substr( $picfile, strpos( $picfile, '^'));		
		return myContentStorage::getGeneralEntityPath("kuser/pic", $this->getId(), $this->getId(), $picfile);
	}

	public function setPicture($filename )
	{
		if (defined("KALTURA_API_V3"))
		{
			parent::setPicture($filename);
			return;
		}
		
		parent::setPicture( myContentStorage::generateRandomFileName( $filename, $this->getPicture() ) );
	}

	public function getPictureUrl() 
	{ 
		if ( parent::getPicture() == null ) return "";
		$path = $this->getPicturePath ( );
		$url = requestUtils::getHost() . $path ;
		return $url;
	}
	
	public function setTags($tags , $update_db = true )
	{
		if ($this->tags !== $tags) {
			$tags = ktagword::updateTags($this->tags, $tags , $update_db );
			parent::setTags($tags);
		}
	} 
	

	public function getCreatedAtAsInt ()
	{
		return $this->getCreatedAt( null );
	}

	public function getUpdateAtAsInt ()
	{
		return $this->getUpdatedAt( null );
	}
	
	public function getFormattedCreatedAt( $format = dateUtils::KALTURA_FORMAT )
	{
		return dateUtils::formatKalturaDate( $this , 'getCreatedAt' , $format );
	}

	public function getFormattedUpdatedAt( $format = dateUtils::KALTURA_FORMAT )
	{
		return dateUtils::formatKalturaDate( $this , 'getUpdatedAt' , $format );
	}
	
	public function getHomepageURL()
	{
		if ( $this->getURLlist() != null && $this->getURLlist() != "" )
		{
			$urls = explode( "|" , $this->getURLlist()  );
			if ( count ($urls) > 0 ) return $urls[0];
		}
		else return null;
	}

	public function getMyspaceURL()
	{
		if ( $this->getUrlList() != null && $this->getUrlList() != "" )
		{
			$urls = explode( "|" , $this->getURLlist()  );
			if ( count ($urls) > 1 ) return $urls[1];
		}
		else return null;
	}
	
	public function setHomepageMyspace( $homepage, $myspace )
	{
		$this->setUrlList( kString::add_http( $homepage ).'|'. kString::add_http( $myspace) );
	}
	
	public function getIMArray()
	{
		if ( $this->getImList() != null && $this->getImList() != "" )
		{
			$ims = explode( "|" , $this->getImList()  );
			if ( count( $ims ) == 6 )
			{	$ims_final = array( "AIM"=>$ims[0], "MSN"=>$ims[1], "ICQ"=>$ims[2], "Skype"=>$ims[3], "Yahoo"=>$ims[4], "Google"=>$ims[5]);
				return $ims_final; }
				else return null;
		}
		else return null;
	}
	
	public function setIMArray( $aim, $msn, $icq, $skype, $yahoo, $google)
	{
		$this->setImList( $aim.'|'.$msn.'|'.$icq.'|'.$skype.'|'.$yahoo.'|'.$google);
	}
	
	public function getMobileArray()
	{
		if ( $this->getMobileNum() != null && $this->getMobileNum() != "" )
		{
			$mobilearr = explode( "|" , $this->getMobileNum()  );
			if ( count( $mobilearr ) == 3 )
			{	$mobile_final = array( "countrycode"=>$mobilearr[0], "areacode"=>$mobilearr[1], "number"=>$mobilearr[2]);
				return $mobile_final; }
				else return null;
		}
		else return null;
	}
	
	public function setMobileArray( $country, $area, $number )
	{
		$this->setMobileNum( $country.'|'.$area.'|'.$number);
		
	}
	
	
	public function getTagsArray()
	{
		return ktagword::getTagsArray ( $this->getTags() );
	}
	
	public function getDateOfBirth($format = '%x')
	{
		$dateStr = parent::getDateOfBirth();
		if ($dateStr  === null)
			return null;
			
		return strtotime($dateStr); 
	}
	
	public function setDateOfBirth($v)
	{
		// keep only the date from the timestamp
		$year = (int)date("Y", $v);
		$day = (int)date("j", $v);
		$month = (int)date("n", $v);
		$dateOnly = mktime(0, 0, 0, $month, $day, $year);
		parent::setDateOfBirth(date("Y-m-d", $dateOnly));
	}
	
	/**
	 * caluculates the age of the kuser according to his date_of_birth
	 */
	public function getAge ()
	{
  		$now_year = date("Y");
		$dateFormat = new sfDateFormat();
		$dob = $this->getDateOfBirth();
		if ( $dob != null && $dob != "0000-00-00" )
		{
			$yob = $dateFormat->format( $dob , 'Y');
			return $now_year-$yob;
		} 
		return null;
	}

	public function setGenderByText ( $gender_text )
	{
		$gender_text = strtolower ( $gender_text );
		if ( $gender_text == "male" ) $this->setGender( 1 );
		elseif ( $gender_text == "female" ) $this->setGender( 2 );
		else $this->setGender( 0 );
	}
	
	public function getGenderText ()
	{
		$gender = $this->getGender();
		if ( $gender === 1 ) return "Male";
		if ( $gender === 2 ) return "Female";
		return "";
	}
	
	public function setCountryByLongIp ( $long_ip )
	{
		$ip = long2ip ( $this->getRegistrationIp() ) ;
		return $this->setCountyByIp ( $ip );
	}
	
	public function setCountyByIp ( $ip )
	{
		//long2ip ( $user->getRegistrationIp() ) ;
		// for first time use, geocode registration IP address
		$myGeoCoder = new myIPGeocoder();
		$country_code = $myGeoCoder->iptocountry( $ip );
		if ( $country_code == null || $country_code == "" || $country_code == "ZZ" ) $country_code  = "US"; //if we can't identify, assume the US
		if ( $this->getCountry() == "" ) $this->setCountry( $country_code );
		
		return $this->getCountry();
	}
	
	public function getLastKshowId( )
	{
		// return the last kshow_id created by this kuser
		$c = new Criteria();
		$c->add ( kshowPeer::PRODUCER_ID , $this->getId() );
		$c->addDescendingOrderByColumn( kshowPeer::ID );
		$kshow = kshowPeer::doSelectOne();

		if ( $kshow )
		{
			return $kshow->getId();
		}
		else
		{
			return 0;
		}
	}

	public function getLastKshowUrl( )
	{
		// return the last kshow_id created by this kuser
		$c = new Criteria();
		$c->add ( kshowPeer::PRODUCER_ID , $this->getId() );
		$c->addDescendingOrderByColumn( kshowPeer::ID );
		$kshow = kshowPeer::doSelectOne( $c );
				
		$host = requestUtils::getHost() . "/";
		
		if ( $kshow )
		{
			return "<a href='" . $host . "/id/" . $kshow->getId() . "'>" . $kshow->getName() . "</a>";
		}
		else
		{
			// This should never happen
			return "<a href='" . $host . "'>Kaltura</a>";
		}
	}
	
	
	public function disable ( $disable_all_content = false )
	{
		$this->setStatus ( KuserStatus::BLOCKED );
	}

	public function moderate ($new_moderation_status) 
	{
		$error_msg = "Moderation status [$new_moderation_status] not supported by user object	";
		switch($new_moderation_status) 
		{
			case moderation::MODERATION_STATUS_APPROVED:
				throw new Exception($error_msg);
				break;			
			case moderation::MODERATION_STATUS_BLOCK:
				myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_USER_BANNED , $this );		
				break;
			case moderation::MODERATION_STATUS_DELETE:
				throw new Exception($error_msg);
				break;
			case moderation::MODERATION_STATUS_PENDING:
				throw new Exception($error_msg);
				break;
			case moderation::MODERATION_STATUS_REVIEW:
				myNotificationMgr::createNotification(kNotificationJobData::NOTIFICATION_TYPE_USER_BANNED , $this );
//				throw new Exception($error_msg);
				break;
			default:
				throw new Exception($error_msg);
				break;
		}
		
		$this->save();
	}
	
	// TODO - fix when we enable the blocking of users
	public function getModerationStatus()
	{
//		if ( $this->getStatus() == KuserStatus::BLOCKED )
//			return moderation::MODERATION_STATUS_BLOCK;
		return moderation::MODERATION_STATUS_APPROVED;
	}

	
	public function getPuserId()
	{
		$puserId = parent::getPuserId();
		if (!$puserId) {
			$puserId = PuserKuserPeer::getPuserIdFromKuserId ( $this->getPartnerId(), $this->getId() );
		}
		
		return $puserId;
	}
	
	// this will make sure that the extra data set in the search_text won't leak out 
	public function getSearchText()
	{
		return mySearchUtils::removePartner( parent::getSearchText() );
	}

	public function getSearchTextRaw()
	{
		return parent::getSearchText();
	}
	
	public function getKuserId()
	{
		return $this->getId();
	}
	
	/**
	 * Set last_login_time parameter to $time (in custom_data)
	 * @param int $time timestamp
	 */
	public function setLastLoginTime($time)
	{
		$this->putInCustomData('last_login_time', $time);
	}
	
	/**
	 * @return last_login_time parameter from custom_data
	 */
	public function getLastLoginTime()
	{
		return $this->getFromCustomData('last_login_time');
	}
	
	//TODO: check if needed
	public function getIsAdmin()
	{
		return parent::getisAdmin() == true;
	}
	
	
	/**
	 * @return Kuser's full name = first_name + last_name
	 */
	public function getFullName()
	{
		if ($this->getFirstName()) {
			return trim($this->getFirstName().' '.$this->getLastName());
		}
		else {
			// full_name is deprecated - this is for backward compatibiliy and for migration
			KalturaLog::ALERT('Field [full_name] on object [kuser] is deprecated but still being read');
			return parent::getFullName();
		}
	}
	
	
	private function setStatusUpdatedAt($v)
	{		
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		$curValue = $this->getStatusUpdatedAt();
		if ( $curValue !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($curValue !== null && $tmpDt = new DateTime($curValue)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$newValue = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->putInCustomData('status_updated_at', $newValue, null);
			}
		} // if either are not null

		return $this;
	}
	
	public function getStatusUpdatedAt($format = 'Y-m-d H:i:s')
	{
		$value = $this->getFromCustomData('status_updated_at', null, null);
		
		if ($value === null) {
			return null;
		}

		if ($value === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($value);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($value, true), $x);
			}
		}

		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}
	
	
	private function setDeletedAt($v)
	{		
		// we treat '' as NULL for temporal objects because DateTime('') == DateTime('now')
		// -- which is unexpected, to say the least.
		if ($v === null || $v === '') {
			$dt = null;
		} elseif ($v instanceof DateTime) {
			$dt = $v;
		} else {
			// some string/numeric value passed; we normalize that so that we can
			// validate it.
			try {
				if (is_numeric($v)) { // if it's a unix timestamp
					$dt = new DateTime('@'.$v, new DateTimeZone('UTC'));
					// We have to explicitly specify and then change the time zone because of a
					// DateTime bug: http://bugs.php.net/bug.php?id=43003
					$dt->setTimeZone(new DateTimeZone(date_default_timezone_get()));
				} else {
					$dt = new DateTime($v);
				}
			} catch (Exception $x) {
				throw new PropelException('Error parsing date/time value: ' . var_export($v, true), $x);
			}
		}

		$curValue = $this->getDeletedAt();
		if ( $curValue !== null || $dt !== null ) {
			// (nested ifs are a little easier to read in this case)

			$currNorm = ($curValue !== null && $tmpDt = new DateTime($curValue)) ? $tmpDt->format('Y-m-d H:i:s') : null;
			$newNorm = ($dt !== null) ? $dt->format('Y-m-d H:i:s') : null;

			if ( ($currNorm !== $newNorm) // normalized values don't match 
					)
			{
				$newValue = ($dt ? $dt->format('Y-m-d H:i:s') : null);
				$this->putInCustomData('deleted_at', $newValue, null);
			}
		} // if either are not null

		return $this;
	}
	
	public function getDeletedAt($format = 'Y-m-d H:i:s')
	{
		$value = $this->getFromCustomData('deleted_at', null, null);
		
		if ($value === null) {
			return null;
		}

		if ($value === '0000-00-00 00:00:00') {
			// while technically this is not a default value of NULL,
			// this seems to be closest in meaning.
			return null;
		} else {
			try {
				$dt = new DateTime($value);
			} catch (Exception $x) {
				throw new PropelException("Internally stored date/time/timestamp value could not be converted to DateTime: " . var_export($value, true), $x);
			}
		}

		if ($format === null) {
			// We cast here to maintain BC in API; obviously we will lose data if we're dealing with pre-/post-epoch dates.
			return (int) $dt->format('U');
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $dt->format('U'));
		} else {
			return $dt->format($format);
		}
	}
	
	/**
	 * Set status and statusUpdatedAt fields
	 * @see Basekuser::setStatus()
	 * @throws kUserException::CANNOT_DELETE_ROOT_ADMIN_USER
	 */
	public function setStatus($status)
	{
		if ($status == KuserStatus::DELETED && $this->isRootUser()) {
			throw new kUserException('', kUserException::CANNOT_DELETE_ROOT_ADMIN_USER);
		}
		
		parent::setStatus($status);
		$this->setStatusUpdatedAt(time());
		if ($status == KuserStatus::DELETED) {
			$this->setDeletedAt(time());
		}
	}
	
	/**
	 * Return user's login data object if valid
	 */
	public function getLoginData()
	{
		$loginDataId = $this->getLoginDataId();
		if (!$loginDataId) {
			return null;
		}
		return UserLoginDataPeer::retrieveByPK($loginDataId);
	}
	

	
	// -- start of deprecated functions
	
	public function setSalt($v)
	{
		// salt column is deprecated
		KalturaLog::ALERT('Field [salt] on object [kuser] is deprecated');
		throw new Exception('Field [salt] on object [kuser] is deprecated');
	}
	
	public function setSha1Password($v)
	{
		// sha1_password column is deprecated
		KalturaLog::ALERT('Field [sha1_password] on object [kuser] is deprecated - trace: ');
		throw new Exception('Field [sha1_password] on object [kuser] is deprecated');
	}
	
	public function getSalt()
	{
		// salt column is deprecated
		KalturaLog::ALERT('Field [salt] on object [kuser] is deprecated - getSalt should be removed from schema after migration');
		return parent::getSalt();
	}
	
	public function getSha1Password()
	{
		// sha1_password column is deprecated
		KalturaLog::ALERT('Field [sha1_password] on object [kuser] is deprecated - getSha1Password should be removed from schema after migration');
		return parent::getSha1Password();
	}
	
	public function setFullName($v)
	{
		// full_name column is deprecated
		KalturaLog::ALERT('Field [full_name] on object [kuser] is deprecated');
		list($firstName, $lastName) = kString::nameSplit($v);
		$this->setFirstName($firstName);
		$this->setLastName($lastName);
	}
	
	// -- end of deprecated functions
	
	
	/**
	 * Disable user login
	 * @throws kUserException::USER_LOGIN_ALREADY_DISABLED
	 */
	public function disableLogin()
	{
		if (!$this->getLoginDataId())
		{
			throw new kUserException('', kUserException::USER_LOGIN_ALREADY_DISABLED);
		}
		
		$loginDataId = $this->getLoginDataId();
		$this->setLoginDataId(null);
		$this->save();
		
		return true;	
	}
	
	/**
	 * Enable user login 
	 * @param string $loginId
	 * @param string $password
	 * @param bool $checkPasswordStructure
	 * @throws kUserException::USER_LOGIN_ALREADY_ENABLED
	 * @throws kUserException::INVALID_EMAIL
	 * @throws kUserException::INVALID_PARTNER
	 * @throws kUserException::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED
	 * @throws kUserException::PASSWORD_STRUCTURE_INVALID
	 * @throws kUserException::LOGIN_ID_ALREADY_USED
	 * @throws kUserException::USER_EXISTS_WITH_DIFFERENT_PASSWORD
	 * @throws kUserException::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED
	 */
	public function enableLogin($loginId, $password, $checkPasswordStructure = true)
	{
		if ($this->getLoginDataId())
		{
			throw new kUserException('', kUserException::USER_LOGIN_ALREADY_ENABLED);
		}
		
		$loginData = UserLoginDataPeer::addlogindata($loginId, $password, $this->getPartnerId(), $this->getFirstName(), $this->getLastName(), $this->getIsAdmin(), $checkPasswordStructure);	
		if (!$loginData)
		{
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		$this->setLoginDataId($loginData->getId());
		return $this;
	}
	
	public function isRootUser()
	{
		$partner = $this->getPartner();
		if (!$partner) {
			return false;
		}
		else {
			return $this->getId() == $partner->getAccountOwnerKuserId();
		}
	}
	
	/**
	 * @return Partner
	 */
	public function getPartner()
	{
		$partner = PartnerPeer::retrieveByPK($this->getPartnerId());
		return $partner;
	}
	
	// ----------------------------------------
	// -- start of user role handling functions
	// ----------------------------------------
	
	/**
	 * Add a user role to the current kuser
	 * @param int $userRoleId
	 * @throws kPermissionException::PERMISSION_ITEM_NOT_FOUND
	 */
	public function addUserRole($userRoleId, $save = true)
	{
		// check if user role item exists
		$userRole = UserRolePeer::retrieveByPK($userRoleId);
		if (!$userRole) {
			throw new kPermissionException("A user role with ID [$userRoleId] does not exist", kPermissionException::USER_ROLE_NOT_FOUND);
		}
		
		// check if role is already associated to the current kuser
		$kuserToRole = KuserToUserRolePeer::getByKuserAndUserRoleIds($this->getId(), $userRoleId);
		if ($kuserToRole) {
			KalturaLog::notice('Kuser with ID ['.$this->getId().'] is already associated with role id ['.$userRoleId.']');
			return true;
		}
		
		// add role to current kuser
		$kuserToRole = new KuserToUserRole();
		$kuserToRole->setUserRole($userRole);
		$this->addKuserToUserRole($kuserToRole);
		if ($save) {
			$this->save();
		}
		return true;
	}
	
	/**
	 * @return string String of role IDs associated to the current kuser
	 */
	public function getUserRoleIds()
	{
		$ids = array();
		$items = $this->getKuserToUserRoles();
		if (!$items) {
			return null;
		}		
		foreach ($items as $item) {
			$ids[] = $item->getUserRoleId();
		}
		return implode(',', $ids);
	}
	
	/**
	 * @return array Array of role IDs associated to the current kuser
	 */
	public function getUserRoleNames()
	{
		$names = array();
		$ids = $this->getUserRoleIds();
		$ids = explode(',', $ids);
		foreach ($ids as $id) {
			$role = UserRolePeer::retrieveByPK($id);
			$names[] = $role->getName();
		}
		return implode(',', $names);
	}

	/**
	 * Remove the given user role from the current kuser
	 * @param int $permissionItemId
	 */
	public function removeUserRole($userRoleId)
	{		
		// check if role is already associated to the kuser
		$kuserToRole = KuserToUserRolePeer::getByKuserAndUserRoleIds($this->getId(), $userRoleId);
		if (!$kuserToRole) {
			KalturaLog::notice('Kuser with id ['.$this->getId().'] is not associated with rolw id ['.$userRoleId.']');
			return true;
		}
		
		// delete association between kuser and role
		$kuserToRole->delete();
	}
	

	/**
	 * Set the roles of the current kuser
	 * @param string $idsString A comma seperated string of user role IDs
	 */
	public function setUserRoles($idsString)
	{
		$this->deleteAllUserRoles();
		$ids = explode(',', trim($idsString));
		
		foreach ($ids as $id)
		{
			if (!is_null($id) && $id != '') {
				$this->addUserRole($id, false);
			}
		}
	}
	
	
	/**
	 * Delete all user roles from the current kuser
	 */
	private function deleteAllUserRoles()
	{
		$c = new Criteria();
		$c->add(KuserToUserRolePeer::KUSER_ID, $this->getId(), Criteria::EQUAL);
		KuserToUserRolePeer::doDelete($c);
	}
	
	
	// --------------------------------------
	// -- end of user role handling functions
	// --------------------------------------
			
}
