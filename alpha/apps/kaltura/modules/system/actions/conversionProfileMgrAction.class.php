<?php
require_once ( "kalturaSystemAction.class.php" );
class conversionProfileMgrAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		myDbHelper::$use_alternative_con = null;
		
		$go = $this->getP ( "go" );
		
		$filter = new ConversionProfileFilter(  );
		$this->list = array();

		$fields_set = $filter->fillObjectFromRequest( $_REQUEST , "filter_" , null );
		
		if ( $go )
		{
			$c = new Criteria();
					
			// filter		
			
			$filter->attachToCriteria( $c );
			
			//if ($order_by != -1) kshowPeer::setOrder( $c , $order_by );
			$this->list = ConversionProfilePeer::doSelect( $c );
		}

		$the_conv = myConversionProfileUtils::getConversionProfile( $filter->get ( "_eq_partner_id" ) , $filter->get ( "_eq_profile_type" ) );
		
		$selected = false;
		if ( $the_conv )
		{
			foreach ( $this->list as &$conv )
			{
				if ( $conv->getId() == $the_conv->getId() ) 
				{
					$selected = true;
					$conv->selected = true;
				}
			}
		}
		
		// is none was selected - need to add the_conv to the list 
		if ( ! $selected && $the_conv )
		{
			$the_conv->selected = true;
			$this->list[] = $the_conv;
		}
		
		$this->filter = $filter;
	}
}
?>