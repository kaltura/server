<?php
require_once ( "defPartnerservices2Action.class.php");

class listdownloadsAction extends defPartnerservices2Action
{
	public function describe()
	{
		return 	
			array (
				"display_name" => "listDownloads",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"filter" => array ("type" => "BatchJobFilter", "desc" => "")
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
					"downloads" => array ("type" => "*BatchJob", "desc" => "")
					),
				"errors" => array (
				)
			); 
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		// TODO -  verify permissions for viewing lists 

		$detailed = $this->getP ( "detailed" , false );
		$limit = $this->getP ( "page_size" , 10 );
		$page = $this->getP ( "page" , 1 );		
		//$order_by = int( $this->getP ( "order_by" , -1 ) );
		
		$offset = ($page-1)* $limit;

		$c = new Criteria();
		$download_types = array ( BatchJob::BATCHJOB_TYPE_FLATTEN , BatchJob::BATCHJOB_TYPE_DOWNLOAD );
		
		$c->add ( BatchJobPeer::JOB_TYPE , $download_types , Criteria::IN );
		
		// filter		
		$filter = new BatchJobFilter();
		$fields_set = $filter->fillObjectFromRequest( $this->getInputParams() , "filter_" , null );
		$filter->attachToCriteria( $c );
		
		//if ($order_by != -1) kshowPeer::setOrder( $c , $order_by );
		$count = BatchJobPeer::doCount( $c );

		$offset = ($page-1)* $limit;
		
		$c->setLimit( $limit );
		
		if ( $offset > 0 )
		{
			$c->setOffset( $offset );
		}
				
		$list = BatchJobPeer::doSelect( $c );
		$level = objectWrapperBase::DETAIL_LEVEL_REGULAR ;

		$this->addMsg ( "count" , $count );
		$this->addMsg ( "page_size" , $limit );
		$this->addMsg ( "page" , $page );

		$wrapper =  objectWrapperBase::getWrapperClass( $list  , $level );
		$this->addMsg ( "downloads" , $wrapper ) ;
	}
}
?>