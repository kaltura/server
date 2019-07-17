<?php


class ResourceReservation extends BinaryResourceReservation
{
	/**
	 * will reserve the resource for some time
	 * @param string $resourceId
	 * @return bool - true if reserve and false if could not
	 */
	public function reserve($resourceId)
	{
		if(parent::reserve($resourceId))
		{
			return true;
		}

		$key = $this->getCacheKeyForResource($resourceId);
		$val = $this->cache->get($key);
		if ($val == $this->userToken) //only the one that reserve the resource can override the reserve (time extending)
		{
			return $this->cache->set($key, $this->userToken, $this->ttl);
		}

		return false;
	}

	/**
	 * will return BatchJob objects.
	 * @param string $resourceId
	 * @return bool - true mean the resource is available
	 */
	public function checkAvailable($resourceId)
	{
		if ($this->cache)
		{
			$val = $this->cache->get($this->getCacheKeyForResource($resourceId));
			if (!$val || $val == $this->userToken) //if not in cache or the caller was the one that reserve it
				return true;
		}

		return false;
	}
}
