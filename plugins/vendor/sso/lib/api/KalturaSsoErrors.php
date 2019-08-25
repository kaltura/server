<?php
/**
 * @package plugins.sso
 * @subpackage errors
 */

class KalturaSsoErrors extends KalturaErrors
{
	const INVALID_SSO_ID = "INVALID_SSO_ID;ID;Invalid sso id - @ID@";
	const DUPLICATE_SSO = "DUPLICATE_SSO;ID;SSO with id [@ID@] already exists in system";
	const SSO_NOT_FOUND = "SSO_NOT_FOUND FOR THE REQUESTED APPLICATION TYPE;PARAM_NAME;\"@PARAM_NAME@\" application type";

}