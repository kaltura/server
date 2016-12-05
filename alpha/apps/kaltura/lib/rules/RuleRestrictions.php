<?php
/**
 * @package Core
 * @subpackage model.data
 */
class RuleRestrictions
{
	const COUNTRY_RESTRICTED_CODE = "COUNTRY_RESTRICTED";
	const SITE_RESTRICTED_CODE = "SITE_RESTRICTED";
	const IP_RESTRICTED_CODE = "IP_RESTRICTED";
	const SESSION_RESTRICTED_CODE = "SESSION_RESTRICTED";
	const USER_AGENT_RESTRICTED_CODE = "USER_AGENT_RESTRICTED";
	const SCHEDULED_RESTRICTED_CODE = "SCHEDULED_RESTRICTED";

	const COUNTRY_RESTRICTED = "Un authorized country\nWe're sorry, this content is only available in certain countries.";
	const SITE_RESTRICTED = "Un authorized domain\nWe're sorry, this content is only available on certain domains.";
	const IP_RESTRICTED = "Un authorized IP address\nWe're sorry, this content is only available for certain IP addresses.";
	const SESSION_RESTRICTED = "No KS where KS is required\nWe're sorry, access to this content is restricted.";
	const SCHEDULED_RESTRICTED = "Out of scheduling\nWe're sorry, this content is currently unavailable.";
	const USER_AGENT_RESTRICTED = "User Agent Restricted\nWe're sorry, this content is not available for your device.";

}
