<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class viewPartnersAction extends kalturaSystemAction
{
	const MAX_PAGE_SIZE = 8000;
	
	private static $extra_fields = array ( "videos" , "images" , "audios" , "entries" , "rcs" , 
		"plays" , "views" , "widgets" , "contribs"  , "total_contribs" , "activeSite7" , "activeSite30" , "activeSite180" ,
			 "activePublisher7" , "activePublisher30" , "activePublisher180" ,  "bandwidth" , "bandwidth_gt" );
	
	public function addStat($partner_id, $name, $value)
	{
		if (!isset($this->partners_stat[$partner_id]))
		$this->partners_stat[$partner_id] = array(
		"id" => $partner_id,
		"name" => $partner_id,
		"kusers" => 0,
		"contributors" => 0,
		"kshows" => 0,
		"entries" => 0,
		"ready_entries" => 0
		);
			
 
		$this->partners_stat[$partner_id][$name] = $value;
	}

	private function addExtraStats ( $partner_id , $stats )
	{
		// cannot update partner that does not exist ! this is an error
		if (!isset($this->partners_stat[$partner_id])) return null;
		
		foreach ( self::$extra_fields as $field )
		{
			$this->partners_stat[$partner_id][$field] = @$stats[$field];
		}
	}
	
	public function execute()
	{
		ini_set("memory_limit","128M");
		ini_set("max_execution_time","240");
		$this->forceSystemAuthentication();

		$start = microtime(true);
		
		$file_path = dirname ( __FILE__ ) . "/../data/viewPartnersData.txt" ;
		$partner_groups = new partnerGroups ( $file_path );
		$this->partner_group_list = $partner_groups->partner_group_list;
		$group_rest = new partnerGroup();
		$group_rest->setName( "_rest" );
		$this->partner_group_list[]= $group_rest; 
		
		$partner_filter = $this->getP ( "partner_filter" );
		$filter_type = $this->getP ( "filter_type" , "project" ) ; //"wiki_wordpress" ); // default a relatively short list
	
		$this->type= $this->getP ( "type" );
		$gloabl_dual = false;//true;
		if ( $this->type == 2 )
		{
			$gloabl_dual = false;	
		}
		
		$this->from_date = $this->getP ( "from_date" );
		$this->to_date = $this->getP ( "to_date" );
		$this->days = $this->getP ( "days" );

		$page = $this->getP ( "page" , 1 );
		if ( $page < 1 ) $page=1;
		$this->page = $page;
		
		$this->partners_between = $this->getP ( "partners_between" ) ;
		if ( $this->partners_between == "false" ) $this->partners_between = false;
		// increment the to_date in one 1 (to be inclusive)
/*		
		if ( $this->to_date )
		{
			$timeStamp = strtotime( $this->to_date );
			$timeStamp += 24 * 60 * 60 ; // add one day
			$this->to_date = date("Y-m-d", $timeStamp);
		}
*/				
		if ( $this->days  )
		{
			$timeStamp = strtotime( $this->to_date );
			$timeStamp -= 24 * 60 * 60 * $this->days; // (- $this->days days)
			$this->from_date =  date("Y-m-d", $timeStamp);
		}
		
		$this->new_first = $this->getP ( "new_first" , null );
		if ( $this->new_first == "false" ) $this->new_first = false;

		// don't display these partners
//		$exclude_list = self::getIdList ( "exclude" );
		
		$this->partners_stat = array();

		$c = new Criteria();
		
		$c->setLimit ( self::MAX_PAGE_SIZE );
		$c->setOffset ( ($page-1) * self::MAX_PAGE_SIZE );
		
		if ( $this->new_first  )
			$c->addDescendingOrderByColumn(PartnerPeer::ID);
		else
			$c->addAscendingOrderByColumn(PartnerPeer::ID);
			
//		$c->addAnd ( PartnerPeer::ID , $exclude_list , Criteria::NOT_IN );

		if ( $this->to_date )
		{
			$c->addAnd ( PartnerPeer::CREATED_AT , $this->to_date , Criteria::LESS_EQUAL );
		}

		if ( $this->partners_between )
		{
			$c->addAnd ( PartnerPeer::CREATED_AT , $this->from_date , Criteria::GREATER_EQUAL );
		}
				
		// TODO - change rule mechanism to be more flixible
			$this->createCriteriaByGroup ( $partner_groups , $c , $filter_type , $partner_filter );
		
		$partners = PartnerPeer::doSelect($c);

		$ids = self::getIds ( $partners );

		$updated_at = null;
		
		if ( $ids && count($ids) >= 1 )
		{
			$statsPool = self::getPartnersStats( $ids , $this->from_date , $this->to_date );
			
	//		fdb::populateObjects( $partners , new PartnerStatsPeer() , "id" , "partnerStats" , false ,"partnerId");
			foreach($partners as $partner)
			{
				$partner_id = $partner->getId();
				$this->addStat($partner_id, "name", $partner_id == $partner->getPartnerName() ? $partner->getAdminName() : $partner->getpartnerName());
				$this->addStat($partner_id, "email", $partner->getAdminEmail());
				$this->addStat($partner_id, "description", $partner->getDescription());
				$this->addStat($partner_id, "url1", $partner->getUrl1());
				$this->addStat($partner_id, "created", substr ( $partner->getCreatedAt() , 0 ,10 ));
				$this->addStat($partner_id, "categories", $partner->getContentCategories());
				$stats = @$statsPool[$partner_id];
				if ( $stats )
				{
					$this->addExtraStats ( $partner_id ,$stats  );
	
				}
			}
		}
		
		$this->dual = $gloabl_dual;
		$this->partner_filter = $partner_filter;
		$this->filter_type = $filter_type;
		$this->updated_at = $updated_at;
		
		$end= microtime(true);

		$this->bench = $end - $start;
	}

	private static function getIds ( $list )
	{
		if( ! $list ) return null;
		$ids = array ();
		foreach ( $list  as $elem )
		{
			$ids[] = $elem->getId();
		}
		return $ids;
	}
	

	
	
	// will return a statsPool for all the ids in the time slot
	private static function getPartnersStats ( $ids , $from_date, $to_date )
	{
		$ids_str = implode ( "," , $ids );
		
		$connection = Propel::getConnection();
		$dateFilter = self::createDateCriteria ( $from_date, $to_date );
		
		$results  = self::executeQuery ( $connection , $ids_str , $dateFilter , PartnerActivity::PARTNER_ACTIVITY_MEDIA );
		
		// videos, images, audios, entries, rcs, widgets
		foreach($results as $resultset)
	    {
	    	$partner_id = $resultset->getInt('PARTNER_ID');
	    	
	    	$video = $resultset->getInt('S1');
	    	$audio = $resultset->getInt('S2');
	    	$image = $resultset->getInt('S3');
	    	$rc = $resultset->getInt('S4');
	    	$entries = $video + $audio + $image; // sum up the clips
	    	
	    	$stats[$partner_id] = array(
	    		"videos" => $video ,
	    		"audios" => $audio ,
	    		"images" => $image ,
	    		"rcs" => $rc,
	    		"entries" => $entries ,
	    		"widgets" => $resultset->getInt('S5'), );
	    }	  

	    // plays , views
	    // see the plays & views are the same type but differenr sub types
		$results  = self::executeQuery ( $connection , $ids_str , $dateFilter , PartnerActivity::PARTNER_ACTIVITY_KDP , true );
		
		foreach($results as $resultset)
	    {
	    	$partner_id = $resultset->getInt('PARTNER_ID');
	    	
	    	$sub_act = $resultset->getInt('SUB_ACTIVITY');
	    	$value = $resultset->get ('S0');
	    	
	    	if ( $sub_act == PartnerActivity::PARTNER_SUB_ACTIVITY_KDP_PLAYS ) $stats[$partner_id]["plays"] = $value;
	    	elseif ( $sub_act == PartnerActivity::PARTNER_SUB_ACTIVITY_KDP_VIEWS ) $stats[$partner_id]["views"] = $value;
	    }	  

	    // bandwidth - for date
		$results  = self::executeQuery ( $connection , $ids_str , $dateFilter , PartnerActivity::PARTNER_ACTIVITY_TRAFFIC );
		
		foreach($results as $resultset)
	    {
	    	$partner_id = $resultset->getInt('PARTNER_ID');
	    	
	    	$bandwidth = (int)($resultset->get ('S0') / 1024);
	    	$stats[$partner_id]["bandwidth"] = $bandwidth;
	    }

		// bandwidth - grand total	    
	    $no_date_filter = "(1=1)";
	    if ( $to_date ) 
	    {
	    	// the to_date is exclusive
			$no_date_filter .=  "AND ACTIVITY_DATE<'$to_date'" ; //" AND DATE_FORMAT(ACTIVITY_DATE,'%y-%m-%d')<='$to_date' ";
	    }
	    
		$results  = self::executeQuery ( $connection , $ids_str , $no_date_filter , PartnerActivity::PARTNER_ACTIVITY_TRAFFIC );
		// videos, images, audios, entries, rcs, widgets
		foreach($results as $resultset)
	    {
	    	$partner_id = $resultset->getInt('PARTNER_ID');
	    	
	    	$bandwidth = (int)($resultset->get ('S0') / 1024);
	    	$stats[$partner_id]["bandwidth_gt"] = $bandwidth;
	    }	    

		// siliding windows - need to format $dateFilter
		// 7 , 30 , 180days back
		self::activeSite ( $connection , $stats ,  $ids_str , $to_date , 7 , "activeSite7" );
		self::activeSite ( $connection , $stats ,  $ids_str , $to_date , 30 , "activeSite30" );
		self::activeSite ( $connection , $stats ,  $ids_str , $to_date , 180 , "activeSite180" );

		self::activePublisher(  $connection , $stats ,  $ids_str , $to_date , 7 , "activePublisher7" );
		self::activePublisher ( $connection , $stats ,  $ids_str , $to_date , 30 , "activePublisher30" );
		self::activePublisher ( $connection , $stats ,  $ids_str , $to_date , 180 , "activePublisher180" );
		
		return $stats;
	}
	
	private static function createDateCriteria ( $from_date , $to_date  , $delta_in_days=null )
	{
		if ( ! $to_date )
		{
			// format the day of today
			$to_date = date ( "Y-m-d");
		}
				
		if ( $delta_in_days )
		{
			$timeStamp = strtotime( $to_date );
			$timeStamp -= 24 * 60 * 60 * $delta_in_days; // (add $delta_in_days days)
			$from_date =  date("Y-m-d", $timeStamp);
		}
		
		$dateFilter = "(1=1 ";
		if ( $from_date ) // the from_date is inclusive 
			$dateFilter .=  "AND ACTIVITY_DATE>='$from_date'" ; // "" AND DATE_FORMAT(ACTIVITY_DATE,'%y-%m-%d')>='$from_date' ";
		if ( $to_date ) // the to_date is exclusive 
			$dateFilter .=  "AND ACTIVITY_DATE<'$to_date'" ; //" AND DATE_FORMAT(ACTIVITY_DATE,'%y-%m-%d')<='$to_date' ";
		$dateFilter .= ")";
		
		return $dateFilter;
	}
	
	// will return a result set per the relevant activity 
	private static function executeQuery ( $connection , $ids_str , $dateFilter , $activity , $sub_activity = false )
	{
		$group_by = "PARTNER_ID";
		if ( $sub_activity ) $group_by .= ",SUB_ACTIVITY";
		 
	    $query = "SELECT PARTNER_ID, SUB_ACTIVITY , SUM(AMOUNT) as S0, SUM(AMOUNT1) AS S1, SUM(AMOUNT2) AS S2, SUM(AMOUNT3) AS S3,SUM(AMOUNT4) AS S4, SUM(AMOUNT5) AS S5 " . 
	    	"FROM partner_activity AS T1 WHERE ACTIVITY=$activity AND" . $dateFilter. " AND PARTNER_ID IN ($ids_str) " .
	    	"GROUP BY $group_by";
	    
		$statement = $connection->query($query);	

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
	
	
	private static function activeSite ( $connection , &$stats ,  $ids_str , $to_date , $delta , $title )
	{
		$dateFilter = self::createDateCriteria ( null, $to_date , $delta );
		$results  = self::executeActiveSiteQuery ( $connection , $ids_str , $dateFilter , PartnerActivity::PARTNER_ACTIVITY_KDP );
		
		foreach($results as $resultset)
	    {
	    	$partner_id = $resultset->getInt('PARTNER_ID');
	    	
	    	$plays = $resultset->get('S1');
	    	$views = $resultset->get('S2');
	    	//$active = (int)$plays + (int)$views;
			$active = "$views : $plays";
	    	$stats[$partner_id][$title ] = $active;
   	    }	  
	}
		
	// plays >= 10, views >= 1
	private static function executeActiveSiteQuery ( $connection , $ids_str , $dateFilter , $activity , $sub_activity = false )
	{
		$min_plays = 1;
		$min_views = 10;
		// PARTNER_SUB_ACTIVITY_KDP_PLAYS = 201
		// PARTNER_SUB_ACTIVITY_KDP_VIEWS = 202
		$group_by = "PARTNER_ID";
		if ( $sub_activity ) $group_by .= ",SUB_ACTIVITY";
		 
	    $query = "SELECT PARTNER_ID, SUB_ACTIVITY , SUM((SUB_ACTIVITY=201) * amount) as S1, SUM((SUB_ACTIVITY=202 )* amount) as S2  " . 
	    	"FROM partner_activity AS T1 WHERE ACTIVITY=$activity  AND " . $dateFilter. " AND PARTNER_ID IN ($ids_str) " .
	    	"GROUP BY $group_by HAVING S1>=$min_plays OR S2>=$min_views ";
	    
		$statement = $connection->query($query);	

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}
		

	private static function activePublisher ( $connection , &$stats ,  $ids_str , $to_date , $delta , $title )
	{
		$dateFilter = self::createDateCriteria ( null, $to_date , $delta );
		$results  = self::executeActivePublisherQuery ( $connection , $ids_str , $dateFilter , PartnerActivity::PARTNER_ACTIVITY_MEDIA );
		
		foreach($results as $resultset)
	    {
	    	$partner_id = $resultset->getInt('PARTNER_ID');
	    	
	    	$entries = $resultset->get('S1');
	    	$widgets = $resultset->get('S2');
	    	//$active = (int)$entries + (int)$widgets;
			$active = "$entries : $widgets";
	    	$stats[$partner_id][$title ] = $active;
   	    }	  
	}	
	
	// widget >= 1, entries >= 1
	private static function executeActivePublisherQuery ( $connection , $ids_str , $dateFilter , $activity  )
	{
		$min_entries = 1;
		$min_widgets = 1;
		
		$group_by = "PARTNER_ID";
		 // SUB_ACTIVITY = 401 - MEDIA
		// amount1+amount2+amount3 - total media entries
		// amount5 - widgets
	    $query = "SELECT PARTNER_ID, SUB_ACTIVITY , SUM(amount1+amount2+amount3) as S1, SUM(amount5) as S2  " . 
	    	"FROM partner_activity AS T1 WHERE ACTIVITY=$activity  AND " . $dateFilter. " AND PARTNER_ID IN ($ids_str) " .
	    	"GROUP BY $group_by HAVING S1>=$min_entries OR S2>=$min_widgets ";
	    
		$statement = $connection->query($query);	

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	
	function createCriteriaByGroup ( $partner_groups , Criteria $c , $group_name , $value )
	{

//	print_r ( $partner_groups->partner_group_list );
		// read the group file from disk
		if ( $group_name == "filter" )
		{
			$group = new partnerGroup();
			$group->field = "text";
			$group->value = $value;
		}
		elseif ( $group_name == "" )
		{
			$group = new partnerGroup();
			$group->field = "text";
			$group->value = "";
		}
		elseif ( $group_name == "_rest" )
		{
			// aggrigate all the lists of ids - they should not appear in the group of type text
			$list_of_ids = array();
			foreach ( $partner_groups->partner_group_list as $g )
			{
				if ( $g->field == "id" )
				{
					$list_of_ids = array_merge( $list_of_ids , $g->value );	
				}
				elseif ( $g->field == "text" )
				{
					$partner_filter_str = "%{$g->value}%";
					// match against the description, name or id
					$crit = $c->getNewCriterion ( PartnerPeer::PARTNER_NAME , $partner_filter_str , Criteria::NOT_LIKE );
                    $crit->addAnd ( $c->getNewCriterion ( PartnerPeer::DESCRIPTION , $partner_filter_str , Criteria::NOT_LIKE ) );
                    $c->addAnd ( $crit );
				}
			}
			$c->addAnd ( PartnerPeer::ID , $list_of_ids , Criteria::NOT_IN );
			return;		
		}
		else
		{
			$l = $partner_groups->partner_group_list;
	 		$group = @$l[strtolower( $group_name )] ;
	
			if ( !$group )
			{
				echo "Cannot find configuration for [$group_name]";
				return;
			}
		}
		
		if ( $group->field == "id" )
		{
			// use the explicit list only
			$c->add ( PartnerPeer::ID , $group->value , Criteria::IN );
			return;
		}
		if ( $group->field == "text" )
		{
			$partner_filter_str = "%{$group->value}%";
			// match against the description, name or id
			$crit = $c->getNewCriterion ( PartnerPeer::PARTNER_NAME , $partner_filter_str , Criteria::LIKE );
			$crit->addOr ( $c->getNewCriterion ( PartnerPeer::DESCRIPTION , $partner_filter_str , Criteria::LIKE ) );
			$c->addAnd ( $crit );
				
			// remove from the list -  $partner_list & $kaltura_related
			$list_of_ids = array();

			// aggrigate all the lists of ids - they should not appear in the group of type text
			foreach ( $partner_groups->partner_group_list as $g )
			{
				if ( $g->field == "id" )
				{
					$list_of_ids = array_merge( $list_of_ids , $g->value );	
				}
			}
			$c->addAnd ( PartnerPeer::ID , $list_of_ids , Criteria::NOT_IN );			
			return;
		}
	}
}


class partnerGroups
{
	private $file_content;
	public $partner_group_list = null;
	 
	public function __construct( $file_name )
	{
		$this->partner_group_list = array();
		$partner_group = null;
		
		$content = file_get_contents ( $file_name );
		$lines = explode ( "\n" , $content );
		foreach ( $lines as $line )
		{
			$trimmed_line = trim ( $line );
			if ( !$trimmed_line ) continue;
			if ( strpos ( $trimmed_line , ":" ) )
			{
//echo "new group: $trimmed_line<br>";			
				// add the previous $partner_group (if exists) to the list 
				if ( $partner_group ) 
				{	
					$partner_group->calculateValue();
					$this->partner_group_list[$partner_group->name]=$partner_group;
							
				}
				// define a new partnerGroup
				$partner_group = new partnerGroup();
				list ( $name , $fields ) = explode ( ":" , $trimmed_line ) ;
				$partner_group->setName( $name );
				$partner_group->setField( $fields );
//echo "new group: [$partner_group->name]<br>";				
			}
			else
			{
				if ( $partner_group )
				{
//echo "data: $trimmed_line<br>";					
					// add it's data
					$partner_group->addToValue( $trimmed_line );
				}
				else
				{
					// error - data comes before the first partner_group !
					echo "Attempting to add {$trimmed_line} to no group at all!";
				}
			}
		}
		
		if ( $partner_group )
		{
			$partner_group->calculateValue();
			$this->partner_group_list[$partner_group->name]=$partner_group;
		}
		
//		print_r ( $partner_group_list );
	}
}

class partnerGroup
{
	const NEW_LINE = "\n";
	
	public $name = null;
	//public $fields = null;
	public $field = null;
	public $value_str = null; 
	public $value = null;
	
	public function setName( $n )
	{
		$this->name = trim ( $n );
	}
	
	
	public function setField ( $f_str )
	{
		$this->field = trim($f_str);
	}
/*	
	public function setFields ( $f_str )
	{
		$f_list = explode ( "," , $f_str );
		$this->fields = array();
		foreach ( $f_list as $f )
		{
			$this->fields[] = trim ( $f );
		}
	}
*/
	public function addToValue ( $v )
	{
		$this->value_str .= trim ( str_replace ( self::NEW_LINE , "" , $v ));
	}
	
	public function calculateValue()
	{
		if ( $this->field == "id" )
		{
			$this->value = array();
			$temp_value = explode ( "," , $this->value_str );
			foreach ( $temp_value as $v )
			{
				$temp_value[] = trim ( $v );
			}
			$this->value = $temp_value;
		}
		else
		{
			$this->value = $this->value_str; 
		}
		
	}
}
?>