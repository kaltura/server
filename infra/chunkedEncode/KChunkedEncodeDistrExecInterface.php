<?php
 
/*****************************
 * Includes & Globals
 */
//ini_set("memory_limit","512M");

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/****************************
	 * KChunkedEncodeDistrExecInterface
	 */
	interface KChunkedEncodeDistrExecInterface
	{
		/* ---------------------------
		 *
		 */
		 public function AddJob($job);
		 
		/* ---------------------------
		 *
		 */
		 public function SaveJob($job);
		 
		/* ---------------------------
		 *
		 */
		 public function FetchJob($keyIdx);
		 
		/* ---------------------------
		 *
		 */
		public function DeleteJob($keyIdx);
		
		/* ---------------------------
		 * GetActiveSessions
		 *	
		 */
		public function GetActiveSessions();
	}
	/*****************************
	 * End of KChunkedEncodeDistrExecInterface
	 *****************************/

	/****************************
	 * KChunkedEncodeDistrSchedInterface
	 */
	interface  KChunkedEncodeDistrSchedInterface extends KChunkedEncodeDistrExecInterface
	{
		/* ---------------------------
		 *
		 */
		public function FetchNextJob();

		/* ---------------------------
		 *
		 */
		public function RefreshJobs($maxSlots, &$jobs);

		/* ---------------------------
		 *
		 */
		public function ExecuteJob($job);
	}
	/*****************************
	 * End of KChunkedEncodeDistrSchedInterface
	 *****************************/
