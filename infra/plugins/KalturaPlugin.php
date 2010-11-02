<?php
abstract class KalturaPlugin implements IKalturaPlugin
{
	public function getInstance($intrface)
	{
		if($this instanceof $intrface)
			return $this;
			
		return null;
	}
}