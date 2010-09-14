<?php
require_once ( "defKeditorservicesAction.class.php");
class getEntryInfoAction extends defKeditorservicesAction
{
	// here the $kshow will be null (thanks to fetchKshow=false) and entry will 
	public  function executeImpl ( kshow $kshow, entry &$entry )
	{
		$genericWidget = "";
		$myspaceWidget = "";
		
		$kshow_id = $kshow->getId();
		$entry_id = $entry->getId();
		
		if (!$kshow->getPartnerId() && !$this->forceViewPermissions ( $kshow, $kshow_id , false , false ))
			die;
		
		$this->kshow_category  = $kshow->getTypeText();
		$this->kshow_description = $kshow->getDescription();
		$this->kshow_name = $kshow->getName();
		$this->kshow_tags = $kshow->getTags();
		
		$kdata = @$_REQUEST["kdata"];
		if ($kdata == "null")
			$kdata = "";
			
		$this->widget_type = @$_REQUEST["widget_type"];
		
		list($genericWidget, $myspaceWidget) = myKshowUtils::getEmbedPlayerUrl($kshow_id, $entry_id, false, $kdata); 
		
		if ($entry_id == 1002)
			$this->share_url = requestUtils::getHost() .  "/index.php/corp/kalturaPromo";
		else if ($kdata)
			$this->share_url = myKshowUtils::getWidgetCmdUrl($kdata, "share");
		else
			$this->share_url = myKshowUtils::getUrl( $kshow_id )."&entry_id=$entry_id";
		
		//list($status, $kmediaType, $kmediaData) = myContentRender::createPlayerMedia($entry); // myContentRender class removed, old code
		$status = $entry->getStatus();
		$kmediaType = $entry->getMediaType();
		$kmediaData = "";
		
		$this->message = ($kmediaType == entry::ENTRY_MEDIA_TYPE_TEXT) ? $kmediaData : "";
		
		$this->generic_embed_code = $genericWidget;
		$this->myspace_embed_code = $myspaceWidget;
		$this->thumbnail = $entry ? $entry->getBigThumbnailPath(true) : "";
		$this->kuser = $entry->getKuser();
		$this->entry = $entry;		
	}
}

?>