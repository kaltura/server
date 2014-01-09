<?php

abstract class KOperationEngineDocument extends KSingleOutputOperationEngine {

	protected function getPdfInfo($file) {
		$pdfInfoExe = KBatchBase::$taskConfig->params->pdfInfo;
		$output = null;
		$command = $pdfInfoExe . " \"" . realpath($file) . "\" 2>&1";
		KalturaLog::debug("Executing: $command");
		exec($command, $output);
		return $output;
	}
	
 	private function getFileInfo($filePath)
	{
		$returnValue = null;
		$output = null;
		$command = "file '{$filePath}' 2>&1";
		KalturaLog::debug("Executing: $command");
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
	
		KalturaLog::debug("file $filePath is of unexpected type : {$fileType}");
		return "invalid file type: {$fileType}";
	}
}

