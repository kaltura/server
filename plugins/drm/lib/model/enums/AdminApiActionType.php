<?php
/**
 * @package plugins.drm
 * @subpackage model.enum
 */ 
interface AdminApiActionType extends BaseEnum
{
    const ADD = 'Add';
    const GET = 'Get';
    const REMOVE = 'Remove';
}