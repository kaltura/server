<?php
/**
 * @package plugins.captionSphinx
 * @subpackage DB
 */
class SphinxCaptionAssetItemCriteria extends SphinxCriteria 
{
	/* (non-PHPdoc)
	 * @see SphinxCriteria::getIndexObjectName()
	 */
	public function getIndexObjectName() {
		return "CaptionAssetItemIndex";
	}
}