<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kMoveCategoryEntriesJobData extends kJobData
{
	/**
	 * Source category id
	 * @var int
	 */   	
    private $srcCategoryId;
    
    /**
     * Destination category id
     * @var int
     */
    private $destCategoryId;
    
    /**
     * All entries from all child categories will be moved as well
     * @var bool
     */
    private $moveFromChildren;
    
    /**
     * Entries won't be deleted from the source entry
     * @var bool
     */
    private $copyOnly;
    
    /**
     * Destination fallback ids
     * @var string
     */
    private $destCategoryFullIds;
    
	/**
	 * @return int $srcCategoryId
	 */
	public function getSrcCategoryId()
	{
		return $this->srcCategoryId;
	}

	/**
	 * @return int $destCategoryId
	 */
	public function getDestCategoryId()
	{
		return $this->destCategoryId;
	}

	/**
	 * @return bool $moveFromChildren
	 */
	public function getMoveFromChildren()
	{
		return $this->moveFromChildren;
	}

	/**
	 * @return bool $copyOnly
	 */
	public function getCopyOnly()
	{
		return $this->copyOnly;
	}

	/**
	 * @param int $srcCategoryId
	 */
	public function setSrcCategoryId($srcCategoryId)
	{
		$this->srcCategoryId = $srcCategoryId;
	}

	/**
	 * @param int $destCategoryId
	 */
	public function setDestCategoryId($destCategoryId)
	{
		$this->destCategoryId = $destCategoryId;
	}

	/**
	 * @param bool $moveFromChildren
	 */
	public function setMoveFromChildren($moveFromChildren)
	{
		$this->moveFromChildren = $moveFromChildren;
	}

	/**
	 * @param bool $copyOnly
	 */
	public function setCopyOnly($copyOnly)
	{
		$this->copyOnly = $copyOnly;
	}
	
	/**
	 * @return the $destCategoryFullIds
	 */
	public function getDestCategoryFullIds() {
		return $this->destCategoryFullIds;
	}

	/**
	 * @param string $destCategoryFullIds
	 */
	public function setDestCategoryFullIds($destCategoryFullIds) {
		$this->destCategoryFullIds = $destCategoryFullIds;
	}

}
