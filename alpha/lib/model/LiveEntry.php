<?php
/**
 * @package Core
 * @subpackage model
 */
abstract class LiveEntry extends entry
{
	/* (non-PHPdoc)
	 * @see entry::getLocalThumbFilePath()
	 */
	public function getLocalThumbFilePath($version , $width , $height , $type , $bgcolor ="ffffff" , $crop_provider=null, $quality = 0,
		$src_x = 0, $src_y = 0, $src_w = 0, $src_h = 0, $vid_sec = -1, $vid_slice = 0, $vid_slices = -1, $density = 0, $stripProfiles = false, $flavorId = null, $fileName = null)
	{
		if ($this->getStatus () == entryStatus::DELETED || $this->getModerationStatus () == moderation::MODERATION_STATUS_BLOCK) {
			KalturaLog::log ( "rejected live stream entry - not serving thumbnail" );
			KExternalErrors::dieError ( KExternalErrors::ENTRY_DELETED_MODERATED );
		}
		
		$contentPath = myContentStorage::getFSContentRootPath ();
		$msgPath = $contentPath . "content/templates/entry/thumbnail/live_thumb.jpg";
		return myEntryUtils::resizeEntryImage ( $this, $version, $width, $height, $type, $bgcolor, $crop_provider, $quality, $src_x, $src_y, $src_w, $src_h, $vid_sec, $vid_slice, $vid_slices, $msgPath, $density, $stripProfiles );
	}
	
	public function setOfflineMessage ( $v )	{	$this->putInCustomData ( "offlineMessage" , $v );	}
	public function getOfflineMessage (  )		{	return $this->getFromCustomData( "offlineMessage" );	}

	public function getRecordStatus ()
	{
	    return $this->getFromCustomData("record_status");
	}
	
	public function setRecordStatus ($v)
	{
	    $this->putInCustomData("record_status", $v);
	}

	public function getDvrStatus ()
	{
	    return $this->getFromCustomData("dvr_status");
	}
	
	public function setDvrStatus ($v)
	{
	    $this->putInCustomData("dvr_status", $v);
	}
	
    public function getDvrWindow ()
	{
	    return $this->getFromCustomData("dvr_window");
	}
	
	public function setDvrWindow ($v)
	{
	    $this->putInCustomData("dvr_window", $v);
	}
	
	public function setLiveStreamConfigurations (array $v)
	{
		$this->putInCustomData('live_stream_configurations', $v);
	}
	
	public function getLiveStreamConfigurations ()
	{
		return $this->getFromCustomData('live_stream_configurations', null, array());
	}
}
