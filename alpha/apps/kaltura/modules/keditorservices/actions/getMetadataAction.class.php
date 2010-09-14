<?php
require_once ( "defKeditorservicesAction.class.php");
class getMetadataAction extends defKeditorservicesAction
{
	/**
	 * Executes addComment action, which returns a form enabling the insertion of a comment
	 * The request may include 1 fields: entry id.
	 */
	protected function executeImpl( kshow $kshow, entry &$entry )
	{
		$version = @$_REQUEST["version"]; // it's a path on the disk
		if ( kString::beginsWith( $version , "." ) )
		{
			// someone is trying to hack in the system 
			return sfView::ERROR;	
		}
		
		// in case we're making a roughcut out of a regular invite, we start from scratch
		if ($entry->getMediaType() != entry::ENTRY_MEDIA_TYPE_SHOW || $entry->getDataPath($version) === null)
		{
			$this->xml_content = "<xml></xml>"; 
			return;
		}
		
		// fetch content of file from disk - it should hold the XML
		$file_name = myContentStorage::getFSContentRootPath() . "/" . $entry->getDataPath( $version );
		
		//echo "[$file_name]";
		
		if ( kString::endsWith( $file_name  , "xml" ))
		{
			if ( file_exists( $file_name ) )
			{
				$this->xml_content = kFile::getFileContent( $file_name );
				
			//	echo "[" . $this->xml_content . "]" ;
				 
			}
			else
			{
				$this->xml_content = "<xml></xml>"; 
			}
			
			myMetadataUtils::updateEntryForPending( $entry , $version , $this->xml_content );
		}
		else return sfView::ERROR;// this is NOT an xml file we are looking for !
		
		
	}
	
	protected function noSuchEntry ( $entry_id )
	{
		$this->xml_content = "No such entry [$entry_id]";
	}
	
	protected function noSuchKshow ( $kshow_id )
	{
		$this->xml_content = "No such show [$kshow_id]";
	}
}

?>