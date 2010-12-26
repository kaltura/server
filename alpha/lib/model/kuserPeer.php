<?php
/**
 * Subclass for performing query and update operations on the 'kuser' table.
 *
 * 
 *
 * @package lib.model
 */ 
class kuserPeer extends BasekuserPeer 
{	
	const  KALTURA_NEW_USER_EMAIL = 120;
	
	private static $s_default_count_limit = 301;

	public static function setDefaultCriteriaFilter ()
	{
		if ( self::$s_criteria_filter == null )
		{
			self::$s_criteria_filter = new criteriaFilter ();
		}
		
		$c = new myCriteria(); 
		$c->addAnd ( kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
		self::$s_criteria_filter->setFilter ( $c );
	}
	
	public static function getKuserByScreenName( $screen_name  )
	{
		$c = new Criteria();
		$c->add ( kuserPeer::SCREEN_NAME , $screen_name );
		return self::doSelectOne( $c ); 
	}
	
	/**
	 * @param int $partner_id
	 * @param string $puser_id
	 * @param bool $ignore_puser_kuser
	 * @return kuser
	 */
	public static function getKuserByPartnerAndUid($partner_id , $puser_id, $ignore_puser_kuser = false)
	{
		if (defined("KALTURA_API_V3") || $ignore_puser_kuser)
		{
			$c = new Criteria();
			$c->add(self::PARTNER_ID, $partner_id);
			$c->add(self::PUSER_ID, $puser_id);
			return self::doSelectOne($c);			
		}
		
		// just grab the kuser, we dont mind which subp we get
		$puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid( $partner_id , 0, $puser_id , true );
		if ( !$puser_kuser ) return false;
		return $puser_kuser->getKuser();
	}
	
	public static function getActiveKuserByPartnerAndUid($partner_id , $puser_id)
	{
		$c = new Criteria();
		$c->add(self::STATUS, KuserStatus::ACTIVE);
		$c->add(self::PARTNER_ID, $partner_id);
		$c->add(self::PUSER_ID, $puser_id);
		return self::doSelectOne($c);			
	}
	
	public static function createKuserForPartner($partner_id, $puser_id, $is_admin = false)
	{
		$kuser = self::getKuserByPartnerAndUid($partner_id, $puser_id);
		
		if (!$kuser)
		{
			$kuser = new kuser();
			$kuser->setPuserId($puser_id);
			$kuser->setScreenName($puser_id);
			$kuser->setFirstName($puser_id);
			$kuser->setPartnerId($partner_id);
			$kuser->setStatus(KuserStatus::ACTIVE);
			$kuser->setIsAdmin($is_admin);
			$kuser->save();
		}
		
		return $kuser;
	}
	
	/**
	 * This function returns a pager object holding the given user's favorite users
	 *
	 * @param int $kuserId = the requested user
	 * @param int $privacy = the privacy filter
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @return the pager object
	 */
	public static function getUserFavorites($kuserId, $privacy, $pageSize, $page)
	{
		$c = new Criteria();
		$c->addJoin(kuserPeer::ID, favoritePeer::SUBJECT_ID, Criteria::INNER_JOIN);
		$c->add(favoritePeer::KUSER_ID, $kuserId);
		$c->add(favoritePeer::SUBJECT_TYPE, favorite::SUBJECT_TYPE_USER);
		$c->add(favoritePeer::PRIVACY, $privacy);
		$c->setDistinct();
		
		// our assumption is that a request for private favorites should include public ones too 
		if( $privacy == favorite::PRIVACY_TYPE_USER ) 
		{
			$c->addOr( favoritePeer::PRIVACY, favorite::PRIVACY_TYPE_WORLD );
		}
			
		$c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME);
		
	    $pager = new sfPropelPager('kuser', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->init();
			    
	    return $pager;
	}

	/**
	 * This function returns a pager object holding the given user's favorite entries
	 * each entry holds the kuser object of its host.
	 *
	 * @param int $kuserId = the requested user
	 * @param int $privacy = the privacy filter
	 * @param int $pageSize = number of kshows in each page
	 * @param int $page = the requested page
	 * @return the pager object
	 */
	public static function getUserFans($kuserId, $privacy, $pageSize, $page)
	{
		$c = new Criteria();
		$c->addJoin(kuserPeer::ID, favoritePeer::KUSER_ID, Criteria::INNER_JOIN);
		$c->add(favoritePeer::SUBJECT_ID, $kuserId);
		$c->add(favoritePeer::SUBJECT_TYPE, favorite::SUBJECT_TYPE_USER);
		$c->add(favoritePeer::PRIVACY, $privacy);
		
		$c->setDistinct();
		
		// our assumption is that a request for private favorites should include public ones too 
		if( $privacy == favorite::PRIVACY_TYPE_USER ) 
		{
			$c->addOr( favoritePeer::PRIVACY, favorite::PRIVACY_TYPE_WORLD );
		}
			
		$c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME);
		
	    $pager = new sfPropelPager('kuser', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->init();
			    
	    return $pager;
	}
	
	/**
	 * This function returns a pager object holding the specified list of user favorites, 
	 * sorted by a given sort order.
	 * the $mine_flag param decides if to return favorite people or fans
	 */
	public static function getUserFavoritesOrderedPager( $order, $pageSize, $page, $kuserId, $mine_flag )
	{
		$c = new Criteria();
		
		if ( $mine_flag ) 
		{
			$c->addJoin(kuserPeer::ID, favoritePeer::SUBJECT_ID, Criteria::INNER_JOIN);
			$c->add(favoritePeer::KUSER_ID, $kuserId); 
		}
		else 
		{
			$c->addJoin(kuserPeer::ID, favoritePeer::KUSER_ID, Criteria::INNER_JOIN);
			$c->add(favoritePeer::SUBJECT_ID, $kuserId); 
		}
			
		$c->add(favoritePeer::SUBJECT_TYPE, favorite::SUBJECT_TYPE_USER);
		
		// TODO: take privacy into account
		$privacy = favorite::PRIVACY_TYPE_USER;
		$c->add(favoritePeer::PRIVACY, $privacy);
		// our assumption is that a request for private favorites should include public ones too 
		if( $privacy == favorite::PRIVACY_TYPE_USER ) 
		{
			$c->addOr( favoritePeer::PRIVACY, favorite::PRIVACY_TYPE_WORLD );
		}
			
		switch( $order )
		{
			
			case kuser::KUSER_SORT_MOST_VIEWED: $c->addDescendingOrderByColumn(kuserPeer::VIEWS);  break;
			case kuser::KUSER_SORT_MOST_RECENT: $c->addAscendingOrderByColumn(kuserPeer::CREATED_AT);  break;
			case kuser::KUSER_SORT_NAME: $c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME); break;
			case kuser::KUSER_SORT_AGE: $c->addAscendingOrderByColumn(kuserPeer::DATE_OF_BIRTH); break;
			case kuser::KUSER_SORT_COUNTRY: $c->addAscendingOrderByColumn(kuserPeer::COUNTRY); break;
			case kuser::KUSER_SORT_CITY: $c->addAscendingOrderByColumn(kuserPeer::CITY); break;
			case kuser::KUSER_SORT_GENDER: $c->addAscendingOrderByColumn(kuserPeer::GENDER); break;		
			case kuser::KUSER_SORT_PRODUCED_KSHOWS: $c->addDescendingOrderByColumn(kuserPeer::PRODUCED_KSHOWS); break;
			
			default: $c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME);
		}
		
