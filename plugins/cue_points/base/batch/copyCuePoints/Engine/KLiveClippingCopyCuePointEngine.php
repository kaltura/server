<?php

/**
 * Created by IntelliJ IDEA.
 * User: david.winder
 * Date: 5/16/2018
 * Time: 2:18 PM
 */
class KLiveClippingCopyCuePointEngine extends KLiveToVodCopyCuePointEngine
{
    //override set status to HANDLED as LiveToVod engine
    protected static function postProcessCuePoints($copiedCuePointIds) ();
}