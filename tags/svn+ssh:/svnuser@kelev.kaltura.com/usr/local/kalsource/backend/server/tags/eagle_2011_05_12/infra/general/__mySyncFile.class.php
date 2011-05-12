<?php
/**
 * @package infra
 * @subpackage Storage
 * @deprecated
 */
class mySyncFile
{
	static public function lock ($filename) {
		if (substr(PHP_OS, 0, 3) == 'WIN') // if Windows OS detected just return true;
			return true;
		
		$sem_key = ftok($filename, 'LOCK'); // get semaphore key
		$sem_id = sem_get($sem_key, 1); // get semaphore identifier
		sem_acquire($sem_id); // acquire semaphore lock
		return $sem_id; // return sem_id
	}
                                                                                                                            
	static public function unlock($sem_id) {
		if (substr(PHP_OS, 0, 3) == 'WIN') // if Windows OS detected just return true;
			return true;
			
		sem_release($sem_id);// release semaphore lock
	}
	
	static public function appendData($fileName, $data)	
	{
		ignore_user_abort(true);
		
		self::lock($fileName);
		
		$fh = fopen($fileName, "a");
		fputs ($fh, $data);
		fclose($fh);
		
		self::unlock($fileName);
		
		ignore_user_abort(false);
	}
}

?>
