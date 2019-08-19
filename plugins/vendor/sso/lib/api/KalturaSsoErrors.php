<?php
/**
 * @package plugins.sso
 * @subpackage errors
 */

class KalturaSsoErrors extends KalturaErrors
{
	const INVALID_SSO_ID = "INVALID_SSO_ID;ID;Invalid sso id - @ID@";
	const DUPLICATE_SSO = "DUPLICATE_SSO;ID;SSO with id [@ID@] already exists in system";
	const MISSING_MANDATORY_PARAMETER = "MISSING_MANDATORY_PARAMETER;PARAM_NAME;Missing parameter \"@PARAM_NAME@\"";
	CONST CANNOT_UPDATE_PARAMETER = "CANNOT_UPDATE_PARAMETER;PARAM_NAME;\"@PARAM_NAME@\" cannot be updated";
}