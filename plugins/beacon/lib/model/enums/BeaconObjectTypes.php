<?php
/**
 * @package plugins.beacon
 * @subpackage model.enum
 */
interface BeaconObjectTypes extends  BaseEnum
{
    const SCHEDULE_RESOURCE_BEACON=1;
    const ENTRY_SERVER_NODE_BEACON=2;
    const SERVER_NODE_BEACON=3;
}