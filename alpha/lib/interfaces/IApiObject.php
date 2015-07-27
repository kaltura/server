<?php
/**
 * @package Core
 * @subpackage model.interfaces
 */ 
interface IApiObject
{
    public function fromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null);
}