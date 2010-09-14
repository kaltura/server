<?php
require_once ( "kalturaSalesAction.class.php" );

/** copied from sfActions */
abstract class kalturaSalesActions extends kalturaSalesAction
{
	/**
	 * Dispatches to the action defined by the 'action' parameter of the sfRequest object.
	 *
	 * This method try to execute the executeXXX() method of the current object where XXX is the
	 * defined action name.
	 *
	 * @return string A string containing the view name associated with this action
	 *
	 * @throws sfInitializationException
	 *
	 * @see kalturaAction
	 */
	public function execute()
	{
		// dispatch action
		$actionToRun = 'execute'.ucfirst($this->getActionName());
		if (!is_callable(array($this, $actionToRun)))
		{
			// action not found
			$error = 'kalturaSystemAction initialization failed for module "%s", action "%s". You must create a "%s" method.';
			$error = sprintf($error, $this->getModuleName(), $this->getActionName(), $actionToRun);
			throw new sfInitializationException($error);
		}

		if (sfConfig::get('sf_logging_enabled'))
		{
			$this->getContext()->getLogger()->info('{kalturaSystemAction} call "'.get_class($this).'->'.$actionToRun.'()'.'"');
		}

		// run action
		$ret = $this->$actionToRun();

		return $ret;
	}
}
?>