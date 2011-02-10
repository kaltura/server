<?php
/**
 * @package api
 * @subpackage ps2
 */
class updateuserAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "updateUser",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"user_id" => array ("type" => "integer", "desc" => ""),
						"user" => array ("type" => "kuser", "desc" => "")
						),
					"optional" => array (
						)
					),
				"out" => array (
					"user" => array ("type" => "PuserKuser", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_USER_ID , 
					APIErrors::USER_ALREADY_EXISTS_BY_SCREEN_NAME ,
				)
			);
	}

	protected function ticketType()	{		return self::REQUIED_TICKET_ADMIN;	}

	// ask to fetch the kuser from puser_kuser
	public function needKuserFromPuser ( ) 	{ 		return self::KUSER_DATA_KUSER_DATA; 	}

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{

		$user_id = $this->getPM ( "user_id" );
		$target_puser_kuser = PuserKuserPeer::retrieveByPartnerAndUid($partner_id , null , $user_id , true );

		if ( ! $target_puser_kuser )
		{
			$this->addError ( APIErrors::INVALID_USER_ID , $user_id );
		}

		$kuser = $target_puser_kuser->getKuser();

		// get the new properties for the kuser from the request
		$kuser_update_data = new kuser();

		$obj_wrapper = objectWrapperBase::getWrapperClass( $kuser , 0 );

		$fields_modified = baseObjectUtils::fillObjectFromMap ( $this->getInputParams() , $kuser_update_data , "user_" , $obj_wrapper->getUpdateableFields() );
		if ( count ( $fields_modified ) > 0 )
		{
			if (!$partner_id) // is this a partner policy we should enforce?
			{
				$kuser_from_db = kuserPeer::getKuserByScreenName ( $kuser->getScreenName() );
				// check if there is a kuser with such a name in the system (and this kuser is not the current one)
				if ( $kuser_from_db && $kuser_from_db->getId() == $kuser->getId() )
				{
					$this->addError( APIErrors::USER_ALREADY_EXISTS_BY_SCREEN_NAME , $kuser->getScreenName() );
					return;
				}
			}

			if ( $kuser_update_data )
			{
				baseObjectUtils::fillObjectFromObject( $obj_wrapper->getUpdateableFields() , $kuser_update_data , $kuser , baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME );
				$target_puser_kuser->setKuser ( $kuser );
			}

			$kuser->save();
		}

		$wrapper = objectWrapperBase::getWrapperClass( $target_puser_kuser , objectWrapperBase::DETAIL_LEVEL_DETAILED);
		$wrapper->removeFromCache( "kuser" , $kuser->getId() );
		$this->addMsg ( "user" , $wrapper );
		$this->addDebug ( "modified_fields" , $fields_modified );

	}
}
?>