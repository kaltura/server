<?php

/**
 *
 * @package Core
 * @subpackage model
 *
 */
class PeerUtils
{

	/**
	 * Retrieve multiple objects by pkey but will keep the ordered of the requested Ids.
	 *
	 * @param      array $pks List of primary keys
	 * @param      PropelPDO $con the connection to use
	 * @throws     PropelException Any exceptions caught during processing will be
	 *		 rethrown wrapped into a PropelException.
	 */
	public static function retrieveByPKsOrdered($peer, $pks, PropelPDO $con = null)
	{
		$objs = $peer::retrieveByPKs($pks, $con);
		usort($objs, self::buildOrderedPkSorter(array_flip ( $pks )));
		return $objs;
	}

	/**
	 * @param $objectsOrder
	 * @return Closure
	 */
	static function buildOrderedPkSorter($objectsOrder) {
		return function ($a, $b) use ($objectsOrder) {
			return ($objectsOrder[$a->getId()] > $objectsOrder[$b->getId()]) ? 1 : -1;
		};
	}
}