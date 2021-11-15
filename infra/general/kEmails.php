<?php
/***
 * This class provides functions to deal with emails
 */

class kEmails
{
	const TAG_AUTH_TYPE = '@authType@';
	const TAG_EXISTING_USER = '@existingUser@';
	const TAG_USER_NAME = '@userName@';
	const TAG_CREATOR_USER_NAME = '@creatorUserName@';
	const TAG_PUBLISHER_NAME = '@publisherName@';
	const TAG_LOGIN_EMAIL = '@loginEmail@';
	const TAG_RESET_PASSWORD_LINK = '@resetPasswordLink@';
	const TAG_PARTNER_ID = '@$partnerId@';
	const TAG_PUSER_ID = '@puserId@';
	const TAG_KMC_LINK = '@kmcLink@';
	const TAG_CONTACT_LINK = '@contactLink@';
	const TAG_BEGINNERS_GUID_LINK = '@beginnersGuideLink@';
	const TAG_QUICK_START_GUID_LINK = '@quickStartGuideLink@';
	const TAG_ROLE_NAME = '@roleName@';
	const TAG_ADMIN_CONSOLE_LINK = '@adminConsoleLink@';
	const TAG_QR_CODE_LINK = '@qrCodeLink@';
	
	public static function populateCustomEmailBody($emailBody, $associativeBodyParams)
	{
		$parsedEmailBody = $emailBody;
		foreach ($associativeBodyParams as $fieldTag => $fieldValue)
		{
			$parsedEmailBody = str_replace($fieldTag, $associativeBodyParams[$fieldValue], $parsedEmailBody);
		}
		return $parsedEmailBody;
	}
	
    /***
     * This function splits a string of emails separated by ; or , into an array of emails
     * @param $recipientsString
     * @return array
     */
    public static function createRecipientsList($recipientsString)
    {
        $recipientsArray = array();
        $currentRecipient = "";
        $doubleQuoteMarks = false;
        $charArray = str_split($recipientsString);
        $ignore_next = false;
        foreach($charArray as $singleChar)
        {
            if ($singleChar === ';' || $singleChar === ',')
            {
                if ($doubleQuoteMarks)
                {
                    $currentRecipient .= $singleChar;
                }
                else
                {
                    if (strlen($currentRecipient) > 0)
                    {
                        $recipientsArray[] = $currentRecipient;
                    }
                    $currentRecipient = "";
                    $doubleQuoteMarks = false;
                }
            }
            else
            {
                $currentRecipient .= $singleChar;
                if ($singleChar === '\\')
                {
                    $ignore_next = true;
                }
                else if ($ignore_next)
                {
                    $ignore_next = false;
                }
                else if ($singleChar === '"')
                {
                    $doubleQuoteMarks = !$doubleQuoteMarks;
                }
            }
        }
        if (strlen($currentRecipient) > 0)
        {
            $recipientsArray[] = $currentRecipient;
        }
        return $recipientsArray;
    }
}