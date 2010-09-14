<?php
class DummyKConversionClient extends kConversionClientBase
{
	public static function getArchiveDirAccessor()
	{
		return parent::getArchiveDir();
	}
}