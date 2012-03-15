<?php
/**
 * @package Core
 * @subpackage model.enum
 */ 
interface taggedObjectType extends BaseEnum
{
    const ENTRY = 1;
    const CATEGORY = 2;
    const FLAVORASSET = 4;
    const THUMBASSET = 5;
    const UICONF = 6;
    const KUSER=8;
    const PERMISSION=9;
    const PERMISSIONITEM=10;
    const USERROLE=11;
}