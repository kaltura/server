<?php
/**
 * @package plugins.systemPartner
 * @subpackage errors
 */
class SystemPartnerErrors extends KalturaErrors
{
	/**
	 * System Partner Service
	 */

    const UNABLE_TO_FORM_GROUP_ASSOCIATION = "UNABLE_TO_FORM_GROUP_ASSOCIATION;PID,GROUP_TYPE;Partner @PID@ groupType @GROUP_TYPE@ does not allow group associations.";
}