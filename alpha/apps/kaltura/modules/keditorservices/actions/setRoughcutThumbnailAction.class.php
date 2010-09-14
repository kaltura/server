<?php
require_once ( "myKshowUtils.class.php");
require_once ( "defKeditorservicesAction.class.php");
class setRoughcutThumbnailAction extends defKeditorservicesAction
{
	protected function executeImpl( kshow $kshow, entry &$entry )
	{
		$this->res = "";
		
		$likuser_id = $this->getLoggedInUserId();

		// if we allow multiple rouchcuts - there is no reason for one suer to override someone else's thumbnail
		if ( $this->allowMultipleRoughcuts()  )
		{
			if ( $likuser_id != $entry->getKuserId())
			{
				// ERROR - attempting to update an entry which doesnt belong to the user
				return "<xml>!!</xml>";//$this->securityViolation( $kshow->getId() );
			}
		}

		$debug = @$_GET["debug"];
		/*
		$kshow_id = @$_GET["kshow_id"];
		$debug = @$_GET["debug"];
		
		$this->kshow_id = $kshow_id;

		if ( $kshow_id == NULL || $kshow_id == 0 ) return;

		$kshow = kshowPeer::retrieveByPK( $kshow_id );
		
		if ( ! $kshow ) 
		{
			$this->res = "No kshow " . $kshow_id ;
			return;	
		}

		// is the logged-in-user is not an admin or the producer - check if show can be published	
		$likuser_id = $this->getLoggedInUserId();
		$viewer_type = myKshowUtils::getViewerType($kshow, $likuser_id);
		if ( $viewer_type != KshowKuser::KSHOWKUSER_VIEWER_PRODUCER && ( ! $kshow->getCanPublish() ) ) 
		{
			// ERROR - attempting to publish a non-publishable show
			return "<xml>!</xml>";//$this->securityViolation( $kshow->getId() );
		}
		
		
		// ASSUME - the kshow & roughcut already exist
		$show_entry_id = $kshow->getShowEntryId();
		$roughcut = entryPeer::retrieveByPK( $show_entry_id );

		$roughcut = entryPeer::retrieveByPK( $entry_id );
		
 
		if ( ! $roughcut)
		{
			$this->res = "No roughcut for kshow " . $kshow->getId() ;
			return;	
		}
		*/		
//		echo "for entry: $show_entry_id current thumb path: " . $entry->getThumbnail() ;
		
		$entry->setThumbnail ( ".jpg");
		$entry->save();
		
		//$thumb_data = $_REQUEST["ThumbData"];

		if(isset($HTTP_RAW_POST_DATA))
			$thumb_data = $HTTP_RAW_POST_DATA;
		else
			$thumb_data = file_get_contents("php://input");

//		$thumb_data = $GLOBALS["HTTP_RAW_POST_DATA"];
		$thumb_data_size = strlen( $thumb_data );
		
		$bigThumbPath = myContentStorage::getFSContentRootPath() .  $entry->getBigThumbnailPath();
		
		kFile::fullMkdir ( $bigThumbPath );
		kFile::setFileContent( $bigThumbPath , $thumb_data );
		
		$path = myContentStorage::getFSContentRootPath() .  $entry->getThumbnailPath();
		
		kFile::fullMkdir ( $path );
		myFileConverter::createImageThumbnail( $bigThumbPath , $path );
		
		$roughcutPath = myContentStorage::getFSContentRootPath() . $entry->getDataPath();
		$xml_doc = new DOMDocument();
		$xml_doc->load( $roughcutPath );
		
		if (myMetadataUtils::updateThumbUrl($xml_doc, $entry->getBigThumbnailUrl()))
			$xml_doc->save($roughcutPath);
			
		$this->res = $entry->getBigThumbnailUrl();
	}
	


}

?>
