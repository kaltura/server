<?php
/**
 * Enable the plugin to validate an operation on the object
 * @package infra
 * @subpackage Plugins
 */
interface IKalturaObjectValidator extends IKalturaBase
{
    /**
     * Function validates required operation on an object and throws exception if it fails.
     * @param BaseObject $object object to validate.
     * 
     * 
     */
    public static function validateObject (BaseObject $object);
}