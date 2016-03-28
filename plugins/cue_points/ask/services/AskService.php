<?php
/**
 * Allows user to handle askzes
 *
 * @service ask
 * @package plugins.ask
 * @subpackage api.services
 */

class AskService extends KalturaBaseService
{

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

		if(!AskPlugin::isAllowedPartner($this->getPartnerId()))
		{
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, AskPlugin::PLUGIN_NAME);
		}
	}

	/**
	 * Allows to add a ask to an entry
	 *
	 * @action add
	 * @param string $entryId
	 * @param KalturaAsk $ask
	 * @return KalturaAsk
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_USER_ID
	 * @throws KalturaAskErrors::PROVIDED_ENTRY_IS_ALREADY_A_ASK
	 */
	public function addAction( $entryId, KalturaAsk $ask )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		if ( !is_null( AskPlugin::getAskData($dbEntry) ) )
			throw new KalturaAPIException(KalturaAskErrors::PROVIDED_ENTRY_IS_ALREADY_A_ASK, $entryId);

		return $this->validateAndUpdateAskData( $dbEntry, $ask );
	}

	/**
	 * Allows to update a ask
	 *
	 * @action update
	 * @param string $entryId
	 * @param KalturaAsk $ask
	 * @return KalturaAsk
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaErrors::INVALID_USER_ID
	 * @throws KalturaAskErrors::PROVIDED_ENTRY_IS_NOT_A_ASK
	 */
	public function updateAction( $entryId, KalturaAsk $ask )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		$kAsk = AskPlugin::validateAndGetAsk( $dbEntry );
		return $this->validateAndUpdateAskData( $dbEntry, $ask, $kAsk->getVersion(), $kAsk );
	}

	/**
	 * if user is entitled for this action will update askData on entry
	 * @param entry $dbEntry
	 * @param KalturaAsk $ask
	 * @param int $currentVersion
	 * @param kAsk|null $newAsk
	 * @return KalturaAsk
	 * @throws KalturaAPIException
	 */
	private function validateAndUpdateAskData( entry $dbEntry, KalturaAsk $ask, $currentVersion = 0, kAsk $newAsk = null )
	{
		if ( !kEntitlementUtils::isEntitledForEditEntry($dbEntry) ) {
			KalturaLog::debug('Update ask allowed only with admin KS or entry owner or co-editor');
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID);
		}
		$askData = $ask->toObject($newAsk);
		$askData->setVersion( $currentVersion+1 );
		AskPlugin::setAskData( $dbEntry, $askData );
		$dbEntry->setIsTrimDisabled( true );
		$dbEntry->save();
		$ask->fromObject( $askData );
		return $ask;
	}

	/**
	 * Allows to get a ask
	 *
	 * @action get
	 * @param string $entryId
	 * @return KalturaAsk
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 *
	 */
	public function getAction( $entryId )
	{
		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$kAsk = AskPlugin::getAskData($dbEntry);
		if ( is_null( $kAsk ) )
			throw new KalturaAPIException(KalturaAskErrors::PROVIDED_ENTRY_IS_NOT_A_ASK, $entryId);

		$ask = new KalturaAsk();
		$ask->fromObject( $kAsk );
		return $ask;
	}

	/**
	 * List ask objects by filter and pager
	 *
	 * @action list
	 * @param KalturaAskFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaAskListResponse
	 */
	function listAction(KalturaAskFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaAskFilter;

		if (! $pager)
			$pager = new KalturaFilterPager ();

		return $filter->getListResponse($pager, $this->getResponseProfile());
	}

	/**
	 * creates a pdf from ask object
	 * The Output type defines the file format in which the ask will be generated
	 * Currently only PDF files are supported
	 * @action serve
	 * @param string $entryId
	 * @param KalturaAskOutputType $askOutputType
	 * @return file
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaAskErrors::PROVIDED_ENTRY_IS_NOT_A_ASK
	 */
	public function serveAction($entryId, $askOutputType)
	{
		KalturaLog::debug("Create a PDF Document for entry id [ " .$entryId. " ]");
		$dbEntry = entryPeer::retrieveByPK($entryId);

		//validity check
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		//validity check
		$kAsk = AskPlugin::getAskData($dbEntry);
		if ( is_null( $kAsk ) )
			throw new KalturaAPIException(KalturaAskErrors::PROVIDED_ENTRY_IS_NOT_A_ASK, $entryId);

		//validity check
		if (!$kAsk->getAllowDownload())
		{
			throw new KalturaAPIException(KalturaAskErrors::ASK_CANNOT_BE_DOWNLOAD);
		}
		//create a pdf
		$kp = new kAskPdf($entryId);
		$kp->createQuestionPdf();
		return $kp->submitDocument();
	}


	/**
	 * sends a with an api request for pdf from ask object
	 *
	 * @action getUrl
	 * @param string $entryId
	 * @param KalturaAskOutputType $askOutputType
	 * @return string
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 * @throws KalturaAskErrors::PROVIDED_ENTRY_IS_NOT_A_ASK
	 * @throws KalturaAskErrors::ASK_CANNOT_BE_DOWNLOAD
	 */
	public function getUrlAction($entryId, $askOutputType)
	{
		KalturaLog::debug("Create a URL PDF Document download for entry id [ " .$entryId. " ]");

		$dbEntry = entryPeer::retrieveByPK($entryId);
		if (!$dbEntry)
			throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $entryId);

		$kAsk = AskPlugin::getAskData($dbEntry);
		if ( is_null( $kAsk ) )
			throw new KalturaAPIException(KalturaAskErrors::PROVIDED_ENTRY_IS_NOT_A_ASK, $entryId);

		//validity check
		if (!$kAsk->getAllowDownload())
		{
			throw new KalturaAPIException(KalturaAskErrors::ASK_CANNOT_BE_DOWNLOAD);
		}

		$finalPath ='/api_v3/service/ask_ask/action/serve/askOutputType/';

		$finalPath .="$askOutputType";
		$finalPath .= '/entryId/';
		$finalPath .="$entryId";
		$ksObj = $this->getKs();
		$ksStr = ($ksObj) ? $ksObj->getOriginalString() : null;
		$finalPath .= "/ks/".$ksStr;

		$partnerId = $this->getPartnerId();
		$downloadUrl = myPartnerUtils::getCdnHost($partnerId) . $finalPath;

		return $downloadUrl;
	}
}
