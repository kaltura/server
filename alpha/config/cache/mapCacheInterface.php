<?php
interface mapCacheInterface
{
	public function store($key, $mapName, $map, $ttl=0);
	public function load($key, $mapName);
	public function isKeyRequired();
	public function hasMap($key, $mapName);
}
