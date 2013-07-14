<?php
/**
 * @package plugins.tagSearch
 * @subpackage model.data
 *
 */
class kIndexTagsByPrivacyContextJobData extends kJobData
{
	/**
	 * @var int
	 */
	protected $changedCategoryId;
	
	/**
	 * @var string
	 */
	protected $deletedPrivacyContexts;
	
	/**
	 * @var string
	 */
	protected $addedPrivacyContexts;
	
	/**
	 * @return string $addedPrivacyContexts
	 */
	public function getAddedPrivacyContexts() {
		return $this->addedPrivacyContexts;
	}

	/**
	 * @param string $addedPrivacyContexts
	 */
	public function setAddedPrivacyContexts($addedPrivacyContexts) {
		$this->addedPrivacyContexts = $addedPrivacyContexts;
	}

	/**
	 * @return string $deletedPrivacyContexts
	 */
	public function getDeletedPrivacyContexts() {
		return $this->deletedPrivacyContexts;
	}

	/**
	 * @param string $deletedPrivacyContexts
	 */
	public function setDeletedPrivacyContexts($deletedPrivacyContexts) {
		$this->deletedPrivacyContexts = $deletedPrivacyContexts;
	}

	/**
	 * @return int $changedCategoryId
	 */
	public function getChangedCategoryId() {
		return $this->changedCategoryId;
	}

	/**
	 * @param int $changedCategoryId
	 */
	public function setChangedCategoryId($changedCategoryId) {
		$this->changedCategoryId = $changedCategoryId;
	}
	
}