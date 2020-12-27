<?php
/**
 * @package plugins.thumbnail
 * @subpackage model
 */

class kResizeImageEntryAdapter extends kBaseResizeAdapter
{
	const IMAGE_FILE_EXTENSION = 'jpg';

	protected function getEntryThumbFilename()
	{
		$entry = $this->parameters->get(kThumbFactoryFieldName::ENTRY);
		return $entry->getVersion() . '.' . self::IMAGE_FILE_EXTENSION;
	}
}