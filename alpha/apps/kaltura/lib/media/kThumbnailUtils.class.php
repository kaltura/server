<?php

/**
 * @package infra
 * @subpackage Media
 */
class kThumbnailUtils
{
	/**
	 * Fit the smaller dimension to the required size.
	 * <br><b>Note:</b> Unless the the source and destination apect-ratios are the same,
	 * this will cause the larger dimension to exceed the required size.
	 */
	const SCALE_UNIFORM_SMALLER_DIM = 1;

	/**
	 * Fit the larger dimension to the required size.
	 * <br><b>Note:</b> Unless the the source and destination apect-ratios are the same,
	 * letter-boxing (empty side-bars) will occur as the smaller dimension will not reach the required size. 
	 */
	const SCALE_UNIFORM_LARGER_DIM = 2;

	/**
	 * Fit the source width to the required size.
	 * <br><b>Note:</b> The height will adjust accordingly and may be smaller, equal to or larger than the required size. 
	 */
	const SCALE_UNIFORM_WIDTH = 3;

	/**
	 * Fit the source height to the required size.
	 * <br><b>Note:</b> The width will adjust accordingly and may be smaller, equal to or larger than the required size. 
	 */
	const SCALE_UNIFORM_HEIGHT = 4;

	/**
	 * Go over all thumbnails (with status = ASSET_STATUS_READY) and look for an exact match.
	 * If none is found, look for the one with the nearest aspect ratio.
	 * This would be the one with the smallest distance from the original.
	 *
	 * @param string $entryId Id of entry containing the thumbnails
	 * @param int $requiredWidth Thumbnail's requested width
	 * @param int $requiredHeight Thumbnail's requested height
	 * @return string|null The path to the physical thumbnail file
	 */
	public static function getNearestAspectRatioThumbnailDescriptorByEntryId( $entryId, $requiredWidth, $requiredHeight, $fallbackThumbnailPath )
	{
		$thumbAssets = assetPeer::retrieveReadyThumbnailsByEntryId( $entryId );
		return self::getNearestAspectRatioThumbnailDescriptorFromThumbAssets( $thumbAssets, $requiredWidth, $requiredHeight, $fallbackThumbnailPath );
	}

	/**
	 * Go over all KalturaThumbAsset thumbnails and look for an exact match.
	 * If none is found, look for the one with the nearest aspect ratio (i.e. the one
	 * with the smallest distance from the original). If there are several with the
	 * same delta from original - the one with the largest dimensions will be picked.
	 *
	 * @param array $thumbAssets ThumbAsset objects array
	 * @param int $requiredWidth Thumbnail's requested width
	 * @param int $requiredHeight Thumbnail's requested height
	 * @return kThumbnailDescriptor|null The thumbnail asset with exact/closest 
	 *                                   aspect ratio to the required, or null
	 *                                   if the entry doesn't contain thumbnails.
	 */
	public static function getNearestAspectRatioThumbnailDescriptorFromThumbAssets( $thumbAssets, $requiredWidth, $requiredHeight, $fallbackThumbnailPath = null )
	{
		// Calc aspect ratio + distance from requiredAspectRatio
		$chosenThumbnailDescriptor = null;

		kThumbnailDescriptor::initDimensions( $requiredWidth, $requiredHeight );

		if ( $fallbackThumbnailPath )
		{
			$imageSizeArray = getimagesize( $fallbackThumbnailPath );
			
			$thumbWidth = $imageSizeArray[0];
			$thumbHeight = $imageSizeArray[1];

			$chosenThumbnailDescriptor = kThumbnailDescriptor::fromParams( $thumbWidth, $thumbHeight, $fallbackThumbnailPath, true );
		}

		if ( empty( $thumbAssets ) )
		{
			return $chosenThumbnailDescriptor;
		}

		// Loop all available thumb assets and choose the best match
		foreach ( $thumbAssets as $thumbAsset )
		{
			$descriptor = kThumbnailDescriptor::fromThumbAsset( $thumbAsset );

			if ( ! $chosenThumbnailDescriptor ) // First descriptor
			{
				$chosenThumbnailDescriptor = $descriptor;
			}
			else
			{
				// Compare the last best-match with the current descriptor
				$res = self::compareThumbnailDescriptors( $chosenThumbnailDescriptor, $descriptor );
				
				// Keep the last best-match unless it needs to go down the ranks (in case $res > 0)
				$chosenThumbnailDescriptor = ($res <= 0) ? $chosenThumbnailDescriptor : $descriptor;
			}
		}

		return $chosenThumbnailDescriptor;
	}

