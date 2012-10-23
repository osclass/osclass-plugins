<?php
    /*
     *
     * functions.php
     */

    function payment_crypt($string)
    {
        $cypher = MCRYPT_RIJNDAEL_256;
        $mode = MCRYPT_MODE_ECB;
        return base64_encode(mcrypt_encrypt($cypher, PAYMENT_CRYPT_KEY, $string, $mode,
            mcrypt_create_iv(mcrypt_get_iv_size($cypher, $mode), MCRYPT_RAND)
            ));
    }

    function payment_decrypt($string)
    {
        $cypher = MCRYPT_RIJNDAEL_256;
        $mode = MCRYPT_MODE_ECB;
        return str_replace("\0", "", mcrypt_decrypt($cypher, PAYMENT_CRYPT_KEY,  base64_decode($string), $mode,
            mcrypt_create_iv(mcrypt_get_iv_size($cypher, $mode), MCRYPT_RAND)
            ));
    }    

?>
