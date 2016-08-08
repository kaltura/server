<?php
/**
 * @service webCasting
 * @package plugins.webCasting
 * @subpackage api.services
 */
class WebCastingService extends KalturaBaseService
{
    /**
     * Returns the constant string monoTheFox
     *
     * @action test
     */
    function testAction()
    {
        $response = new KalturaWebCastingTestResponse();
        $response->info = "momoTheFox";

        return $response;
    }
}


