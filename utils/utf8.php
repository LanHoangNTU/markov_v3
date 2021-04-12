<?php

function convertToUTF8($text){

    $encoding = mb_detect_encoding($text, mb_detect_order(), false);

    if($encoding == "UTF-8")
    {
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');    
    }


    $out = iconv(mb_detect_encoding($text, mb_detect_order(), false), "UTF-8//IGNORE", $text);


    return $out;
}

?>