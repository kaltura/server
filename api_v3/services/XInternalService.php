<?php

/**
 * Internal Service is used for actions that are used internally in Kaltura applications and might be changed in the future without any notice.
 *
 * @service xInternal
 */
class XInternalService extends KalturaBaseService
{
	/**
	 * Creates new download job for multiple entry ids (comma separated), an email will be sent when the job is done
	 * This sevice support the following entries: 
	 * - MediaEntry
	 * 	   - Video will be converted using the flavor params id
	 *     - Audio will be downloaded as MP3
	 *     - Image will be downloaded as Jpeg
	 * - MixEntry will be flattend using the flavor params id
	 * - Other entry types are not supported
	 * 
	 * Returns the admin email that the email message will be sent to 
	 * 
	 * @action xAddBulkDownload
	 * @param string $entryIds Comma separated list of entry ids
	 * @param string $flavorParamsId
	 * @return string
	 */
	public function xAddBulkDownloadAction($entryIds, $flavorParamsId = "")
	{
		$flavorParamsDb = null;
		if ($flavorParamsId !== null && $flavorParamsId != "")
		{
			$flavorParamsDb = flavorParamsPeer::retrieveByPK($flavorParamsId);
		
			if (!$flavorParamsDb)
				throw new KalturaAPIException(KalturaErrors::FLAVOR_PARAMS_ID_NOT_FOUND, $flavorParamsId);
		}
		
		kJobsManager::addBulkDownloadJob($this->getPartnerId(), $this->getKuser()->getPuserId(), $entryIds, $flavorParamsId);
		
		return $this->getKuser()->getEmail();
	}
}