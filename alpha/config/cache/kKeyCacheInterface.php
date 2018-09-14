<?php
interface kKeyCacheInterface
{
	public function storeKey($key, $ttl=30);
	public function loadKey();
}
