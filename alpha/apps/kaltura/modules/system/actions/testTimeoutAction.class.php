<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class testTimeoutAction extends sfAction 
{
	public function execute () 
	{
		$timeout_in_seconds = $this->getRequestParameter( "secs" , 10 );
		
		sleep($timeout_in_seconds);
		$res = "Slept $timeout_in_seconds seconds"		;

		return $this->renderText( $res );
	}
}
?>