<?php

require_once ( "kalturaAction.class.php");
/**
 * emailImport actions.
 *
 * @package    kaltura
 * @subpackage emailImport
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class defKeditorservicesAction extends kalturaAction
{
	//	protected $kshow_id;
	//	protected $kshow;
	// the objects bellow are actually the user's session 
	protected $partner_id;
	protected $subp_id;
	protected $ks_str;
	protected $uid; 
	
	protected function fetchKshow()
	{
		return true;
	}
	/**
	 * This function will be implemented in eash of the derived convrete classes which represent a service
	 * for Keditor.
	 * To simplifu work - it will be passed the $this->kshow which will never be null.
	 */
/*
	abstract protected function executeImpl( $kshow ); 

	abstract protected function noSuchKshow ( $kshow_id );
	*/
		
	public function execute()
	{
		//$this->debug = @$_REQUEST["debug"];
		$this->debug = false;

		$entry_id = @$_REQUEST["entry_id"];
		if ( $entry_id == NULL || !$entry_id || $entry_id < 0 )
		{
			$kshow_id = @$_REQUEST["kshow_id"];
			if ($kshow_id)
			{
				$kshow = kshowPeer::retrieveByPK( $kshow_id );
				if ( ! $kshow ) return; // request for non-existing kshow_id
				$entry_id = $kshow->getShowEntryId();
			}
		}
		
		if ( $entry_id == NULL || !$entry_id || $entry_id < 0 )
			return;
		
		$this->partner_id = $this->getRequestParameter( "partner_id" ); 
		$this->subp_id = $this->getRequestParameter( "subp_id" );
		$this->ks_str = $this->getRequestParameter( "ks" );
		$this->uid = $this->getRequestParameter( "uid" );
		
		$this->entry_id = $entry_id;
		$entry = entryPeer::retrieveByPK($entry_id);
		
		if ( $entry == NULL )
		{
			$this->noSuchEntry( $entry_id );
			return;
		}
		
		if ( $this->fetchKshow() )
		{
			$kshow_id = $entry->getKshowId();
			
			//$kshow_id = @$_REQUEST["kshow_id"];
			$this->kshow_id = $kshow_id;
	
			if ( $kshow_id == NULL || !$kshow_id ) return;
	
			$kshow = kshowPeer::retrieveByPK( $kshow_id );
	// TODO - PRIVILEGES
	/*		$user_ok = $this->forceEditPermissions( $kshow , $kshow_id , false);
			
			if ( ! $user_ok )
			{
				return $this->securityViolation( $kshow_id ); 
			}
	*/
			if ( $kshow == NULL )
			{
				$this->noSuchKshow ( $kshow_id );
				return;
			}
		}
		else
		{
			
			$kshow = new kshow();
			$kshow_id = $entry->getKshowId();
			$this->kshow_id = $kshow_id;
		}
		
		// TODO
		// validate editor has proper privileges !
		//$this->forceAuthentication();

		$this->entry = $entry;
		$this->kshow = $kshow;
		$duration = 0;
		
//		$this->logMessage ( __CLASS__ . " 888 $kshow_id"  , "err");
		
		$result = $this->executeImpl( $this->kshow, $this->entry );
		
		if ( $result != NULL )
		{
			$this->getResponse()->setHttpHeader ( "Content-Type" , $result );
		}
		else
		{
			$this->getResponse()->setHttpHeader ( "Content-Type" , "text/xml; charset=utf-8" );
		}
		
		$this->getController()->setRenderMode ( sfView::RENDER_CLIENT );
	}
	
	protected function executeImpl( kshow $kshow, entry &$entry)
	{
		return "text/html; charset=utf-8";
	}

	protected function noSuchEntry ( $entry_id )
	{
		$this->xml_content = "No such entry [$entry_id]";
	}
	
	protected function noSuchKshow ( $kshow_id )
	{
		$this->xml_content = "No such show [$kshow_id]";
	}
	
	
	protected function  securityViolation( $kshow_id )
	{
		$xml = "<xml><kshow id=\"$kshow_id\" securityViolation=\"true\"/></xml>";
		$this->getResponse()->setHttpHeader ( "Content-Type" , "text/xml; charset=utf-8" );
		$this->getController()->setRenderMode ( sfView::RENDER_NONE );
		return $this->renderText( $xml );
	}
	
	
	/**
	 * Supports backward compatibility
	 * returns all kusers of the puser
	 */
	protected function getLoggedInUserIds ( )
	{
		$ret = array($this->getLoggedInPuserId());
		
		$c = new Criteria();
		$c->add(kuserPeer::PUSER_ID, $this->uid);
		$kusers = kuserPeer::doSelect($c);
		
		foreach($kusers as $kuser)
			$ret[] = $kuser->getId();
			
		return $ret;
	}
	
	protected function getLoggedInUserId ( )
	{
		if ( $this->partner_id )
		{
			// this part overhere should be in a more generic place - part of the services
			$ks = "";
			// TODO - for now ignore the session
			$valid = true; // ( 0 >= kSessionUtils::validateKSession ( $this->partner_id , $this->uid , $this->ks_str ,&$ks ) );
			if ( $valid )
			{
				$puser_id = $this->uid;
				// actually the better user indicator will be placed in the ks - TODO - use it !! 
				// $puser_id = $ks->user; 
				
				$kuser_name = $puser_name = $this->getP ( "user_name" );
				if ( ! $puser_name )
				{
					$kuser_name = myPartnerUtils::getPrefix( $this->partner_id ) . $puser_id;
				}
				// will return the existing one if any, will create is none
				$puser_kuser = PuserKuserPeer::createPuserKuser ( $this->partner_id , $this->subp_id, $puser_id , $kuser_name , $puser_name, false  );
				$likuser_id = $puser_kuser->getKuserId(); // from now on  - this will be considered the logged in user
				return $likuser_id;
			}

		}
		else
		{	
			return parent::getLoggedInUserId();
		}
	}
	
	protected function 	allowMultipleRoughcuts ( )
	{	
		$this->logMessage( "allowMultipleRoughcuts: [" . $this->partner_id . "]");
		if ( $this->partner_id == null ) return true;
		else
		{
			// this part overhere should be in a more generic place - part of the services
			$multiple_roghcuts = Partner::allowMultipleRoughcuts( $this->partner_id );
			return $multiple_roghcuts;
		}
	}		
}


?>