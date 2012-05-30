<?php


/**
 * Skeleton subclass for performing query and update operations on the 'batch_job_log' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package Core
 * @subpackage model
 */
class BatchJobLogPeer extends BaseBatchJobLogPeer {
    
    /**
     * Function retreives BatchJobLog object by a specific job ID.
     * @param string $jobId
     * @return BatchJobLog
     */
    public static function retrieveByBatchJobId ($jobId)
    {
        $c = new Criteria();
        $c->addAnd(BatchJobLogPeer::JOB_ID, $jobId, Criteria::EQUAL);
        
        return self::doSelectOne($c);
    }

} // BatchJobLogPeer
