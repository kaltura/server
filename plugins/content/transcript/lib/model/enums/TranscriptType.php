<?php
/**
 * @package plugins.transcript
 * @subpackage model.enum
 */ 
interface TranscriptType extends AttachmentType
{
	const SRT = 4;
	const DFXP = 5;
	const JSON = 6;
}
