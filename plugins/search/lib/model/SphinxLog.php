<?php


/**
 * Skeleton subclass for representing a row from the 'sphinx_log' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.sphinxSearch
 * @subpackage model
 */
class SphinxLog extends BaseSphinxLog {

	const INDEXING_QUERY_COMPRESS_THRESHOLD_DEFAULT = 8 * 1024; //800KB

	public function getIsCompressed()	{ return $this->getFromCustomData( "IsCompressed", null, false ); }
	public function setIsCompressed( $v )	{ $this->putInCustomData( "IsCompressed" , $v ); }

	public function setSql($v)
	{
		if ( self::shouldCompressQuery($v) )
		{
			$v = base64_encode(gzcompress($v));
			self::setIsCompressed(true);
		}
		elseif ($this->getIsCompressed())
		{
			self::setIsCompressed(false);
		}
		return parent::setSql($v);
	}

	public function getSql()
	{
		$v = parent::getSql();
		if ( $this->getIsCompressed() )
		{
			$v = gzuncompress(base64_decode($v));
		}
		return $v;
	}

	/**
	 * @param $query
	 * @return bool
	 */
	private function shouldCompressQuery($query)
	{
		return strlen($query) > kConf::get('indexing_query_compress_threshold', 'local', self::INDEXING_QUERY_COMPRESS_THRESHOLD_DEFAULT);
	}

	public function applyDefaultValues()
	{
		parent::applyDefaultValues();

		$this->setDc(kDataCenterMgr::getCurrentDcId());
	}

} // SphinxLog