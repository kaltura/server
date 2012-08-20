<?php
/**
 * @package    Core
 * @subpackage kEditorServices
 */
require_once ( "myKshowMetadataCreator.class.php");
require_once ( "myContentStorage.class.php");
require_once ( "defKeditorservicesAction.class.php");

/**
 * @package    Core
 * @subpackage kEditorServices
 */
class createDefaultMetadataAction extends defKeditorservicesAction
{
	/**
	 * Executes index action
	 */
	protected function executeImpl ( kshow $kshow )
	{
		$this->xml_content = ""; 

		$kshow_id = $this->kshow_id;
		if ( $kshow_id == NULL || $kshow_id == 0 )		return sfView::SUCCESS;
		$metadata_creator = new myKshowMetadataCreator ();

		$this->show_metadata = $metadata_creator->createMetadata ( $kshow_id );

//		$kshow = kshowPeer:retrieveByPK( $kshow_id );
		$entry = entryPeer::retrieveByPK( $kshow->getShowEntryId() );


		// TODO - this should never happen
		if ( $entry == NULL )
		{
			// there is no show entry for this show !
			$entry = $kshow->createEntry ( entry::ENTRY_MEDIA_TYPE_SHOW , $kshow->getProducerId() );
		}

		$content_path = myContentStorage::getFSContentRootPath();
		$file_path = $content_path.$entry->getDataPath() ;

		// check to see if the content of the file changed
		$current_metadata = kFile::getFileContent( $file_path );

		$comp_result = strcmp ( $this->show_metadata , $current_metadata  );
		if ( $comp_result != 0 )
		{
			$ext = pathinfo($file_path, PATHINFO_EXTENSION);
			if ( $ext != "xml")
			{
				// this is for the first time - override the template path by setting the data to NULL
				$entry->setData ( NULL );
				$file_path = pathinfo($file_path, PATHINFO_DIRNAME) . "/" . kFile::getFileNameNoExtension ( $file_path ) . ".xml";
			}

			// this will increment the name if needed
			$entry->setData ( $file_path );
			$file_path = $content_path.$entry->getDataPath() ;

			$entry->save();

			myContentStorage::fullMkdir ( $file_path );
			kFile::setFileContent( $file_path , $this->show_metadata );
			
			$this->xml_content = $this->show_metadata;
			
			
		}

	}

	protected function noSuchKshow ( $kshow_id )
	{
		$this->xml_content = "";
	}

}
?>