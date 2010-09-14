<?php
require_once ( "kalturaSystemAction.class.php" );

class serveFileAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		$file_path = $this->getP ( "file_path" );
		
		if ( ! file_exists( $file_path ))
		{ 
			echo "Cannot find file [$file_path]";
			die();
		} 
		$mime_type = kFile::mimeType( $file_path );
//		echo "[[$mime_type]]";
		kFile::dumpFile($file_path , $mime_type );
		die();
	}
}
?>