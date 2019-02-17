<?php

/**
 * @package Core
 * @subpackage model.data
 */
class kFilterPager extends kPager
{
	public function calcPageSize()
	{
		return max(min($this->pageSize, baseObjectFilter::getMaxInValues()), 0);
	}

}
