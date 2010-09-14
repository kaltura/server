<?php

require_once ( "kalturaSystemAction.class.php" );

class ajaxGetSwfVersionsAction extends kalturaSystemAction
{
	public function execute()
	{
		$this->forceSystemAuthentication();
		
		$type = $this->getRequestParameter("type");
		$versionsPath = "/web/flash/";
		
		$uiConf = new uiConf();
		$dirs = $uiConf->getDirectoryMap();
		
		$versions = array();
		if (array_key_exists($type, $dirs))
		{
			$swfDir = $dirs[$type];
			$path = $versionsPath . $swfDir . "/";
			$path = realpath($path);
			$files = scandir($path);
			foreach($files as $file)
			{
				if (is_dir(realpath($path . "/" . $file)) && strpos($file, "v") === 0)
					$versions[] = $file;
			}
		}
		
		return $this->renderText(json_encode($versions));
	}
}

?>