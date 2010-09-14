<?php
require_once ( "defPartnerservices2Action.class.php");

class listuiconfsAction extends defPartnerservices2Action
{
	public function describe()
	{
		return
			array (
				"display_name" => "listUiconf",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array (
						"filter" => array ("type" => "uiConfFilter", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => ""),
						"detailed_fields" => array ("type" => "string", "desc" => "A list of fields (that do not belong to the level of details) to add to the uiconf - separated by ','"),
						"page_size" => array ("type" => "integer", "default" => 10, "desc" => ""),
						"page" => array ("type" => "boolean", "default" => 1, "desc" => ""),
						)
					),
				"out" => array (
					"count" => array ("type" => "integer", "desc" => ""),
					"page_size" => array ("type" => "integer", "desc" => ""),
					"page" => array ("type" => "integer", "desc" => ""),
					"uiconfs" => array ("type" => "*uiConf", "desc" => ""),
					),
				"errors" => array (
				)
			);
	}


	protected function setExtraFilters ( uiconfFilter &$fields_set )	{	}
	
	protected function getObjectPrefix () { return "uiconf"; } 

	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$this->applyPartnerFilterForClass( new uiConfPeer() , $partner_id );
		
		$detailed = $this->getP ( "detailed" , false );
		$detailed_fields = $this->getP ( "detailed_fields" );
		
		$limit = $this->getP ( "page_size" , 10 );
		$limit = $this->maxPageSize ( $limit );

		$page = $this->getP ( "page" , 1 );

		$offset = ($page-1)* $limit;

		$c = new Criteria();

		// filter
		$filter = new uiConfFilter(  );
		$fields_set = $filter->fillObjectFromRequest( $this->getInputParams() , "filter_" , null );

		$this->setExtraFilters ( $filter );

		$filter->attachToCriteria( $c );
		$count = uiConfPeer::doCount( $c );

		if ( $count > 0 )
		{
			$offset = ($page-1)* $limit;
			$c->setLimit( $limit );
	
			if ( $offset > 0 ) $c->setOffset( $offset );
	
			$list = uiConfPeer::doSelect( $c );
			$level = $detailed ? objectWrapperBase::DETAIL_LEVEL_DETAILED  : objectWrapperBase::DETAIL_LEVEL_REGULAR ;
		}
		else
		{
			// no need to query the data - there is none !
			$list = array();
			$level = objectWrapperBase::DETAIL_LEVEL_REGULAR ;
		}
		$this->addMsg ( "count" , $count );
		$this->addMsg ( "page_size" , $limit );
		$this->addMsg ( "page" , $page );
		foreach($list as $listed_uiConf)
		{
			if(!$listed_uiConf->isValid())
			{
				$this->addError(APIErrors::INTERNAL_SERVERL_ERROR, "uiConf object [{$listed_uiConf->getId()}] is not valid" );
				return;
			}
		}
		if ( $detailed_fields )
		{
			$extra_fields = explode ( "," , $detailed_fields );
			$wrapper =  objectWrapperBase::getWrapperClass( $list  , $level , objectWrapperBase::DETAIL_VELOCITY_DEFAULT , 0 , $extra_fields );
		}
		else
		{
			$wrapper =  objectWrapperBase::getWrapperClass( $list  , $level );
		}
		$this->addMsg ( $this->getObjectPrefix() , $wrapper ) ;
	}
}
?>