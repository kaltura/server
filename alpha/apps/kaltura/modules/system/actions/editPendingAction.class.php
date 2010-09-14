<?php
require_once ( "kalturaSystemAction.class.php" );

class editPendingAction extends kalturaSystemAction
{

	public function execute()
	{
		$this->forceSystemAuthentication();
		
		$kshow_id = @$_REQUEST["kshow_id"];
		$this->kshow_id = $kshow_id;
		$this->kshow = NULL;
		
		$entry_id = @$_REQUEST["entry_id"];
		$this->entry_id = $entry_id;
		$this->entry = NULL;
		
		$this->message =  "";
		if ( !empty ( $kshow_id ))
		{
			$this->kshow = kshowPeer::retrieveByPK( $kshow_id );
			if (  ! $this->kshow )
			{
				$this->message = "Cannot find kshow [$kshow_id]";
			}
			else
			{
				$this->entry = $this->kshow->getShowEntry();
			} 
		}
		elseif ( !empty ( $kshow_id ))
		{
			$this->entry = entryPeer::retrieveByPK( $entry_id );
			if ( ! $this->entry )
			{
				$this->message = "Cannot find entry [$entry_id]";
			}
			else
			{
				$this->kshow = $this->$this->entry->getKshow();
			}
		}
		
		if ( $this->kshow )
		{
			$this->metadata = $this->kshow->getMetadata();
		}
		else
		{
			$this->metadata = "";
		}
		
		$pending_str = $this->getP ( "pending" );
		$remove_pending = $this->getP ( "remove_pending" );
		
		
		if ( $this->metadata && ( $remove_pending || $pending_str ) )
		{
			if  ( $remove_pending )				$pending_str = "";
			
			$xml_doc = new DOMDocument();
			$xml_doc->loadXML( $this->metadata );
			$metadata = kXml::getFirstElement( $xml_doc , "MetaData" );
			$should_save = kXml::setChildElement( $xml_doc , $metadata , "Pending" , $pending_str , true );
			if  ( $remove_pending )
				$should_save = kXml::setChildElement( $xml_doc , $metadata , "LastPendingTimeStamp" /*myMetadataUtils::LAST_PENDING_TIMESTAMP_ELEM_NAME*/ , "" , true );
			
			if ( $should_save )
			{
				$fixed_content = $xml_doc->saveXML();
				$content_dir =  myContentStorage::getFSContentRootPath();
				$file_name = realpath( $content_dir . $this->entry->getDataPath( ) );
				
				$res = file_put_contents( $file_name , $fixed_content ); // sync - NOTOK 
				
				$this->metadata = $fixed_content;
			}
		}
		
		$this->pending = $pending_str;
		
		$this->kshow_id = $kshow_id;
		$this->entry_id = $entry_id;
	}
}
?>