<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kOperationResources extends kContentResource
{
	/**
	 * Array of resources associated with operation resource
	 * @var array<kOperationResource>
	 */
	private $resources;
	/**
	 *
	 * @var ChapterNamingPolicy
	 */
	private $chapterNamingPolicy;

	/**
	 * @return array
	 */
	public function getResources()
	{
		return $this->resources;
	}

	/**
	 * @param array<kOperationResource> $resources
	 */
	public function setResources(array $resources)
	{
		$this->resources = $resources;
	}

	/**
	 * @return ChapterNamingPolicy
	 */
	public function getChapterNamingPolicy()
	{
		return $this->chapterNamingPolicy;
	}

	/**
	 * @param ChapterNamingPolicy $chapterNamingPolicy
	 */
	public function setChapterNamingPolicy($chapterNamingPolicy)
	{
		$this->chapterNamingPolicy = $chapterNamingPolicy;
	}
}