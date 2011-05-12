<?php

/**
 * Subclass for representing a row from the 'conversion' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class conversion extends Baseconversion
{
	const CONVERSION_STATUS_ERROR = -1;
	const CONVERSION_STATUS_PRECONVERT = 1;
	const CONVERSION_STATUS_COMPLETED = 2;
	
	const MAX_SENSIBLE_RATIO = 2;//2.5;
	
	public function getOutFileOk(  )
	{
		return $this->isFileOk ( $this->getOutFileSize() );
	}
	
	public function getOutFile2Ok(  )
	{
		return $this->isFileOk ( $this->getOutFileSize2() );
	}
	
	private function isFileOk( $file_size )
	{
		
		$is = $this->getInFileSize();
		if ( $file_size )
		{
			return ( $file_size / $is < self::MAX_SENSIBLE_RATIO );	
		}
		return true;
	}
}
