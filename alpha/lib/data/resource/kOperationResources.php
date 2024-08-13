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
	 * @var array<kDimensionsAttributes>
	 */
	private $dimensionsAttributes;

	/**
	 * @return array<kDimensionsAttributes>
	 */
	public function getDimensionsAttributes()
	{
		return $this->dimensionsAttributes;
	}

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

	/**
	 * @param array<kDimensionsAttributes> $dimensionsAttributes
	 */
	public function setDimensionsAttributes($dimensionsAttributes)
	{
		$this->dimensionsAttributes = $dimensionsAttributes;
	}
}
