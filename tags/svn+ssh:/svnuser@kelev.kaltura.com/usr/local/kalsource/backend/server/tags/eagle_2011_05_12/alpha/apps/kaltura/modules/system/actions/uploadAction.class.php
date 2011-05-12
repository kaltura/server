<?php
/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
require_once ( "model/genericObjectWrapper.class.php" );
require_once ( "kalturaSystemAction.class.php" );

/**
 * @package    Core
 * @subpackage system
 * @deprecated
 */
class uploadAction extends kalturaSystemAction
{

	public function execute()
	{
		$this->forceSystemAuthentication();

		$this->basePath = "/content/dynamic/";
		$dynamicRoot = myContentStorage::getFSContentRootPath().$this->basePath;

		if($this->getRequest()->getMethod() == sfRequest::POST)
		{
			$origFilename = basename($_FILES['Filedata']['name']);
			
			$fullPath = $dynamicRoot.$origFilename;
			move_uploaded_file($_FILES['Filedata']['tmp_name'], $fullPath);
			chmod ( $fullPath , 0777 );
			
			return $this->renderText("ok");
		}
		else if($this->getRequest()->getMethod() == sfRequest::DELETE)
		{
			$filename = basename($_REQUEST['fileName']);
			kFile::deleteFile($dynamicRoot.$filename);
			
			return $this->renderText("ok");
		}
		
		$this->files = kFile::dirListExtended($dynamicRoot, false);
	
		$this->extraHead = <<<EOT
		<style type="text/css">
			table{ font-size:1.2em; width:100%; margin:40px 0 0 0; }
			table thead{ font-size:1.4em; }
				table thead td{ border-bottom:1px solid #444; margin-bottom:20px; }
			table tbody td{ padding:2px 0; color:#ccc; }
				table tbody td b{ font-weight:normal; cursor:default; }
				table tbody td span.btn{ margin-right:12px;}
			div#helper{ display:none; position:absolute; left:0; width:250px; }
			div#helper img{ float:right; max-width:250px; }
		</style>
EOT;
		
				
	}
}
?>