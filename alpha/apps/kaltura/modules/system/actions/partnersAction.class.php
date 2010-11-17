<?php
require_once ( "kalturaSystemAction.class.php" );
class partnersAction extends kalturaSystemAction
{
	public function execute()
	{
		ini_set( "memory_limit","64M" );
		
		$this->forceSystemAuthentication();
		
		myDbHelper::$use_alternative_con = null;
		
		$partner_id = $this->getP ( "partner_id" );
		$search_text = $this->getP ( "search_text" );
		$command = $this->getP ( "command" );
		
		if ( $command == "removeCache" )
		{
			PartnerPeer::resetPartnerInCache ( $partner_id);
			return $this->renderText( "Removed partner [$partner_id] from cache" );
		}
		elseif ( $command == "save" )
		{
			$partner = new Partner();
			$pw = objectWrapperBase::getWrapperClass( $partner , 0 );
			$extra_fields  = array ( "partnerName" , "description" , "adminName" , "adminEmail" , "useDefaultKshow" , "conversionString" , "flvConversionString" , "allowQuickEdit" , 
				"shouldForceUniqueKshow" , "returnDuplicateKshow" , "notificationsConfig" , "notify" , "allowMultiNotification" , "appearInSearch" ,
				"mergeEntryLists" , "allowLks" , "allowAnonymousRanking", "isFirstLogin", "matchIp", "host", "cdnHost", "rtmpUrl" , "defThumbOffset" ,
				"landingPage" , "userLandingPage", "status" , "serviceConfigId", "partnerPackage", "moderateContent" , "currentConversionProfileType" , "monitorUsage",
				"templatePartnerId", "addEntryMaxFiles" , "defaultConversionProfileId", "partnerGroupType", "partnerParentId", "enableAnalyticsTab",
				"liveStreamEnabled", "storageServePriority", "storageDeleteFromKaltura", "enableSilverLight", "partnerSpecificServices", "partnerSpecificServices", "enabledPlugins",
				"enable508Players", "enableVast", "appStudioExampleEntry", "appStudioExamplePlayList0", "appStudioExamplePlayList1", "delivryBlockCountries",
				"maxLoginAttempts", "loginBlockPeriod", "numPrevPassToKeep", "passReplaceFreq");
			$allowed_params = array_merge ( $pw->getUpdateableFields() , $extra_fields );	

			$fields_modified = baseObjectUtils::fillObjectFromMap ( $_REQUEST , $partner , "partner_" , $allowed_params , BasePeer::TYPE_PHPNAME , true );
			
			if(class_exists('MetadataPlugin'))
				$partner->setPluginEnabled(MetadataPlugin::PLUGIN_NAME, $_REQUEST['partner_enableMetadata']);
			
			if(class_exists('AuditPlugin'))
				$partner->setPluginEnabled(AuditPlugin::PLUGIN_NAME, $_REQUEST['partner_enableAuditTrail']);
			
			if(class_exists('VirusScanPlugin'))
				$partner->setPluginEnabled(VirusScanPlugin::PLUGIN_NAME, $_REQUEST['partner_enableVirusScan']);
			
			if(!isset($_REQUEST['partner_partnerParentId']) || $_REQUEST['partner_partnerParentId'] == '' )
			{
				$partner->setPartnerParentId(null);
			}
			
			$partner_from_db = PartnerPeer::retrieveByPK( $partner_id );
			if ( $partner_from_db )
			{
				baseObjectUtils::fillObjectFromObject( $allowed_params , $partner , $partner_from_db , baseObjectUtils::CLONE_POLICY_PREFER_NEW , null , BasePeer::TYPE_PHPNAME , true );
			}

			if ( $partner_from_db->getServiceConfigId() == "" ) $partner_from_db->setServiceConfigId ( null );
			
			if($partner_from_db->getPartnerParentId() == -1000)
			{
				$partner_from_db->setPartnerParentId(null);
			}
			$partner_from_db->save();
//			PartnerPeer::resetPartnerInCache ( $partner_id);
		}
		
		$c = new Criteria();
		if ( true ) //$partner_id )
		{
			$c->add ( PartnerPeer::ID , $partner_id );
		}
		if ( $search_text )
		{
			$crit = $c->getNewCriterion( PartnerPeer::PARTNER_NAME , "%{$search_text}%" , Criteria::LIKE ) ;
			$crit->addOr ( $c->getNewCriterion( PartnerPeer::DESCRIPTION , "%{$search_text}%" , Criteria::LIKE ) );
			$c->addAnd ($crit );
		}
		
		$c->setLimit ( 1);
		//$this->partner = PartnerPeer::retrieveByPK( $partner_id );
		$this->partner_list = PartnerPeer::doSelect ( $c );
		if ( count($this->partner_list) == 1 )
		{ 
			$this->partner = $this->partner_list[0];
		} 
		else
		{
			$this->partner = null;
		}
		$this->partner_id= $partner_id;
		$this->search_text = $search_text;
	}
}
?>