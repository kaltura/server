<?php
/**
 * @package Admin
 * @subpackage paginator
 */
class Kaltura_FilterPaginatorForMediaRepurposing implements Zend_Paginator_Adapter_Interface
{
	/**
	 * @var int
	 */
	protected $partnerId;

	/**
	 * @var int
	 */
	protected $mrId;

	/**
	 * @var int
	 */
	protected $count;



	public function __construct($partnerId, $mrId)
	{
		$this->partnerId = $partnerId;
		$this->mrId = $mrId;

	}


	public function getItems($offset, $itemCountPerPage) {
		$this->count = 0;
		$mediaRepurposingProfiles = MediaRepurposingUtils::getMrs($this->partnerId);

		if (!$this->mrId) {
			$this->count = count($mediaRepurposingProfiles);
			return $mediaRepurposingProfiles;
		}
		
		foreach ($mediaRepurposingProfiles as $mr)
			if ($mr->name == $this->mrId) {
				$this->count = 1;
				return array($mr);
		}


		return array();
	}

	public function count() {
		return $this->count;
	}



}