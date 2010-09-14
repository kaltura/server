<?php
class dashboardUtils
{

	public static function getUpdatedKshows ( $bands_only = true )
	{
		$c = new Criteria();
		//$c->addJoin ( entryPeer::KSHOW_ID , kshowPeer::ID , Criteria::INNER_JOIN );
		$c->addJoin ( entryPeer::KSHOW_ID , kshowPeer::ID );
		$c->addSelectColumn( entryPeer::KSHOW_ID );
		$c->addAsColumn( 'cnt' , 'COUNT('.entryPeer::KSHOW_ID.')' );
		$c->addAnd ( kshowPeer::PARTNER_ID , 5 , ( $bands_only ? Criteria::EQUAL : Criteria::NOT_EQUAL ) );
		$c->add ( entryPeer::TYPE , entry::ENTRY_TYPE_MEDIACLIP );
		// only band that have new entries (newer then 60 seconds from the time the kshow was created)
		$crit = $c->getNewCriterion( entryPeer::CREATED_AT ,
		"UNIX_TIMESTAMP(" . entryPeer::CREATED_AT .")>UNIX_TIMESTAMP(" .kshowPeer::CREATED_AT .")+60" ,
		Criteria::CUSTOM  );
		$c->add( $crit );


		$c->addGroupByColumn(entryPeer::KSHOW_ID);

		$rs = entryPeer::doSelectStmt($c);
		$kshowIds = Array();

		$res = $rs->fetchAll();
		foreach($res as $record) 
		{
			$kshowIds[$record[0]] = $record[1];
		}
		
//		// old code from doSelectRs
//		while($rs->next())
//		{
//			$kshowIds[$rs->getInt(1)]=$rs->getInt(2);
//		}

		$rs->close();

		return $kshowIds;
	}


	public  static function updateKusersRoughcutCount ( $kuser_list )
	{
		$kuser_ids = self::getAllIds ( $kuser_list );
		$kusers_roughcuts = self::getKusersRoughcutCount ( $kuser_ids );

		foreach ( $kuser_list as $kuser )
		{
			$count = @$kusers_roughcuts[$kuser->getId()]; // very strange if $count will not be a number !
			$kuser->setRoughcutCount ( $count >= 0 ? $count : 0 );
		}

	}

	public static function getKusersRoughcutCount ( array $kuser_id_list )
	{
		$c = new Criteria();
		$c->add ( entryPeer::TYPE , entry::ENTRY_TYPE_SHOW );
		$c->add ( entryPeer::KUSER_ID , $kuser_id_list , Criteria::IN );
		$c->addSelectColumn( entryPeer::KUSER_ID );
		$c->addAsColumn( 'cnt' , 'COUNT('.entryPeer::KUSER_ID.')' );
		$c->addGroupByColumn(entryPeer::KUSER_ID);

		$rs = entryPeer::doSelectStmt($c);
		$kusers_roughcuts = Array();

		$res = $rs->fetchAll();
		foreach($res as $record) 
		{
			$id = $record[0];
			$cnt = $record[1];
			$kusers_roughcuts[$id] = $cnt;
		}
		
//		// old code from doSelectRs
//		while($rs->next())
//		{
//			$id= $rs->getInt(1);
//			$cnt = $rs->getInt(2);
//			$kusers_roughcuts[$id]=$cnt;
//		}

		$rs->close();

		return $kusers_roughcuts;
	}

	public  static function updateKshowsRoughcutCount ( $kshow_list )
	{
		$kshow_ids = self::getAllIds ( $kshow_list );
		$kshow_roughcuts = self::getKshowRoughcutCount ( $kshow_ids );

		foreach ( $kshow_list as $kshow )
		{
			$count = @$kshow_roughcuts[$kshow->getId()]; // very strange if $count will not be a number !
			$kshow->setRoughcutCount ( $count >= 0 ? $count : 0 );
		}

	}

	public  static  function getKshowRoughcutCount ( array $kuser_id_list )
	{
		$c = new Criteria();
		$c->add ( entryPeer::TYPE , entry::ENTRY_TYPE_SHOW );
		$c->add ( entryPeer::KSHOW_ID , $kuser_id_list , Criteria::IN );
		$c->addSelectColumn( entryPeer::KSHOW_ID );
		$c->addAsColumn( 'cnt' , 'COUNT('.entryPeer::KSHOW_ID.')' );
		$c->addGroupByColumn(entryPeer::KSHOW_ID);

		$rs = entryPeer::doSelectStmt($c);
		$kusers_roughcuts = Array();

		$res = $rs->fetchAll();
		foreach($res as $record) 
		{
			$id = $record[0];
			$cnt = $record[1];
			$kusers_roughcuts[$id] = $cnt;
		}
		
//		// old code from doSelectRs
//		while($rs->next())
//		{
//			$id= $rs->getInt(1);
//			$cnt = $rs->getInt(2);
//			$kusers_roughcuts[$id]=$cnt;
//		}

		$rs->close();

		return $kusers_roughcuts;
	}


	public  static  function getAllIds ( $obj_list )
	{
		$ids = array();
		foreach ( $obj_list as $obj )
		{
			$ids[] = $obj->getId();
		}

		return $ids;
	}

	public static function option ( $current_value , $value , $text )
	{
		$str = "<option value=\"$value\" " . ( $current_value==$value ? "selected=\"selected\"" : "" ) . ">$text</option>";
		return $str;
	}

	public static function options ( $current_value , array $arr )
	{
		$str = "";
		foreach ( $arr as $name => $value )
		{
			$str .= self::option( $current_value , $name , $value );
		}
		return $str;
	}
	
	public static function partnerOptions ( $current_value  )
	{
		$options[-1] = "All";
		
		$c = new Criteria();
		$partners = PartnerPeer::doSelect($c);
		foreach($partners as $partner)
			$options[$partner->getId()] = $partner->getPartnerName();
			
		return dashboardUtils::options ( $current_value , $options );
			//array (-1 => "All" , 0 => "Kaltura" , 100 => "FB" , 5 => "MS Bands" , 10 => "BuddyLube|Mudvayne") );
	}
}
?>