<?php
interface keyCacheInterface
{
	public function storeKey($key, $ttl=30);
	public function loadKey();
}
