<?php
/**
 * @package plugins.interactivity
 * @subpackage model.items
 */

class kInteractivityDataValidator extends kInteractivityBaseValidator
{
	const NODES = 'nodes';
	const OBJECT_NAME = 'InteractivityData';
	const NODE_ID = 'id';

	/** @var kInteractivityNodeValidator*/
	protected $nodeValidator;

	/**
	 * kEntryInteractivityDataValidator constructor.
	 * @param entry $entry
	 */
	public function __construct($entry)
	{
		parent::__construct($entry);
		$this->setUpNodeValidator($entry);
	}

	public function setUpNodeValidator($entry)
	{
		$this->nodeValidator = new kInteractivityNodeValidator($entry);
	}

	/**
	 * @param array $data
	 * @throws kInteractivityException
	 */
	public function validate($data)
	{
		if(!$data)
		{
			throw new kInteractivityException(kInteractivityException::EMPTY_INTERACTIVITY_DATA);
		}

		$this->validateNodes($data);
	}


	/**
	 * @param array $data
	 * @throws kInteractivityException
	 */
	protected function validateNodes($data)
	{
		if(isset($data[self::NODES]))
		{
			$nodesIds = array();
			$interactionsIds = array();
			$this->validateArrayField($data, self::OBJECT_NAME, self::NODES);
			foreach ($data[self::NODES] as $node)
			{
				$this->nodeValidator->validate($node);
				$nodesIds[] = $node[self::NODE_ID];
				$interactionsIds = array_merge($interactionsIds, $this->nodeValidator->getInteractionIds());
			}

			if($this->isThereDuplicateValues($nodesIds))
			{
				throw new kInteractivityException(kInteractivityException::DUPLICATE_NODES_IDS, kInteractivityException::DUPLICATE_NODES_IDS);
			}

			if($this->isThereDuplicateValues($interactionsIds))
			{
				throw new kInteractivityException(kInteractivityException::DUPLICATE_INTERACTIONS_IDS, kInteractivityException::DUPLICATE_INTERACTIONS_IDS);
			}
		}
	}

	protected function isThereDuplicateValues($arrayIds)
	{
		return count($arrayIds) > count(array_unique($arrayIds));
	}
}