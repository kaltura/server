<?php
/***
 * This class provides functions to deal with emails
 */

class kEmails
{

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