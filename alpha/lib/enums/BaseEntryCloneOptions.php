<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface BaseEntryCloneOptions extends BaseEnum
{
    const USERS = 1;
    const CATEGORIES = 2;
    const CHILD_ENTRIES = 3;
    const ACCESS_CONTROL = 4;
}
