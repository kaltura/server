<?php
/**
 * @package plugins.interactivity
 * @subpackage model
 */

class kInteractivityDataFilter extends BaseObject
{
	/**
	 * @var kInteractivityRootFilter
	 */
	protected $rootFilter;

	/**
	 * @var kInteractivityInteractionFilter
	 */
	protected $interactionFilter;

	/**
	 * @var kInteractivityNodeFilter
	 */
	protected $nodeFilter;

	/**
	 * @return kInteractivityRootFilter
	 */
	public function getRootFilter()
	{
		return $this->rootFilter;
	}

	/**
	 * @param kInteractivityRootFilter $rootFilter
	 */
	public function setRootFilter($rootFilter)
	{
		$this->rootFilter = $rootFilter;
	}

	/**
	 * @return kInteractivityInteractionFilter
	 */
	public function getInteractionFilter()
	{
		return $this->interactionFilter;
	}

	/**
	 * @param kInteractivityInteractionFilter $interactionFilter
	 */
	public function setInteractionFilter($interactionFilter)
	{
		$this->interactionFilter = $interactionFilter;
	}

	/**
	 * @return kInteractivityNodeFilter
	 */
	public function getNodeFilter()
	{
		return $this->nodeFilter;
	}

	/**
	 * @param kInteractivityNodeFilter $nodeFilter
	 */
	public function setNodeFilter($nodeFilter)
	{
		$this->nodeFilter = $nodeFilter;
	}

	/**
	 * @param string $data
	 * @return string
	 */
	public function filterData($data)
	{
		$dataJson = json_decode($data, true);
		if($this->rootFilter)
		{
			$dataJson = $this->filterObject($dataJson, $this->rootFilter->getFieldsAsArray());
		}

		if(isset($dataJson[kInteractivityDataFieldsName::NODES]))
		{
			$dataJson[kInteractivityDataFieldsName::NODES] = $this->filterNodes($dataJson[kInteractivityDataFieldsName::NODES]);
		}

		return json_encode($dataJson);
	}

	/**
	 * @param array $nodes
	 * @return array
	 */
	protected function filterNodes($nodes)
	{
		$result = array();
		foreach ($nodes as $node)
		{
			if($this->nodeFilter)
			{
				$filteredNode = $this->filterObject($node, $this->nodeFilter->getFieldsAsArray());
			}
			else
			{
				$filteredNode = $node;
			}

			if(isset($filteredNode[kInteractivityDataFieldsName::INTERACTIONS]))
			{
				$filteredNode[kInteractivityDataFieldsName::INTERACTIONS] = $this->filterInteractions($filteredNode[kInteractivityDataFieldsName::INTERACTIONS]);
			}

			$result[] = $filteredNode;
		}

		return $result;
	}

	/**
	 * @param array $interactions
	 * @return array
	 */
	protected function filterInteractions($interactions)
	{
		$result = array();
		if($this->interactionFilter)
		{
			foreach ($interactions as $interaction)
			{
				$result[] = $this->filterObject($interaction, $this->interactionFilter->getFieldsAsArray());
			}
		}
		else
		{
			$result = $interactions;
		}

		return $result;
	}

	/**
	 * @param array $object
	 * @param array $fieldsToKeep
	 * @return array
	 */
	protected function filterObject($object, $fieldsToKeep)
	{
		$result = array();
		foreach($object as $key => $value)
		{
			if(in_array($key, $fieldsToKeep))
			{
				$result[$key] = $value;
			}
		}

		return $result;
	}
}
