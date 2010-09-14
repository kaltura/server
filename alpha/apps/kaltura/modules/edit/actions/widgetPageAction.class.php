<?php
require_once ( "kalturaAction.class.php");
/**
 * edit actions.
 *
 * @package    kaltura
 * @subpackage edit
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class widgetPageAction extends kalturaAction
{
	public function execute()
	{
		$this->kshow_id = $this->getRequestParameter('kshow_id' , 10010 );
		$this->uid = $this->getRequestParameter('uid' , $this->getLoggedInUserId() );
		if ( ! $this->uid ) $this->uid = 10000;
		$this->partner_id = 0;
		$this->subp_id = 100;
		$this->partner_name  = "Kaltura";
	}
}
?>