<?php

/**
 * class: api_admin
 * creation: 2022-11-10 18:25:50
 * author: applh.com
 * license: MIT
 */


class api_admin
{
    //@start_class

    static function test ()
    {
        $now = date("Y-m-d H:i:s");
        return "(test OK)($now)";
    }

    static function read ()
    {
        $message = "";

        os::debug("api_admin::read");
        $table = form::filter_input("var", "table", "geocms");
        $where = form::filter_input("", "where", "");
        $order = form::filter_input("", "order", "id DESC");
        $limit = form::filter_input("", "limit", "");
        $rows = model::read($table, $where, $order, $limit);
        api::json_data("geocms", $rows);
        return $message;
    }

    //@end_class
}

//@end_file