		$c->setDistinct();
		
		
	    $pager = new sfPropelPager('kuser', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->init();
			    
	    return $pager;
	}
	
	
	/**
	 * This function returns a pager object holding all the users
	 */
	public static function getAllUsersOrderedPager( $order, $pageSize, $page )
	{
		$c = new Criteria();
		
		switch( $order )
		{
			
			case kuser::KUSER_SORT_MOST_VIEWED: $c->addDescendingOrderByColumn(kuserPeer::VIEWS);  break;
			case kuser::KUSER_SORT_MOST_RECENT: $c->addAscendingOrderByColumn(kuserPeer::CREATED_AT);  break;
			case kuser::KUSER_SORT_NAME: $c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME); break;
			case kuser::KUSER_SORT_AGE: $c->addAscendingOrderByColumn(kuserPeer::DATE_OF_BIRTH); break;
			case kuser::KUSER_SORT_COUNTRY: $c->addAscendingOrderByColumn(kuserPeer::COUNTRY); break;
			case kuser::KUSER_SORT_CITY: $c->addAscendingOrderByColumn(kuserPeer::CITY); break;
			case kuser::KUSER_SORT_GENDER: $c->addAscendingOrderByColumn(kuserPeer::GENDER); break;		
			case kuser::KUSER_SORT_MOST_ENTRIES: $c->addDescendingOrderByColumn(kuserPeer::ENTRIES); break;		
			case kuser::KUSER_SORT_MOST_FANS: $c->addDescendingOrderByColumn(kuserPeer::FANS); break;		
			
			default: $c->addAscendingOrderByColumn(kuserPeer::SCREEN_NAME);
		}
		
