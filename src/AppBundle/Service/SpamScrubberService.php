<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SpamScrubberService extends Controller
{
    public function spamScrubber($value)
    {
        // List all bad values when sending an email
        $veryBad = array('to:', 'cc:', 'bcc:', 'content-type:', 'mime-version:', 'multipart-mixed:', 'content-transfer-encoding:');

        // If any of these above value are found in the email then return an empty string
        foreach ($veryBad as $v) {
            if (stripos($value, $v) !== false){
                return '';
            }
        }

        // Replace any new line characters with spaces
        $value = str_replace(array('\r', '\n', '%0a', '%0d'), ' ', $value);

        // Return the value
        return trim($value);
    }
}