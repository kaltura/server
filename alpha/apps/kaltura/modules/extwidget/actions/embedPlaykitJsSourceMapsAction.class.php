<?php

/**
 * @package Core
 * @subpackage externalWidgets
 */
class embedPlaykitJsSourceMapsAction extends sfAction
{
	var $uiconf_id = null;

	public function execute()
	{
		//Get file name
		$fileName = $this->getRequestParameter('path');
		if (!$fileName)
			KExternalErrors::dieError(KExternalErrors::MISSING_PARAMETER, 'path');
		//file name should be base64 encoded string which ends with min.js.map
		if (!preg_match('`^[a-zA-Z0-9+/]+={0,2}.min.js.map$`', $fileName)) {
			KExternalErrors::dieGracefully("Wrong source map name pattern");
		}

		//Get config params
		try {
			$bundleWebDirPath = kConf::get('playkit_js_bundles_path');
		} catch (Exception $ex) {
			KExternalErrors::dieGracefully($ex->getMessage());
		}

		$sourceMapFilePath = $bundleWebDirPath . $fileName;
		$sourceMap = kFileUtils::getDumpFileRenderer($sourceMapFilePath, "application/octet-stream");

		echo($sourceMap->output());

		KExternalErrors::dieGracefully();
	}
}
