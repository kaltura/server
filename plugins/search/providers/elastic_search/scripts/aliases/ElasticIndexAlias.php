<?php
/**
 * @package plugins.elasticSearch
 * @subpackage scripts
 */
class ElasticIndexAlias
{

	protected $indexName;
	protected $aliasName;

	public function __construct($indexName, $aliasName)
	{
		$this->indexName = $indexName;
		$this->aliasName = $aliasName;
	}

	public function getIndexName()
	{
		return $this->indexName;
	}

	public function setIndexName($indexName)
	{
		$this->indexName = $indexName;
	}

	public function getAliasName()
	{
		return $this->aliasName;
	}

	public function setAliasName($aliasName)
	{
		$this->aliasName = $aliasName;
	}

}
