<?php
/**
 * @package plugins.fairplay
 * @subpackage Admin
 * @abstract
 */
class Form_FairplayProfileConfigureExtend_SubForm extends Form_DrmProfileConfigureExtend_SubForm
{
	public function init()
	{
		$this->addElement('file', 'publicCertificate', array(
			'label'			=> 'Public Certificate:',
			'filters'		=> array(),
		));
	}

	public function getObject($object, $objectType, array $properties, $add_underscore = true, $include_empty_fields = false)
	{
		$object = parent::getObject($object, $objectType, $properties, $add_underscore, $include_empty_fields);

		if($object instanceof Kaltura_Client_Fairplay_Type_FairplayDrmProfile)
		{
			$upload = new Zend_File_Transfer_Adapter_Http();
			$files = $upload->getFileInfo();
			
			if(isset($files['publicCertificate']))
			{
				$file = $files['publicCertificate'];
				if ($file['size'])
				{
					$content = file_get_contents($file['tmp_name']);
					$object->publicCertificate = base64_encode($content);
				}
			}
		}
		return $object;
	}
}