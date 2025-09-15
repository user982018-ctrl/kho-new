<?php

 //loại bỏ số 0/ +84 
function customPhone($listPhone) {
    $result = [];
   
    foreach ($listPhone as $phone) {
        $firstCharater = mb_substr($phone, 0, 1);
      
        if ( $firstCharater == 0) {
            $newChar = substr_replace($phone, '', 0, 1);
            // $newChar = str_replace('0','',$phone);
        } else if ( $firstCharater == '+') {
            // $newChar = str_replace('+84','',$phone);
            $newChar = substr_replace($phone, '', 0, 3);
        }

        $result[] = $newChar;
    }
    return $result;
}
