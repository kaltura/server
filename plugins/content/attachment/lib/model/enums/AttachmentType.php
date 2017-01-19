<?php
/**
 * @package plugins.attachment
 * @subpackage model.enum
 */ 
interface AttachmentType extends BaseEnum
{
	const TEXT = 1;
	const MEDIA = 2;
	const DOCUMENT = 3;
	const JSON = 4;
}
