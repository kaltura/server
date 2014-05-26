<?php

/**
 * @package plugins.KalturaInternalTools
 * @subpackage admin
 */
class KalturaInternalToolsPluginFlavorParams extends KalturaApplicationPlugin
{

    public function __construct()
    {
        $this->action = 'KalturaInternalToolsPluginFlavorParams';
        $this->label = 'Flavor Params';
        $this->rootLabel = 'Developer';

    }

    /**
     * @return string - absolute file path of the phtml template
     */
    public function getTemplatePath()
    {
        return realpath(dirname(__FILE__));
    }

    public function getRequiredPermissions()
    {
        return array();
    }


    public function doAction(Zend_Controller_Action $action)
    {
        $form = new Form_NewFlavorParam();
        $action->view->form = $form;

        $request = $action->getRequest();

        if ($request->isPost())
        {
            $params = $request->getParams();

            $valid = true;

            $client = Infra_ClientHelper::getClient();

            $fp = new Kaltura_Client_Type_FlavorParams();


            if ($params['partner_id'] <= 0)
            {
                $valid = false;
                $form->getElement('partner_id')->addError('Partner ID cannot be zero');
            }
            else
            {
                $valid = $form->isValid($params);
            }


            if ($valid)
            {
                $fp->name = $params['name'];
                $fp->systemName = $params['name'];
                $fp->isSystemDefault = Kaltura_Client_Enum_NullableBoolean::TRUE_VALUE;
                $fp->description = $params['description'];
                $fp->tags = $params['tags'];
                $fp->partnerId = $params['partner_id'];
                $fp->videoCodec = $params['video_codec'];
                $fp->audioCodec = $params['audio_codec'];
                $fp->format = $params['container_format'];
                $fp->videoBitrate = $params['video_bitrate'];
                $fp->audioBitrate = $params['audio_bitrate'];
                $fp->height = $params['video_height'];
                $fp->width = $params['video_width'];
                $fp->twoPass = $params['two_pass'];
                $fp->conversionEngines = '2,99,3';
                $fp->conversionEnginesExtraParams = strlen($params['extra_params']) > 0 ? $params['extra_params'] :
                    '-flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 100k -maxrate 400k -bufsize 1200k -rc_eq \'blurCplx^(1-qComp)\' -level 30 -async 2 -vsync 1 -threads 4 | -flags +loop+mv4 -cmp 256 -partitions +parti4x4+partp8x8+partb8x8 -trellis 1 -refs 1 -me_range 16 -keyint_min 20 -sc_threshold 40 -i_qfactor 0.71 -bt 100k -maxrate 400k -bufsize 1200k -rc_eq \'blurCplx^(1-qComp)\' -level 30 -async 2 -vsync 1 | -x264encopts qcomp=0.6:qpmin=10:qpmax=50:qpstep=4:frameref=1:bframes=0:threads=auto:level_idc=30:global_header:partitions=i4x4+p8x8+b8x8:trellis=1:me_range=16:keyint_min=20:scenecut=40:ipratio=0.71:ratetol=20:vbv-maxrate=400:vbv-bufsize=1200';
                $fp->isSystemDefault = false;
                try
                {

                    $systemPartnerPlugin = Kaltura_Client_SystemPartner_Plugin::get($client);
                    $filter = new Kaltura_Client_SystemPartner_Type_SystemPartnerFilter();
                    $partner = $systemPartnerPlugin->systemPartner->get($fp->partnerId);

                    $oldKs = $client->getKs();

                    $newKs = $client->generateSession($partner->adminSecret, "", Kaltura_Client_Enum_SessionType::ADMIN, $fp->partnerId, 86400, "");
                    $client->setKs($newKs);

                    //Infra_ClientHelper::impersonate($fp->partnerId);
                    $result = $client->flavorParams->add($fp);

                    $client->setKs($oldKs);

                    $action->view->resultString = 'Flavor named \'' . $result->name . '\' successfully created ID is: ' . $result->id;
                }
                catch (Exception $e)
                {
                    var_dump($e);

                }

            }


        }


    }

    private static function formatThisData($time)
    {
        return strftime("%d/%m %H:%M:%S", $time);
    }
}

