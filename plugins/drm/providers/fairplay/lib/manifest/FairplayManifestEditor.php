<?php
/**
 * @package plugins.fairplay
 * @subpackage manifest
 *
 */

class FairplayManifestEditor extends BaseManifestEditor
{
	public $entryId;
	
	/* (non-PHPdoc)
 * @see BaseManifestEditor::editManifestHeader()
 */
	public function editManifestHeader ($manifestHeader)
	{
		$manifestHeader .= "\n";
		$manifestHeader .= '#EXT-X-SESSION-KEY:METHOD=SAMPLE-AES,URI="skd://entry-'.$this->entryId .
							'",KEYFORMAT="com.apple.streamingkeydelivery",KEYFORMATVERSIONS="1"';
		return $manifestHeader;
	}
}