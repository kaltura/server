<?php

require_once ( "kalturaAction.class.php" );

class supportAction extends kalturaAction
{
    const SUPPORT_EMAIL_TYPE_ID = 210;
    
    public function execute ( ) 
    {
        $this->sent_request = false;
        if(isset($_POST['partner_id']))
        {
            // do mail
            $mail_body = array();
            foreach($_POST as $key => $val)
            {
                $mail_body[] = $key.': '.$val;
            }
            $strMailBody = implode('<BR><BR>', $mail_body);
            
            $body_params = array($strMailBody);
            $subject_params = array($_POST['subject']);
            kJobsManager::addMailJob(
                    null, 
                    0, 
                    $_POST['partner_id'],
                    self::SUPPORT_EMAIL_TYPE_ID, 
                    kMailJobData::MAIL_PRIORITY_NORMAL,
                    $_POST['email'], 
                    $_POST['your_name'].' ',
                    'partnersupport@kaltura.com',
                    $body_params,
                    $subject_params);
            $this->sent_request = true;
        }
        sfView::SUCCESS;
    }
}