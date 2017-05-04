<?php

/**
 * @package plugins.captionSearch
 * @subpackage model
 */
class CaptionAssetItemContainer
{
    private $items = array();
    public $assetId;
    public $language;

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

    public function getLines()
    {
        $lines = array();
        foreach ($this->getItems() as $item)
        {
            $line = $this->getLine($item);
            $lines[] = $line;
        }
        return $lines;
    }

    private function getLine($item)
    {
        $line = array(
            'start_time' => $item['startTime'],
            'end_time' => $item['endTime'],
            'content' => $item['content']//this should be the non analyzed field
        );
        $lang = $this->getLanguage();
        $analyzedFieldName = elasticSearchUtils::getAnalyzedFieldName($lang, 'content');

        if($analyzedFieldName)
            $line[$analyzedFieldName] = $item['content'];
        //general for all lang
        $line['content_trigrams'] = $item['content'];
        return $line;
    }
}
