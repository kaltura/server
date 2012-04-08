<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface KVoteStatus extends BaseEnum
{
    const KVOTE_STATUS_REVOKED = 1;
    
    const KVOTE_STATUS_VOTED = 2;
}