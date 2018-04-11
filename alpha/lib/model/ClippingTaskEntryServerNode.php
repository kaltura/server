<?php

/**
 * @package Core
 * @subpackage model
 */
class ClippingTaskEntryServerNode extends TaskEntryServerNode
{
    const OM_CLASS = 'ClippingTaskEntryServerNode';

    const CUSTOM_DATA_CLIP_ATTRIBUTES = "clip_attributes";
    const CUSTOM_DATA_CLIPPED_ENTRY_ID = "clipped_entry_id";

    public function getClipAttributes()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CLIP_ATTRIBUTES);
	}

    public function setClipAttributes($v)
	{
		return $this->putInCustomData(self::CUSTOM_DATA_CLIP_ATTRIBUTES, $v);
	}

	public function getClippedEntryId()
	{
        return $this->getFromCustomData(self::CUSTOM_DATA_CLIPPED_ENTRY_ID);
    }

	public function setClippedEntryId($v)
	{
   		return $this->putInCustomData(self::CUSTOM_DATA_CLIPPED_ENTRY_ID, $v);
	}

	public function validateEntryServerNode()
	{
   		return;
	}

    public function preInsert(PropelPDO $con = null)
    {
        $this->setStatus(EntryServerNodeStatus::LIVE_CLIPPING_TASK_CREATED);
        return parent::preInsert($con);
    }
}