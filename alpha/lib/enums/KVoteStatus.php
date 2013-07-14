<?php
/**
 * @package Core
 * @subpackage model.enum
 */
interface KVoteStatus extends BaseEnum
{
    const REVOKED = 1;
    
    const VOTED = 2;
}