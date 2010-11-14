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
	const KUSER_ALLOW_ALL = 1000;
	  
	// enum for different status
	const KUSER_STATUS_SUSPENDED = 0;
	const KUSER_STATUS_ACTIVE = 1;
	const KUSER_STATUS_DELETED = 2;	
	
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
		
		return parent::save( $con );	
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/Basekuser#preUpdate()
	 */
	public function preUpdate(PropelPDO $con = null)
	{
		if($this->isColumnModified(kuserPeer::STATUS) && $this->getStatus() == self::KUSER_STATUS_DELETED)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return parent::preUpdate($con);
	}
	
	public static function isAdmin ( $kuser_id )
	{
		return ( $kuser_id > 0 && $kuser_id < self::KUSER_ALLOW_ALL ); 	
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
	
	public function setPassword($password) 
	{ 
		$salt = md5(rand(100000, 999999).$this->getScreenname().$this->getEmail()); 
		$this->setSalt($salt); 
		$this->setSha1Password(sha1($salt.$password));  
	} 
	
	public function isPasswordValid ( $password_to_match )
	{
		return sha1( $this->getSalt().$password_to_match ) == $this->getSha1Password() ;
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
	
	public function getPicId() 
	{ 
		return sha1( $this->getSalt().$this->getId() );
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
		$this->setStatus ( self::KUSER_STATUS_SUSPENDED );
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
//		if ( $this->getStatus() == self::KUSER_STATUS_SUSPENDED )
//			return moderation::MODERATION_STATUS_BLOCK;
		return moderation::MODERATION_STATUS_APPROVED;
	}

	
	public function getPuserId()
	{
		if (defined("KALTURA_API_V3"))
			return parent::getPuserId();
			
		//return " {$this->getPartnerId()} , {$this->getId()}";
		return PuserKuserPeer::getPuserIdFromKuserId ( $this->getPartnerId(), $this->getId() );	
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
	
}
