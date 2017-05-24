<?php

/**
 * @package plugins.captionSearch
 * @subpackage model
 */
class CaptionAssetItemContainer
{
    private $items;
    public $assetId;
    public $language;

    public function __construct()
    {
        $this->items = array();
    }

    public function addItem(CaptionAssetItem $item)
    {
        $indexData = array();
        $indexData['content'] = $item->getContent();
        $indexData['startTime'] = $item->getStartTime();
        $indexData['endTime'] = $item->getEndTime();
        $this->items[] = $indexData;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getAssetId()
    {
        return $this->assetId;
    }

    public function getLanguage()
    {
        return $this->language;
    }
}
