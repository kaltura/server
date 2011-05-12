<?php
/**
 * @package api
 * @subpackage enum
 */
class KalturaBatchJobErrorTypes extends KalturaEnum
{
	const APP = 0;
	const RUNTIME = 1;
	const HTTP = 2; // codes list could be found in http://en.wikipedia.org/wiki/List_of_HTTP_status_codes
	const CURL = 3; // codes list could be found in http://curl.haxx.se/libcurl/c/libcurl-errors.html
	const KALTURA_API = 4;
	const KALTURA_CLIENT = 5;
}
