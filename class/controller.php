<?php

/**
 * class: controller
 * creation: 2022-11-10 18:33:35
 * author: applh.com
 * license: MIT
 */


class controller
{
    //@start_class

    static function public()
    {
        return true;
    }

    static function admin()
    {
        $res = false;
        // get api_key
        $api_key = os::filter("var", "api_key");
        // if api_key then check if it is valid
        // debug 
        
        os::debug($api_key);

        if ($api_key) {
            // api_key is base64 encoded
            $api_key = base64_decode($api_key);
            // trim api_key
            $api_key = trim($api_key);

            // api_key is json encoded
            $keys = json_decode($api_key, true);
            // check if $keys is valid
            if (is_array($keys)) {
                $maxtime = $keys["maxtime"] ?? 0;
                $hash = $keys["hash"] ?? "";
                // check if $maxtime is not expired
                if ($maxtime > time()) {
                    $key_private = gaia::kv("api/key");
                    // check if $hash is password_hash($key_private . $maxtime)
                    if (password_verify($key_private . $maxtime, $hash)) {
                        $res = true;
                    }
                }
            }
        }
        return $res;
    }

    //@end_class
}

//@end_file