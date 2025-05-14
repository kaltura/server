<?php

/**
 * dblib doesn't support transactions so we need to add a workaround for transactions, last insert ID, and quoting
 *
 * @package    propel.adapter.MSSQL
 */
class MssqlDebugPDO extends DebugPDO
{
	/**
	 * Begin a transaction.
	 *
	 * It is necessary to override the abstract PDO transaction functions here, as
	 * the PDO driver for MSSQL does not support transactions.
	 */
	public function beginTransaction(): bool
	{
		$return = true;
		$opcount = $this->getNestedTransactionCount();
		if ( $opcount === 0 ) {
			$execResult = self::exec('BEGIN TRANSACTION');
			$return = ($execResult !== false);
			$this->isUncommitable = false;
		}
		$this->incrementNestedTransactionCount();
		return $return;
	}
	
	/**
	 * Commit a transaction.
	 *
	 * It is necessary to override the abstract PDO transaction functions here, as
	 * the PDO driver for MSSQL does not support transactions.
	 */
  public function commit(): bool
	{
		$return = true;
		$opcount = $this->getNestedTransactionCount();
		if ($opcount > 0) {
			if ($opcount === 1) {
				if ($this->isUncommitable) {
					throw new PropelException('Cannot commit because a nested transaction was rolled back');
				} else {
					$execResult = self::exec('COMMIT TRANSACTION');
					$return = ($execResult !== false);
				}
			}
			$this->decrementNestedTransactionCount();
		}
		return $return;
	}

	/**
	 * Roll-back a transaction.
	 *
	 * It is necessary to override the abstract PDO transaction functions here, as
	 * the PDO driver for MSSQL does not support transactions.
	 */
	public function rollBack(): bool
	{
		$return = true;
		$opcount = $this->getNestedTransactionCount();
		if ($opcount > 0) {
			if ($opcount === 1) { 
				$execResult = self::exec('ROLLBACK TRANSACTION');
				$return = ($execResult !== false);
			} else {
				$this->isUncommitable = true;
			}
			$this->decrementNestedTransactionCount(); 
		}
		return $return;
	}

	/**
	 * Rollback the whole transaction, even if this is a nested rollback
	 * and reset the nested transaction count to 0.
	 *
	 * It is necessary to override the abstract PDO transaction functions here, as
	 * the PDO driver for MSSQL does not support transactions.
	 */
	public function forceRollBack(): bool
	{
		$return = true;
		$opcount = $this->getNestedTransactionCount();
		if ($opcount > 0) {
			// If we're in a transaction, always roll it back
			// regardless of nesting level.
			$execReturn = self::exec('ROLLBACK TRANSACTION');
			$return = ($execReturn !== false);
			
			// reset nested transaction count to 0 so that we don't
			// try to commit (or rollback) the transaction outside this scope.
			$this->nestedTransactionCount = 0;
		}
		return $return;
	}

	public function lastInsertId($seqname = null): int
	{
		$result = self::query('SELECT SCOPE_IDENTITY()');
		return (int)$result->fetchColumn();
	}
	
	public function quoteIdentifier($text): string
	{
		return '[' . $text . ']';
	}
	
	public function useQuoteIdentifier(): bool
	{
		return true;
	}
}
