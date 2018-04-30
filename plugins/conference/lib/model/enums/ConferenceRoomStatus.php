<?php

/**
 * @package plugins.conference
 * @subpackage model.enum
 */
interface ConferenceRoomStatus extends BaseEnum
{
	const CREATED = 1;
	const READY = 2;
	const ENDED = 3;
}