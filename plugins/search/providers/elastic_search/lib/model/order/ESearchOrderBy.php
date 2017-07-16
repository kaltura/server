<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.order
 */
class ESearchOrderBy extends BaseObject
{
    /**
     * @var array
     */
    protected $orderItems;

    /**
     * @return array
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * @param array $orderItems
     */
    public function setOrderItems($orderItems)
    {
        $this->orderItems = $orderItems;
    }

}
