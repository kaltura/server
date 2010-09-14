<?php

/**
 * extservices actions.
 *
 * @package    kaltura
 * @subpackage extservices
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class redirectAction extends kalturaAction
{
	public function execute()
	{
		$return_to = $_REQUEST["return_to"];
		$url = $_REQUEST["url"];
		setcookie( 'kaltura_redirect', base64_encode($return_to), time() + 3600 , '/' );
		
		$this->redirect( $url );
	}
}
