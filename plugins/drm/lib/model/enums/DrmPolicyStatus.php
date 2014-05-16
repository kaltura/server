<?php
/**
 * @package plugins.drm
 * @subpackage model.enum
 */ 
interface DrmPolicyStatus extends BaseEnum
{
	const ACTIVE = 1;
	const DELETED = 2;
}