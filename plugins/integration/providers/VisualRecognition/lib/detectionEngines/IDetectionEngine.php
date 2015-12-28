<?php


/**
 * Interface for activation strategies for the FingersCrossedHandler.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
interface IDetectionEngine
{
	/*
	 * used to init the calls to the provider.
	 * in case of false response, all other calls to that provider will fail, until another init will be called
	 */
	public function init();
	/*
	 * method that will call 3rd party service and return external token / job ID
	 */
	public function initiateRecognition(array $thumbnailUrls);

	/*
	 * method that gets a token / job ID of previous call to the 3rd party,
	 * and makes the necessary call to return the data if done
	 *
	 * @return array of words or false if job not done
	 */
	public function checkRecognitionStatus(array $jobIds);

	/*
	 * This api provider results are to understand if the entry answers some critiria, hence,
	 * if we can stop after the first hit, no need to run for all the thumbnail of the video,
	 * just until the result is true (from the checkRecognitionStatus or initiateRecognition)
	 */
	public function shouldStopAfterFirstHit();

	/*
	 * If returns true, the caller should call checkRecognitionStatus to get results, otherwise no
	 * need to call it and the results will be returned from initiateRecognition
	 */
	public function asyncCall();
}
