<?php
/**
 * @package plugins.adCuePoint
 * @subpackage model.enum
 */
interface AdProtocolType extends BaseEnum
{
	const CUSTOM = 0;
	const VAST = 1;
	const VAST_2_0 = 2;
	const VPAID = 3;
}