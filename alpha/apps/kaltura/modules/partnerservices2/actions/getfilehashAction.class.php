<?php
require_once ( "defPartnerservices2Action.class.php");
require_once ( "myPartnerUtils.class.php");

class getfilehashAction extends defPartnerservices2Action
{
	public function describe()
	{
		return array();		
	}
	
	protected function needKuserFromPuser ( )
	{
		// will use the $puser_id for the hashcode no need to feth the kuser_id
		return self::KUSER_DATA_NO_KUSER;
	}
	
	public function executeImpl ( $partner_id , $subp_id , $puser_id , $partner_prefix , $puser_kuser )
	{
		$count = $this->getP ( "count" , 1 );
		if ( $count < 1 ) $count =1;
		
		for ( $i=1 ; $i<=$count ; $i++ )
		{
			$hash =  md5 ( "getfilehashAction" . $partner_id . $puser_id . $i . time()) ;
			$this->addMsg ( "hash$i" , $hash );
		}
	}
}
?>