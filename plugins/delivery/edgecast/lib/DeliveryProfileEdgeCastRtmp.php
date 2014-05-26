<?php
class DeliveryProfileEdgeCastRtmp extends DeliveryProfileRtmp
{	
	/**
	 * @param flavorAsset $flavorAsset
	 * @return string
	 */
	protected function doGetFlavorAssetUrl(flavorAsset $flavorAsset)
	{
		$url = parent::doGetFlavorAssetUrl($flavorAsset);
		// move version param to "behind" the flavor asset id
		$flavorAssetId = $flavorAsset->getId();
		$flavorIdStr = '/flavorId/'.$flavorAssetId;
		$url = str_replace($flavorIdStr, '', $url);
		$url = str_replace('serveFlavor', 'serveFlavor'.$flavorIdStr, $url);
		
		return $url;
	}
}
