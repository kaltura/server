<?php
/**
 * @package api
 * @subpackage ps2
 */
require_once 'getentryAction.class.php';

/**
 * @package api
 * @subpackage ps2
 */
class getplaylistAction extends getentryAction
{
	public function describe()
	{
		return 
			array (
				"display_name" => "getPlaylist",
				"desc" => "" ,
				"in" => array (
					"mandatory" => array ( 
						"playlist_id" => array ("type" => "integer", "desc" => "")
						),
					"optional" => array (
						"detailed" => array ("type" => "boolean", "desc" => "")
						)
					),
				"out" => array (
					"playlist" => array ("type" => "entry", "desc" => ""),
					"embed" => array ("type" => "string", "desc" => "The HTML embed code for this playlist"),
					),
				"errors" => array (
				)
			); 
	}
	
	protected function addData ( $playlist )
	{
		// TODO - how to retrieve the widget_id and ui_conf_id for this partner ?
		// ui_conf_id = 190 will hold the correct ui_conf for the playlist
		list ( $embed , $width , $height ) = myPlaylistUtils::getEmbedCode( $playlist , null , 199 ) ; 
		
		$this->addMsg ( "embed" , $embed );
		$this->addMsg ( "width" , $width );
		$this->addMsg ( "height" , $height );
	}
	
	protected function getObjectPrefix () { return "playlist"; }
	
	protected function getCriteria (  ) 
	{ 
		$c = new Criteria();
		$c->addAnd ( entryPeer::TYPE , entryType::PLAYLIST );
		return $c; 
	}
}
?>