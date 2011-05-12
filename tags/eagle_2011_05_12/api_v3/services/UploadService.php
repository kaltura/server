<?php

/**
 *
 * @service upload
 * @package api
 * @subpackage services
 */
class UploadService extends KalturaEntryService
{
	/**
	 * 
	 * @action upload
	 * @param file $fileData The file data
	 * @return string Upload token id
	 */
	function uploadAction($fileData)
	{
		$ksUnique = md5($this->getKs()->toSecureString());
		
		$uniqueId = md5($fileData["name"]);
		
		$ext = pathinfo($fileData["name"], PATHINFO_EXTENSION);
		$token = $ksUnique."_".$uniqueId.".".$ext;
		
		$res = myUploadUtils::uploadFileByToken($fileData, $token, "", null, true);
	
		return $res["token"];
	}
	
	/**
	 * 
	 * @action getUploadedFileTokenByFileName
	 * @param string $fileName
	 * @return KalturaUploadResponse
	 */
	function getUploadedFileTokenByFileNameAction($fileName)
	{
		$res = new KalturaUploadResponse();
		$ksUnique = md5($this->getKs()->toSecureString());
		
		$uniqueId = md5($fileName);
		
		$ext = pathinfo($fileName, PATHINFO_EXTENSION);
		$token = $ksUnique."_".$uniqueId.".".$ext;
		
		$entryFullPath = myUploadUtils::getUploadPath($token, "", null , strtolower($ext)); // filesync ok
		if (!file_exists($entryFullPath))
			throw new KalturaAPIException(KalturaErrors::UPLOADED_FILE_NOT_FOUND_BY_TOKEN);
			
		$res->uploadTokenId = $token;
		$res->fileSize = filesize($entryFullPath);
		return $res; 
	}
}