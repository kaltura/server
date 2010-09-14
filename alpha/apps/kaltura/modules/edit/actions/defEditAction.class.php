<?php
require_once ( "kalturaAction.class.php");
/**
 * edit actions.
 *
 * @package    kaltura
 * @subpackage edit
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class defEditAction extends kalturaAction
{
	public function execute()
	{
		//$this->kshow_id = $_REQUEST["kshow_id"];
		$this->kshow_id = $this->getRequestParameter('kshow_id' , 0 );
		$this->entry_id = $this->getRequestParameter('entry_id' , 0 );

/* TODO - PRIVILEGES */
		$partner_id = $this->getP ( "partner_id" );
		if ( $partner_id )
		{
			// now check for the ks:
			$ks_str = $this->getP ( "ks" );
			if ( !$ks_str )
			{
				// TODO - show js error and return to back_url
				die;
			}

			$ks = null;
			$puser_id = $this->getP ( "uid" );
			$res = kSessionUtils::validateKSession( $partner_id , $puser_id , $ks_str , $ks );
			if ( 0 >= $res )
			{
				// TODO - show js error and return to back_url
				die;
			}
		}
		else
		{
			// authenticate and open in new window if authentication problem
			$this->forceEditPermissions ( NULL , $this->kshow_id , true , true );
		}

		if (!$this->entry_id)
		{
			$kshow = kshowPeer::retrieveByPK( $this->kshow_id );
			$this->entry_id = $kshow->getShowEntryId();
		}

		$referer = @$_SERVER["HTTP_REFERER"];
		if (strstr($referer, 'kaltura.com'))
			$this->backUrl = "";
		else
			$this->backUrl = "http://www.kaltura.com/index.php";

		// partner_id
		// subp_id
		// partner text1, text2, text3 ??
		// logo_url (with a very specific size 75x35)
		// text for the back button
		// text for the publish button
		// back_url
		$var_names = array( "kshow_id" , "entry_id" , "partner_id" , "subp_id" , "ks" , "uid" , "logo_url" , "btn_txt_back" , "btn_txt_publish" , "partner_name" , "first_visit" , "partner_data"/* "back_url"*/ );
		$vars = $this->fromRequest ( $var_names );
		$host = requestUtils::getRequestHostId();
		$vars["host"] =  $host;
		$this->vars = $vars;
		
		$this->host = $host;

		$this->navigate_top = $this->getRequestParameter( "navigate_top" , true );
		$this->back_url = $this->getRequestParameter( "back_url" , null );

	}

	private function fromRequest ( $names  )
	{
		$arr = array();
		foreach ( $names as $name )
		{
			$val = $this->getRequestParameter( $name , null );
			if ( $val !== null ) $arr[$name] = $val;
		}

		return $arr;
	}
}
?>