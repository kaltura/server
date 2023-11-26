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
	 * @var ChapterNamePolicy
	 */
	private $chapterNamePolicy;

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
	 * @return ChapterNamePolicy
	 */
	public function getChapterNamePolicy()
	{
		return $this->chapterNamePolicy;
	}

	/**
	 * @param ChapterNamePolicy $chapterNamePolicy
	 */
	public function setChapterNamePolicy($chapterNamePolicy)
	{
		$this->chapterNamePolicy = $chapterNamePolicy;
	}
}