<?php
/**
 * @package api
 * @subpackage objects
 */
abstract class KalturaContext extends KalturaObject
{
    /**
     * Function to validate the context.
     */
    abstract protected function validate ();
}