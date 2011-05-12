<?php
/**
 * @package    Core
 * @subpackage kEditorServices
 */
require_once ( "defKeditorservicesAction.class.php");
require_once ( "myKshowUtils.class.php");

/**
 * @package    Core
 * @subpackage kEditorServices
 */
class getKshowInfoAction extends defKeditorservicesAction
{
	protected function executeImpl ( kshow $kshow, entry &$entry )
	{
		if ($entry->getMediaType() == entry::ENTRY_MEDIA_TYPE_SHOW)
			$this->show_versions = array_reverse($entry->getAllversions());
		else
			$this->show_versions = array();
			
		$this->producer = kuser::getKuserById ( $kshow->getProducerId() );
		$this->editor = $entry->getKuser();
		$this->thumbnail = $entry ? $entry->getThumbnailPath() : "";
		
		// is the logged-in-user is an admin or the producer or the show can always be published...	
		$likuser_id = $this->getLoggedInUserId();
		$viewer_type = myKshowUtils::getViewerType($kshow, $likuser_id);
		$this->entry = $entry ? $entry : new entry() ; // create a dummy entry for the GUI
		$this->can_publish =  ( $viewer_type == KshowKuser::KSHOWKUSER_VIEWER_PRODUCER ||  $kshow->getCanPublish() ) ;
	}

	protected function noSuchKshow ( $kshow_id )
	{
		$this->kshow = new kshow();
		$this->producer = new kuser() ;
	}

}

?>