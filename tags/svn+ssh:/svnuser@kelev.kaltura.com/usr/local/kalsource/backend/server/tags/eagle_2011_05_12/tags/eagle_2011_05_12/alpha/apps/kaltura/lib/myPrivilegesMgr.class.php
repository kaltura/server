<?php
/*
require_once ( "kshowPeer.class.php");
require_once ( "entryPeer.class.php");
*/
// helper class to expose the set of PRIVs to the application
class PRIV
{
	const BROWSE  					= 0 ; 			// browse shows
	const SEARCH					= 1 ;
	const BROWSE_OTHERS_MYKALTURA 	= 2 ;
	const SEND_EMAIL				= 3 ;
	const RANK 						= 4 ;
	const COMMENT					= 5 ;
	const CONTRIBUTE				= 6 ;
	const ENTER_MYKALTURA			= 7 ;
	const PRODUCE_SHOW				= 8 ;
	const POST_TO_PRODUCER 			= 9 ;			// send email to the producer - as someone who contributed...
	const EDIT_SHOW					= 10 ;
	const GRANT_PRIVILEGES			= 11 ;
}

class myPrivilegesMgr
{
	const PERMISSIONS_PUBLIC 	= 1;
	const PERMISSIONS_PRIVATE 	= 2;
	const PERMISSIONS_GROUP 	= 3;
	const PERMISSIONS_FRIENDS	= 4;

	
	// TODO - think of the correct expiry period
	const PRIVILEGE_EXPIRY_IN_SECONDS = 3600 ; // 60 minutes
	const PRIVILEGE_CONTRIBUTOR_EXPIRY_IN_SECONDS = 900 ; // 15 minutes - someone can contribute and his status can change

	private static $s_producer_privileges = NULL;
	private static $s_contributer_privileges = NULL;
	private static $s_viewer_privileges = NULL;
	private static $s_anonymous_privileges = NULL;
	
	static private $s_priv_cache = NULL;

	
	/**
	 * this method calles both getPrivileges & then verifyPrivileges.
	 * It's easier to call, but if called more than once, better call getPrivileges and then as many calls as needed of
	 * verifyPrivileges
	 */
	static public function hasPrivileges ( $is_authenticated  , $kuser_id , $show_id , $requested_privileges )
	{
		$granted_privileges = self::getPrivileges ( $is_authenticated  , $kuser_id , $show_id );
		return self::verifyPrivileges ( $granted_privileges , $requested_privileges );
	}
	
	// Use to retrive the set or privileges a user has per show
	static public function getPrivileges ( $is_authenticated  , $kuser_id , $show_id )
	{
		self::init();
		if ( !$is_authenticated )
		{
			return self::getAnonymousUserPrivileges  ( $kuser_id , $show_id );
		}

		if ( self::isProducer( $kuser_id , $show_id ) )
		{
			return self::getProducerPrivileges ( $kuser_id , $show_id );
		}
		elseif ( self::groupMechanismEnabled () )
		{
			// TODO - implement ...
		}
		elseif ( self::isContributor (  $kuser_id , $show_id ) )
		{

		}
		else
		{
			return self::getViewerPrivileges ( $kuser_id , $show_id );
		}

	}

	/**
	 *  once holding the user's list ($granted_privileges )- check if he has the  $requested_privileges
	 * 	the $requested_privileges can be a single privilege or an array.
	 *  for a single privilege the return value is boolean - either the user has the requested privilges or not.
	 * 	for am array of privileges, the return value can be one of 3:
	 * 		true - all requested privileges exist,
	 * 		false - none of the requested privileges exist
	 * 		associative array of name + boolean for every requested privileg
	 */ 
	static public function verifyPrivileges ( $granted_privileges , $requested_privileges )
	{
		if ( is_array( $requested_privileges ) )
		{
			$one_true = false;
			$one_false = false;
			$result = array ();
			foreach ( $requested_privileges as $req_priv )
			{
				if ( array_key_exists ( $req_priv , $granted_privileges ) )
				{
					$one_true = true;
					$result [ $req_priv ] = true;
				}
				else
				{
					$one_false = true;
					$result [ $req_priv ] = false;
				}
			}
				
			if ( !$one_true )
			{
				// all is false - return false
				return false;
			}

			if ( !$one_false )
			{
				// all is true - return true;
				return true;
			}
				
			return $result;
		}
		else
		{
			return array_key_exists ( $requested_privileges , $granted_privileges );
		}
	}

