<?php
require_once ( "kalturaWebserviceRenderer.class.php" );
/**
 * This class will make common tasks in the action classes much easier.
 *
 */
abstract class kalturaBaseWebserviceAction extends kalturaAction
{
	protected static $escape_text = false;
	
	protected $response_type = kalturaWebserviceRenderer::RESPONSE_TYPE_XML;
	
	protected function renderDataInRequestedFormat( $response_params , $return_value = false )
	{
		$renderer = new kalturaWebserviceRenderer( $this );
		list ( $response , $content_type ) = $renderer->renderDataInRequestedFormat( $response_params , $this->response_type,  self::$escape_text );

		$this->getResponse()->setHttpHeader ( "Content-Type"  , $content_type  );
		
		if ( $return_value )
		{
			return $response ;
		}
		else
		{
			return $this->renderText( $response ) ;
		}
	}
	
}



?>