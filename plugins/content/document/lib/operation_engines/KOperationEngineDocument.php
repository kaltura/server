<?php

abstract class KOperationEngineDocument extends KSingleOutputOperationEngine {

	const DOC_METADATA_JSON_NAME = 'docMetadata.json';

	protected function getPdfInfo($file) {
		$pdfInfoExe = KBatchBase::$taskConfig->params->pdfInfo;
		$output = null;
		$command = $pdfInfoExe . " \"" . realpath($file) . "\" 2>&1";
		KalturaLog::info("Executing: $command");
		exec($command, $output);
		return $output;
	}
	
 	private function getFileInfo($filePath)
	{
		$returnValue = null;
		$output = null;
		$command = "file '{$filePath}'";
		KalturaLog::info("Executing: $command");
		exec($command, $output, $returnValue);
		return implode("\n",$output);
	}
	
	protected function checkFileType($filePath, $supportedTypes) {
	
		$matches = null;
		$fileType = null;
		
		$fileInfo = $this->getFileInfo($filePath);
		if(preg_match("/[^:]+: ([^,]+)/", $fileInfo, $matches)) 
			$fileType = $matches[1];

		foreach ($supportedTypes as $validType)
		{
			if (strpos($fileType, $validType) !== false)
				return null;
		}
	
		KalturaLog::info("file $filePath is of unexpected type : {$fileType}");
		return "invalid file type: {$fileType}";
	}

    /**
     * This function is merging and converting XML format to unify json format for the document conversion data
     * @param $arr - array where the keys will be the keys in the output json and the values are path to XML files to convert
     * @param $basePath - base path to all files
     */
	protected static function jsonFormat($arr, $basePath)
	{
		$docMetadata = [];
		foreach ($arr as $k => $v )
		{
			$str = simplexml_load_string(kFileBase::getFileContent($basePath . $v));
			$docMetadata[$k] = json_decode(json_encode($str), true);
		}
		kFile::setFileContent($basePath . self::DOC_METADATA_JSON_NAME, json_encode($docMetadata));
	}
}

