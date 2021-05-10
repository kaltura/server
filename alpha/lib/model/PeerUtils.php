<?php

/**
 *
 * @package Core
 * @subpackage model
 *
 */
class PeerUtils
{

	const SETTER_GETTER_PREFIX_LEN = 3;

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

	static protected function getName($input)
	{
		return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', substr($input, self::SETTER_GETTER_PREFIX_LEN))), '_') . '_extension';
	}

	static public function getExtension($obj, $getterFuncStr)
	{
		return $obj->getFromCustomData(self::getName($getterFuncStr), null, '');
	}
	static public function setExtension($obj, $v, $maxLengthInDb, $setterFuncStr)
	{
		$ext = substr($v, $maxLengthInDb);
		$obj->putInCustomData(self::getName($setterFuncStr), ($ext === false) ? '' : $ext);
	}
}