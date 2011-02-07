<?php

/**
 * Subclass for representing a row from the 'flavor_params_conversion_profile' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class flavorParamsConversionProfile extends BaseflavorParamsConversionProfile
{
	const READY_BEHAVIOR_IGNORE = -1;
	const READY_BEHAVIOR_INHERIT_FLAVOR_PARAMS = 0;
	const READY_BEHAVIOR_REQUIRED = 1;
	const READY_BEHAVIOR_OPTIONAL = 2;
}
