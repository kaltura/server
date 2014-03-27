<?php
/**
 * @package UI-infra
 * @subpackage Security
 */
class Infra_PreventFramesPlugin extends Zend_Controller_Plugin_Abstract 
{
	/**
	 * Called before Zend_Controller_Front exits its dispatch loop.
	 *
	 * @return void
	 */
	public function dispatchLoopShutdown()
	{
		$response = $this->getResponse();
		if ( ! $response ) {
			$response = new Zend_Controller_Response_Http();
			$this->setResponse( $response );
		}
		
		// Prevent opening from within an iFrame (valid for browsers that respect this header)
		$response->setHeader("X-Frame-Options", "DENY");
	}
}