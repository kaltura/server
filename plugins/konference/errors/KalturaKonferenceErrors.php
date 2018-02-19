<?php
/**
 * @package plugins.konference
 * @subpackage api.errors
 */

class KalturaKonferenceErrors extends KalturaErrors
{
	const CONFERENCE_ROOMS_UNAVAILABLE = "CONFERENCE_ROOMS_UNAVAILABLE;Unable to allocate confernce room, no resources available";
	const ROOM_NOT_READY = "ROOM_NOT_READY;ENTRY_SERVER_NODE_ID;server node with host name [@ENTRY_SERVER_NODE_ID@] not found";
	const CONFERENCE_ROOMS_UNREACHABLE = "CONFERENCE_ROOMS_UNREACHABLE;Unable to allocate confernce room, servers are unreachable";
}
