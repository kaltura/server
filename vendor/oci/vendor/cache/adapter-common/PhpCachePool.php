<?php

/*
 * This file is part of php-cache organization.
 *
 * (c) 2015 Aaron Scherer <aequasi@gmail.com>, Tobias Nyholm <tobias.nyholm@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cache\Adapter\Common;

use Cache\TagInterop\TaggableCacheItemPoolInterface;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
interface PhpCachePool extends TaggableCacheItemPoolInterface
{
    /**
     * {@inheritdoc}
     *
     * @return PhpCacheItem
     */
    public function getItem($key);

    /**
     * {@inheritdoc}
     *
     * @return array|\Traversable|PhpCacheItem[]
     */
    public function getItems(array $keys = []);
}