	// prepare the cache and the lists of privileges per role
	static private function init()
	{
		self::initPrivArrays ();
		if 	( self::$s_priv_cache == NULL )
		{
			self::$s_priv_cache = new myCache ( "myPrivilegesMgr:" );
		}
	}

	// in the future, if the privileges will be dynamic and per show, this will have to read from the DB
	static private function initPrivArrays ()
	{
		self::$s_anonymous_privileges = array ( PRIV::BROWSE , PRIV::SEARCH , PRIV::BROWSE_OTHERS_MYKALTURA , PRIV::SEND_EMAIL );
		self::$s_viewer_privileges = array ( PRIV::RANK , PRIV::COMMENT, PRIV::CONTRIBUTE , PRIV::ENTER_MYKALTURA , PRIV::PRODUCE_SHOW );
		self::addPrivileges ( PRIV::$s_viewer_privileges , PRIV::$s_anonymous_privileges );
		self::$s_contributer_privileges = array ( PRIV::POST_TO_PRODUCER );
		self::addPrivileges ( PRIV::$s_contributer_privileges , PRIV::$s_viewer_privileges  );
		self::$s_producer_privileges = array ( PRIV::EDIT_SHOW , PRIV::GRANT_PRIVILEGES );
		self::addPrivileges ( PRIV::$s_producer_privileges , PRIV::$s_contributer_privileges  );
		
	}
	
	static private function isProducer ( $kuser_id , $show_id )
	{
		$cache_res = self::getFromCache ( "isProducer" , $kuser_id , $show_id ) ;
		if ( $cache_res == NULL )
		{
			//- from show table - count ( id ) where id = show_id && producer_id = kuser_id  > 0   - easy and fast (show_id is PK)
			$c = new Criteria();
			$c->add ( kshowPeer::ID , $show_id );
			$c->add ( kshowPeer::PRODUCER_ID , $kuser_id);
			$count = kshowPeer::doCount($c);

			self::putCache ( "isProducer" , $kuser_id , $show_id , $count > 0 ) ;
			return $count > 0;
		}
		else
		{
			return $cache_res;
		}
	}

	static private function isContributor (  $kuser_id , $show_id )
	{
		$cache_res = self::getFromCache ( "isContributor" , $kuser_id , $show_id ) ;
		if ( $cache_res == NULL )
		{
			//- from entry table - count ( id ) where show_id = show_id && kuser_id = kuser_id > 0 - fast if will add index ( show_id , kuser_id )
			$c = new Criteria();
			$c->add ( entryPeer::KUSER_ID , $kuser_id);
			$c->add ( entryPeer::KSHOW_ID , $show_id);
			$count = kshowPeer::doCount($c);

			self::putCache ( "isContributor" , $kuser_id , $show_id , $count > 0 ) ;
			return $count > 0;
		}
		else
		{
			return $cache_res;
		}
	}

	
	// TODO - have a list of all the possible privileges in the system
	static private function getProducerPrivileges ( $kuser_id , $show_id )
	{
		// verify this is the producer of the kshow
		return self::$s_producer_privileges;
	}

	static private function getContributorPrivileges ( $kuser_id , $show_id )
	{
		return self::$s_contributer_privileges;
	}

	static private function getViewerPrivileges ( $kuser_id , $show_id )
	{
		return self::$s_viewer_privileges;
	}

	static private function getAnonymousUserPrivileges ( $kuser_id , $show_id )
	{
		return self::$s_anonymous_privileges;
	}

	static private function groupMechanismEnabled ()
	{
		return false;
	}


	static private function getFromCache ( $prefix , $kuser_id , $show_id )
	{
		return self::$s_priv_cache.get ( $prefix . self::getCacheKey ( $kuser_id , $show_id ) );
	}

	static private function putInCache ( $prefix , $kuser_id , $show_id , $obj , $expiry = self::PRIVILEGE_EXPIRY_IN_SECONDS)
	{
		return self::$s_priv_cache.put ( $prefix . self::getCacheKey ( $kuser_id , $show_id ) , $obj , false , $expiry );
	}

	static private function getCacheKey ( $kuser_id , $show_id )
	{
		return $show_id .":" . $kuser_id ;
	}
	
	// will add the privelges from the second array to those of the first array so that the first array grows to hold both sets of privilieges
	static private function addPrivileges ( &$arr1 , $arr2 )
	{
		$srr1 = array_merge ( $arr1 , $arr2 );
	}
}
?>