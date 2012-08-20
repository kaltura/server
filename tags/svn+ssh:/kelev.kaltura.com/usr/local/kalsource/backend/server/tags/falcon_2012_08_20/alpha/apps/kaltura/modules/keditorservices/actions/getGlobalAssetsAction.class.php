<?php
/**
 * @package    Core
 * @subpackage kEditorServices
 */
require_once ( "defKeditorservicesAction.class.php");

/**
 * fetch global assets - ones that
 * 
 * @package    Core
 * @subpackage kEditorServices
 */
class getGlobalAssetsAction extends defKeditorservicesAction
{
	protected function executeImpl ( kshow  $kshow )
	{
		$asset_type = $this->getRequestParameter( "type" , entry::ENTRY_MEDIA_TYPE_VIDEO );

		if ( $asset_type > entry::ENTRY_MEDIA_TYPE_AUDIO || $asset_type < entry::ENTRY_MEDIA_TYPE_VIDEO )
/*		
		if ( ! in_array( $asset_type, 
			array ( entry::ENTRY_MEDIA_TYPE_VIDEO , 
					entry::ENTRY_MEDIA_TYPE_IMAGE , 
					entry::ENTRY_MEDIA_TYPE_TEXT , 
					entry::ENTRY_MEDIA_TYPE_HTML , 
					entry::ENTRY_MEDIA_TYPE_AUDIO ) ) ) */
		{
			// TODO - 
			// trying to fetch invalid media type	
		}
		
		$show_entry_id = $kshow->getShowEntryId();
		$intro_id = $kshow->getIntroId();
		
		
		$c = new Criteria();
		$c->add ( entryPeer::KUSER_ID , kuser::KUSER_KALTURA );
		$c->add ( entryPeer::TYPE , kuser::KUSER_KALTURA );
				
		$this->entry_list = entryPeer::doSelect( $c );
		if ( $this->entry_list == NULL )
			$this->entry_list = array ();
	}
	
	protected function noSuchKshow ( $kshow_id )
	{
		$this->entry_list = array ();
	}

}

?>