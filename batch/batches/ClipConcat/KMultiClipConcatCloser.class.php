<?php
/**
 * Will close multi clip concat jobs that wasn't finished in the configured max time
 * @package Scheduler
 * @subpackage ClipConcat
 */
class KMultiClipConcatCloser extends KClipConcatCloser
{
	/* (non-PHPdoc)
 * @see KBatchBase::getType()
 */
	public static function getType()
	{
		return KalturaBatchJobType::MULTI_CLIP_CONCAT;
	}
}