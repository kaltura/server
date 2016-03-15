<?php
/**
 * Interface denoting an event is a deferred event that should be raised only at the end of the multirequest
 *
 * @package Core
 * @subpackage events
 */
interface IKalturaMultiDeferredEvent
{

	/**
	 * @param array $partnerCriteriaParams
	 */
	public function setPartnerCriteriaParams(array $partnerCriteriaParams);

}