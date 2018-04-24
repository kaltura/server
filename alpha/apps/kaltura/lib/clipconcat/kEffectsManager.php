<?php
/**
 * @package server-infra
 * @subpackage clipconcat
 */

class kEffectsManager
{

	const MILLISECONDS_TO_SECONDS= 1000;

	/**
	 * @param kClipAttributes $singleAttribute
	 * @return string
	 */
	public function getFFMPEGEffects($singleAttribute)
	{
		$effects = " -filter_complex '";
		//$aEffects = $this->addAudioEffects($singleAttribute);
		$vEffects = $this->addVideoEffects($singleAttribute);
		//if (!empty($aEffects) && !empty($vEffects))
		//	return $effects . $vEffects ."'".$effects . $aEffects . "'" ;
		//elseif(!empty($aEffects))
		//	return $effects . $aEffects . "'";
		if(!empty($vEffects))
			return $effects . $vEffects . "'";
		return '';
	}

	/**
	 * @param kClipAttributes $singleAttribute
	 * @return string
	 */
	private function addVideoEffects($singleAttribute)
	{
		$fadeIn = "";
		$fadeOut = "";
		foreach ($singleAttribute->getEffectArray() as $effect)
		{
			switch ($effect->getEffectType()) {
				case kEffectType::VIDEO_FADE_IN:
					$d = min(intval($effect->getValue()) / self::MILLISECONDS_TO_SECONDS, $singleAttribute->getDuration() / self::MILLISECONDS_TO_SECONDS);
					$fadeIn = "fade=t=in:st=0:d=$d";
					break;
				case kEffectType::VIDEO_FADE_OUT:
					$d = min(intval($effect->getValue()) / self::MILLISECONDS_TO_SECONDS, $singleAttribute->getDuration() / self::MILLISECONDS_TO_SECONDS);
					$st = $singleAttribute->getDuration() / self::MILLISECONDS_TO_SECONDS  - intval($effect->getValue()) / self::MILLISECONDS_TO_SECONDS;
					if ($st  > 0)
						$fadeOut = "fade=t=out:st=$st:d=$d";
					break;
				default:
					break;
			}
		}
		if ($fadeIn && $fadeOut)
			return $fadeIn .','.$fadeOut;
		return $fadeIn . $fadeOut;
	}

	/*

	private function addAudioEffects($singleAttribute)
	{
		$aFadeIn = "";
		$aFadeOut = "";
		foreach ($singleAttribute->getEffectArray() as $effect)
		{
			switch ($effect->getEffectType()) {
				case kEffectType::AUDIO_FADE_IN:
					$d = min(intval($effect->getValue()) / self::MILLISECONDS_TO_SECONDS, $singleAttribute->getDuration() / self::MILLISECONDS_TO_SECONDS);
					$aFadeIn = "afade=t=in:ss=0:d=$d";
					break;
				case kEffectType::AUDIO_FADE_OUT:
					$d = min(intval($effect->getValue()) / self::MILLISECONDS_TO_SECONDS, $singleAttribute->getDuration() / self::MILLISECONDS_TO_SECONDS);
					$st = $singleAttribute->getDuration() / self::MILLISECONDS_TO_SECONDS - intval($effect->getValue()) / self::MILLISECONDS_TO_SECONDS;
					if ($st  > 0)
						$aFadeOut = "afade=t=out:st=$st:d=$d";
					break;
				default:
					break;
			}
		}
		if ($aFadeIn && $aFadeOut)
			return $aFadeIn .','.$aFadeOut;
		return $aFadeIn . $aFadeOut;
	}*/



}