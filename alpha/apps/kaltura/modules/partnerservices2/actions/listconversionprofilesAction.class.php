<?php
/**
 * @package api
 * @subpackage ps2
 */
class listconversionprofilesAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 	
			array (
				"display_name" => "listConversionProfile",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"filter" => array ("type" => "ConversionProfileFilter", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => ""),
						"page_size" => array ("type" => "integer", "default" => 10, "desc" => ""),
						"page" => array ("type" => "boolean", "default" => 1, "desc" => ""),
						)
					),
				"out" => array (
					"count" => array ("type" => "integer", "desc" => ""),
					"page_size" => array ("type" => "integer", "desc" => ""),
					"page" => array ("type" => "integer", "desc" => ""),
					"conversionProfile" => array ("type" => "*ConversionProfile", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	// TODO - this is very wrong for this service!
	// because it is used only for the KMC - it is tailed and does not act like a regular "list" service
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
//		$this->applyPartnerFilterForClass( new ConversionProfilePeer() , $partner_id );
		// TODO -  verify permissions for viewing lists 

		$detailed = $this->getP ( "detailed" , false );
		$limit = $this->getP ( "page_size" , 10 );
		$page = $this->getP ( "page" , 1 );		
		//$order_by = int( $this->getP ( "order_by" , -1 ) );
		
		$offset = ($page-1)* $limit;

		$partner_current_conversion_profile = myPartnerUtils::getCurrentConversionProfile( $partner_id );
		
		$c = new Criteria();
				
		// filter		
		$filter = new ConversionProfileFilter(  );
		$fields_set = $filter->fillObjectFromRequest( $this->getInputParams() , "filter_" , null );
		$filter->set ( "_order_by" , null );  // ignore the order_by for now
		$filter->attachToCriteria( $c );
		
		// partner_id either the partner or partner_id=0
		$crit = $c->getNewCriterion( ConversionProfilePeer::PARTNER_ID , 0 );
		$c->addAnd ( $crit->addOr ( $c->getNewCriterion( ConversionProfilePeer::PARTNER_ID , $partner_id ) ) );
		
		// for now - only enabled profiles
		$c->addAnd ( ConversionProfilePeer::ENABLED , 1 );
		
		// make sure the partner's profiles will appear first ordered by id desc - last will come first
		$order_by = "(" . ConversionProfilePeer::PARTNER_ID . "<>{$partner_id})";  // first take the patner_id and then the rest
		myCriteria::addComment( $c, "Only Kaltura Network" );
		$c->addAscendingOrderByColumn ( $order_by );//, Criteria::CUSTOM );
		$c->addDescendingOrderByColumn ( ConversionProfilePeer::UPDATED_AT );//, Criteria::CUSTOM );
		$c->addDescendingOrderByColumn ( ConversionProfilePeer::ID );//, Criteria::CUSTOM );
		
		//if ($order_by != -1) kshowPeer::setOrder( $c , $order_by );
		$count = ConversionProfilePeer::doCount( $c );

		$offset = ($page-1)* $limit;
		
		$c->setLimit( $limit );
		
		if ( $offset > 0 )
		{
			$c->setOffset( $offset );
		}
				
		$list = ConversionProfilePeer::doSelect( $c );
		
		if ( count ( $list ) > 0 )
		{
			// reorder the list so the first will always be the best default  for the partner
			$partner_list = array( $partner_current_conversion_profile ); // the first is always the partner's default
			
//			$this->addDebug( "partner_current_conversion_profile" , objectWrapperBase::getWrapperClass( $partner_current_conversion_profile ) );
			$global_list = array();
			$default_prof = array();
			foreach ( $list as $conv_profile )
			{
				if ( $conv_profile->getPartnerId() == ConversionProfile::GLOBAL_PARTNER_PROFILE )
				{
					if ( $conv_profile->getProfileType() == ConversionProfile::DEFAULT_COVERSION_PROFILE_TYPE )
					{
						$default_prof[] = $conv_profile;
					}
					else
						$global_list[] = $conv_profile;
						
				}
				elseif ( $conv_profile->getPartnerId() == $partner_id )
				{
					$partner_list[] = $conv_profile;
				}
			}
			
//			$level = objectWrapperBase::DETAIL_LEVEL_REGULAR ;
//			$this->addMsg ( "par-list" , objectWrapperBase::getWrapperClass( $partner_list  , $level ) ) ;
//			$this->addMsg ( "def-list" , objectWrapperBase::getWrapperClass( $default_prof  , $level ) ) ;
//			$this->addMsg ( "rest-list" , objectWrapperBase::getWrapperClass( $global_list  , $level ) ) ;
			
			// this is the correct order - the partners , the default and all the rest of the globals
			$list = array_merge ( $partner_list , $default_prof , $global_list );
		}
		
		$level = objectWrapperBase::DETAIL_LEVEL_REGULAR ;

		$this->addMsg ( "count" , $count );
		$this->addMsg ( "page_size" , $limit );
		$this->addMsg ( "page" , $page );

		$wrapper =  objectWrapperBase::getWrapperClass( $list  , $level );
		$this->addMsg ( "conversionProfiles" , $wrapper ) ;
	}
}
?>