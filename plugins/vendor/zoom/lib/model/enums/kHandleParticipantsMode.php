<?php
/**
 * @package plugins.vendor
 * @subpackage model.enum
 */
interface kHandleParticipantsMode extends BaseEnum
{
	const ADD_AS_CO_PUBLISHERS = 0;
	const ADD_AS_CO_VIEWERS = 1;
	const IGNORE = 2;
}