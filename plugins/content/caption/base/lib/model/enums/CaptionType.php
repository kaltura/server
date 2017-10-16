<?php
/**
 * @package plugins.caption
 * @subpackage model.enum
 */ 
interface CaptionType extends BaseEnum 
{
	const SRT = 1;
	const DFXP = 2;
	const WEBVTT = 3;
	const CAP = 4;
}