	/**
	 * Look for the smallest delta from original aspect ratio.
	 * If the deltas match:
	 *    If the dimensions match - give priority to the default thumbnail (if any).
	 *    Otherwise:
	 *       Give priority to the thumbnail the exactly matches the required dimensions.
	 *            If none exist - prefer the asset with larger dimensions.
	 *  
	 * @param kThumbnailDescriptor $a @see getNearestAspectRatioThumbnailDescriptorFromThumbAssets()
	 * @param kThumbnailDescriptor $b @see getNearestAspectRatioThumbnailDescriptorFromThumbAssets()
	 * @return int 	(-1) = a before b, (+1) = a after b, (0) = don't care (equal)
	 */
	private static function compareThumbnailDescriptors( $a, $b )
	{
		$aDelta = $a->getDeltaFromOrigAspectRatio();
		$bDelta = $b->getDeltaFromOrigAspectRatio();

		// Look for the smaller delta
		if ( $aDelta < $bDelta )
		{
			return -1;
		}
		else if ( $aDelta > $bDelta )
		{
			return 1;
		}
		else // Same delta (i.e. same aspect ratio)
		{
			// Note: Because the aspect ratio is identical, it's enough to check just one dimension (we'll check the width)
			$aWidth = $a->getWidth();
			$bWidth = $b->getWidth();

			if ( $aWidth != $bWidth ) // Dimensions don't match
			{
				$requiredWidth = kThumbnailDescriptor::getRequiredWidth();

				// Give priority to exact-match dimensions
				if ( $aWidth == $requiredWidth )
				{
					return -1;
				}
				else if ( $bWidth == $requiredWidth )
				{
					return 1;
				}
				else // No exact match
				{
					// Give priority to the one the the larger dimensions
					return $bWidth - $aWidth; // < 0 = $a has priority, > 0 = $b has priority, 0 = equal
				}
			}
			else // Same dimensions
			{
				// Boost the priority of the larger asset for the sake of better quality
				// by looking for the asset with the bigger dimensions
				return $b->getIsDefault() - $a->getIsDefault(); // -1 = $a has priority, 1 = $b has priority, 0 = equal
			}
		}
	}

	/**
	 * Perform a full scale of source dimensions according to the required dimensions.
	 *
	 * @param number $srcWidth The current width
	 * @param number $srcHeight The current height
	 * @param number $reqWidth The required width
	 * @param number $reqHeight The required height
	 * @param number $scaleMethod See the various SCALE_UNIFORM_* constants.
	 * @param number $scaledWidth Reference to a variable that will receive the new (scaled) width
	 * @param number $scaledHeight Reference to a variable that will receive the new (scaled) height
	 */
	public static function scaleDimensions( $srcWidth, $srcHeight, $reqWidth, $reqHeight, $scaleMethod, & $scaledWidth, & $scaledHeight )
	{
		// Calc. the width/height factors, rounded to 3 digits precision 
		$scaleFactorW = round( $reqWidth / $srcWidth, 3 );
		$scaleFactorH = round( $reqHeight / $srcHeight, 3 );

		// Note: See the description of each SCALE_UNIFORM_* constnat for details.
		switch ( $scaleMethod )
		{
			case self::SCALE_UNIFORM_SMALLER_DIM:
				$uniformScaleFactor = max( $scaleFactorW, $scaleFactorH );
				break;

			case self::SCALE_UNIFORM_LARGER_DIM:
				$uniformScaleFactor = min( $scaleFactorW, $scaleFactorH );
				break;

			case self::SCALE_UNIFORM_WIDTH:
				$uniformScaleFactor = $scaleFactorW;
				break;

			case self::SCALE_UNIFORM_HEIGHT:
				$uniformScaleFactor = $scaleFactorH;
				break;
		}

		// Scale both dimensions according to the selected factor (rounded to integers)
		$scaledWidth = round( $srcWidth * $uniformScaleFactor );
		$scaledHeight = round( $srcHeight * $uniformScaleFactor );
	}
}
