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

	protected function getFileNameEncryptKey($operator) {
		if (strpos($operator->params, 'encryptFileNames') !== false)
		{
			return KBatchBase::$taskConfig->params->encryptSeed . $this->job->entryId;
		}
		return null;
	}

    /**
     * This function is merging and converting XML format to unify json format for the document conversion data
     * @param $arr - array where the keys will be the keys in the output json and the values are path to XML files to convert
     * @param $basePath - base path to all files
     */
	protected static function jsonFormat($arr, $basePath)
	{
		try {
			$docMetadata = [];
			foreach ($arr as $key => $path) {
				$str = simplexml_load_string(kFileBase::getFileContent($basePath . $path));
				$docMetadata[$key] = json_decode(json_encode($str), true);
			}
			kFile::setFileContent($basePath . self::DOC_METADATA_JSON_NAME, json_encode($docMetadata));

		}
		catch (Exception $e)
		{
			KalturaLog::warning("Fail to create json file: " . $e->getMessage());
		}
	}

	protected static function encryptFileName($basePath, $fileName, $key) {
		if (!$key )
		{
			return $fileName;
		}
		try {
			$pathinfo = pathinfo($fileName);
			$encryptName = base64_encode(hash_hmac('sha256', $pathinfo['filename'], $key, true));
			$newName = str_replace('=', '', strtr($encryptName, '+/', '-_')) . '.' . $pathinfo['extension'];
			KalturaLog::info('RENAME: ' . $fileName . ' to ' . $newName);
			kFile::rename($basePath . $fileName, $basePath . $newName);
			return $newName;
		}
		catch (Exception $e)
		{
			KalturaLog::warning("Fail to encryptFileName for file" . $fileName . "] due to: " . $e->getMessage());
		}
	}
}

