<?php
/**
 * @package api
 * @subpackage ps2
 */
class getkshowAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getKShow",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"kshow_id" => array ("type" => "string", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"kshow" => array ("type" => "kshow", "desc" => "")
					),
				"errors" => array (
					APIErrors::INVALID_KSHOW_ID ,
				)
			); 
	}

	// ask to fetch the kuser from puser_kuser 
	public function needKuserFromPuser ( )	
	{	
		$kshow_id = $this->getPM ( "kshow_id" );
		if ( $kshow_id == kshow::KSHOW_ID_USE_DEFAULT )			return parent::KUSER_DATA_KUSER_ID_ONLY ;
		return self::KUSER_DATA_NO_KUSER;	
	}
		
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$kshow_id = $this->getPM ( "kshow_id" );
		$detailed = $this->getP ( "detailed" , false );
		$kshow_indexedCustomData3 = $this->getP ( "indexedCustomData3" );
		$kshow = null;
        
		if ( $kshow_id == kshow::KSHOW_ID_USE_DEFAULT )
        {
            // see if the partner has some default kshow to add to
            $kshow = myPartnerUtils::getDefaultKshow ( $partner_id, $subp_id , $puser_kuser );
            if ( $kshow ) $kshow_id = $kshow->getId();
        }
		elseif ( $kshow_id )
		{
			$kshow = kshowPeer::retrieveByPK( $kshow_id );
		}
		elseif ( $kshow_indexedCustomData3 )
		{
			$kshow = kshowPeer::retrieveByIndexedCustomData3( $kshow_indexedCustomData3 );
		}

		if ( ! $kshow )
		{
			$this->addError ( APIErrors::INVALID_KSHOW_ID , $kshow_id );
		}
		else
		{
			$level = ( $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED : objectWrapperBase::DETAIL_LEVEL_REGULAR );
			$wrapper = objectWrapperBase::getWrapperClass( $kshow , $level );
			// TODO - remove this code when cache works properly when saving objects (in their save method)
			$wrapper->removeFromCache( "kshow" , $kshow_id );
			$this->addMsg ( "kshow" , $wrapper ) ;
		}
	}
}
?>