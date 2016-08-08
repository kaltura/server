<?php
/**
 * @package plugins.webCasting
 * @subpackage errors
 */
class WebCastingErrors extends KalturaErrors
{
    const UI_CONF_NOT_FOUND = "UI_CONF_NOT_FOUND;UICONFID;UIConf passed as argument [@UICONFID@] was not found";
    const UNKNOWN_OS = "UNKNOWN_OS;OS;passed unknown OS argument [@OS@]. Should be windows|osx";
}