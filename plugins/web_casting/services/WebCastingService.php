<?php
/**
 * @service webCasting
 * @package plugins.webCasting
 * @subpackage api.services
 */
class WebCastingService extends KalturaBaseService
{
    function testAction()
    {
        $response = new KalturaWebCastingTestResponse();
        $response->info = "momoTheFox";

        return $response;
    }
}