		$pager = new sfPropelPager('kuser', $pageSize);
	    $pager->setCriteria($c);
	    $pager->setPage($page);
	    $pager->init();
			    
	    return $pager;
	}
	

	public static function selectIdsForCriteria ( Criteria $c )
	{
		$c->addSelectColumn(self::ID);
		$rs = self::doSelectStmt($c);
		$id_list = Array();
		
		while($rs->next())
		{
			$id_list[] = $rs->getInt(1);
		}
		
		$rs->close();
		
		return $id_list;
	}

	public static function doCountWithLimit (Criteria $criteria, $distinct = false, $con = null)
	{
		$criteria = clone $criteria;
		$criteria->clearSelectColumns()->clearOrderByColumns();
		if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
			$criteria->addSelectColumn("DISTINCT ".self::ID);
		} else {
			$criteria->addSelectColumn(self::ID);
		}

		$criteria->setLimit( self::$s_default_count_limit );
		
		$rs = self::doSelectStmt($criteria, $con);
		$count = 0;
		while($rs->next())
			$count++;
	
		return $count;
	}
	
	
	public static function doStubCount (Criteria $criteria, $distinct = false, $con = null)
	{
		return 0;
	}	
	
	/**
	 * @param string $email
	 * @return kuser
	 */
	public static function getKuserByEmail($email, $partnerId = null)
	{
		$c = new Criteria();
		$c->add (kuserPeer::EMAIL, $email);
		
		if(!is_null($partnerId))
			$c->add (kuserPeer::PARTNER_ID, $partnerId);
			
		$kuser = kuserPeer::doSelectOne( $c );
		
		return $kuser;
		
	}

	/**
	 * @param string $email
	 * @param string $password
	 * @param int $partnerId
	 * @return kuser
	 */
	public static function userLogin($puserId, $password, $partnerId)
	{
		$kuser = self::getKuserByPartnerAndUid($partnerId , $puserId);
		if (!$kuser) {
			throw new kUserException('', kUserException::USER_NOT_FOUND);
		}

		if (!$kuser->getLoginDataId()) {
			throw new kUserException('', kUserException::LOGIN_DATA_NOT_FOUND);
		}
		
		$kuser = UserLoginDataPeer::userLoginByDataId($kuser->getLoginDataId(), $password, $partnerId);
					
		return $kuser;
	}
	
	
	public static function getByLoginDataAndPartner($loginDataId, $partnerId)
	{
		$c = new Criteria();
		$c->addAnd(kuserPeer::LOGIN_DATA_ID, $loginDataId);
		$c->addAnd(kuserPeer::PARTNER_ID, $partnerId);
		$c->addAnd(kuserPeer::STATUS, KuserStatus::DELETED, Criteria::NOT_EQUAL);
		$kuser = self::doSelectOne($c);
		if (!$kuser) {
			return false;
		}
		return $kuser;
	}
	
	
	/**
	 * Adds a new kuser and user_login_data records as needed
	 * @param kuser $user
	 * @param string $password
	 * @param bool $checkPasswordStructure
	 * @throws kUserException::USER_NOT_FOUND
	 * @throws kUserException::USER_ALREADY_EXISTS
	 * @throws kUserException::INVALID_EMAIL
	 * @throws kUserException::INVALID_PARTNER
	 * @throws kUserException::ADMIN_LOGIN_USERS_QUOTA_EXCEEDED
	 * @throws kUserException::USER_EXISTS_WITH_DIFFERENT_PASSWORD
	 * @throws kUserException::LOGIN_ID_ALREADY_USED
	 * @throws kUserException::PASSWORD_STRUCTURE_INVALID
	 * @throws kPermissionException::ROLE_ID_MISSING
	 * @throws kPermissionException::ONLY_ONE_ROLE_PER_USER_ALLOWED
	 */
	public static function addUser(kuser $user, $password = null, $checkPasswordStructure = true)
	{
		if (!$user->getPuserId()) {
			throw new kUserException('', kUserException::USER_ID_MISSING);
		}
		
		// check if user with the same partner and puserId already exists		
		$existingUser = kuserPeer::getKuserByPartnerAndUid($user->getPartnerId(), $user->getPuserId());
		if ($existingUser) {
			throw new kUserException('', kUserException::USER_ALREADY_EXISTS);
		}
		
		// check if roles are valid - may throw exceptions
		if (!$user->getUserRoleIds()) {
			// assign default role according to user type admin / normal
			$userRole = UserRolePeer::getDefaultRoleForUser($user);
			$user->setUserRoles($userRole->getId());
		}
		UserRolePeer::testValidRolesForUser($user->getUserRoleIds());
		
		if($user->getScreenName() === null) {
			$user->setScreenName($user->getPuserId());
		}
			
		if($user->getFullName() === null) {
			$user->setFirstName($user->getPuserId());
		}
		
		if (is_null($user->getStatus())) {
			$user->setStatus(KuserStatus::ACTIVE);
		}
		
		// if password is set, user should be able to login to the system - add a user_login_data record
		if ($password || $user->getIsAdmin()) {
			// throws an action on error
			$user->enableLogin($user->getEmail(), $password ? $password : UserLoginDataPeer::generateNewPassword());
			if (!$password) {
				self::sendNewUserMail($user);
			}
		}	
		
		$user->save();
		return $user;
	}
	
	
	
	private static function sendNewUserMail(kuser $user)
	{
		$mailType = null;
		$bodyParams = array();

		$mailType = self::KALTURA_NEW_USER_EMAIL;
				
		$userName = $user->getFullName();
		$loginEmail = $user->getEmail();
		$partnerId = $user->getPartnerId();
		$roleName = $user->getUserRoleNames();
		$resetPasswordLink = UserLoginDataPeer::getPassResetLink($user->getLoginData()->getPasswordHashKey());
		$kmcLink = trim(kConf::get('apphome_url'), '/').'/kmc';
		$contactLink = kConf::get('contact_url');
		$beginnersGuideLink = kConf::get('beginners_tutorial_url');
		$quickStartGuideLink = kConf::get('quick_start_guide_url');
		$forumsLink = kConf::get('forum_url');
		$unsubscribeLink = kConf::get('unsubscribe_mail_url').$loginEmail;
		
		
		$bodyParams = array($userName, $loginEmail, $partnerId, $roleName, $resetPasswordLink, $kmcLink, $contactLink, $beginnersGuideLink, $quickStartGuideLink, $forumsLink, $unsubscribeLink);
	
		kJobsManager::addMailJob(
			null, 
			0, 
			$partnerId, 
			$mailType, 
			kMailJobData::MAIL_PRIORITY_NORMAL, 
			kConf::get ("partner_registration_confirmation_email" ), 
			kConf::get ("partner_registration_confirmation_name" ), 
			$loginEmail, 
			$bodyParams);
	}
			
}
