<?php
/**
 * @package plugins.playReady
 * @subpackage model.enum
 */ 
interface PlayReadyPlayEnablerType extends BaseEnum
{
	const UNKNOWN = '786627D8-C2A6-44BE-8F88-08AE255B01A';
	const UNKNOWN_520 = 'B621D91F-EDCC-4035-8D4B-DC71760D43E9'; //less than or equal to 520,000 pixels per frame
	const DTCP = 'D685030B-0F4F-43A6-BBAD-356F1EA0049A';
	const HDCP_MIRACAST = 'A340C256-0941-4D4C-AD1D-0B6735C0CB24';
	const HDCP_WIVU = '1B4542E3-B5CF-4C99-B3BA-829AF46C92F8';
	const HELIX = '002F9772-38A0-43E5-9F79-0F6361DCC62A';	
	const AIRPLAY = '5ABF0F0D-DC29-4B82-9982-FD8E57525BFC';
}