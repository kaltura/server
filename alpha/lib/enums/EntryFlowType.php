<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface EntryFlowType extends BaseEnum
{
    const CLIP_CONCAT = 1;
    const TRIM_CONCAT = 2;
    const LIVE_CLIPPING = 3;

}