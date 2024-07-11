<?php

namespace App\Helpers;

class OperatorType
{
    public static function getOperatorType($mobile): string
    {
        $mobile = trim($mobile);
        $ntc_regex = "/9[78][456][0-9]{7}/";
        $ncell_regex = "/98[012][0-9]{7}/";
        $sc_regex1 = "/96[12][0-9]{7}/";
        $sc_regex2 = "/988[0-9]{7}/";
        if (preg_match($ntc_regex, $mobile)) {
            return "NTC";
        } else if (preg_match($ncell_regex, $mobile)) {
            return "NCELL";
        } else if (preg_match($sc_regex1, $mobile) || preg_match($sc_regex2, $mobile)) {
            return "SMARTCELL";
        } else {
            return "OTHER";
        }
    }
}
