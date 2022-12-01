<?php

/**
 * class: sample
 * creation: 2022-12-01 15:05:59
 * author: AppLH.com
 * license: MIT
 */


class model
{
    //@start_class
    static $pdo = null;

    static function read ($table, $where="", $order="", $limit="")
    {
        try {
            $pdo = static::db();
            $sql = "SELECT * FROM `$table`";
            if ($where != "") {
                $sql .= " WHERE $where";
            }
            if ($order != "") {
                $sql .= " ORDER BY $order";
            }
            if ($limit != "") {
                $sql .= " LIMIT $limit";
            }
            error_log($sql);
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        }
        catch (PDOException $e) {
            error_log($e->getMessage());
        }
        
        return $rows ?? [];
    }

    static function create ($data, $table="geocms")
    {
        $pdo = static::db();

        $cols = array_keys($data);
        $sql_cols = implode("`, `", $cols);
        $sql_tokens = implode(", :", $cols);

        $sql = 
        <<<sql
        INSERT INTO `$table` 
        (`$sql_cols`) 
        VALUES 
        (:$sql_tokens)
        sql;

        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return $pdo->lastInsertId();
    }

    static function db()
    {
        if (static::$pdo == null) {
            static::$pdo = model::create_geocms();
        }
        return static::$pdo;
    }

    static function create_geocms()
    {
        // path domain
        $path_domain = gaia::kv("path_domain");
        $db_file = gaia::kv("db/sqlite") ?? "geocms.sqlite";
        $path_db = "$path_domain/$db_file";
        $db_dsn = "sqlite:$path_db";

        $db = null;
        if (is_file($path_db)) {
            // create sqlite database if not exists
            $db = new PDO($db_dsn);
            // set charset to utf8
            // $db->exec("set names utf8mb4 COLLATE utf8mb4_general_ci;");
            // $db->exec("set names utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }
        else {
            error_log("database file not found: $path_db ($db_dsn)");

            // create sqlite database if not exists
            $db = new PDO($db_dsn);
            // $db->exec("set names utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // create table geocms if not exists
            // with columns
            // id, 
            // x, y, z, t, 
            // path, filename, extension, code, created, modified, content, size, md5,
            // title, content, media, author_id, cats, tags, description, status,

            $sql =
            <<<sql
            CREATE TABLE IF NOT EXISTS `geocms` (
                `id` INTEGER PRIMARY KEY AUTOINCREMENT,
                `x` INTEGER,
                `y` INTEGER,
                `z` INTEGER,
                `t` INTEGER,
                `path` TEXT,
                `filename` TEXT,
                `extension` TEXT,
                `code` BLOB,
                `created` TEXT,
                `modified` TEXT,
                `size` INTEGER,
                `md5` TEXT,
                `title` TEXT,
                `content` TEXT,
                `media` TEXT,
                `description` TEXT,
                `author_id` INTEGER,
                `cats` TEXT,
                `tags` TEXT,
                `status` TEXT
            )
            sql;

            $db->exec($sql);
        }
        return $db;
    }

    //@end_class
}

//@end_file
