<?php

/**
 * @package     omeka
 * @subpackage  itemnetwork
 */

function in_schema()
{

    $db = get_db();
    $db->query(<<<SQL

    CREATE TABLE IF NOT EXISTS {$db->prefix}item_network_exhibits (

        id                      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        owner_id                INT(10) UNSIGNED NOT NULL,
        added                   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        modified                TIMESTAMP NULL,
        published               TIMESTAMP NULL,
        item_query              TEXT NULL,
        title                   TEXT NULL,
        slug                    VARCHAR(100) NOT NULL,
        public                  TINYINT(1) NOT NULL,

        PRIMARY KEY             (id)

    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

SQL
);
    $db->query(<<<SQL

    CREATE TABLE IF NOT EXISTS {$db->prefix}item_network_records (

        id                      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        owner_id                INT(10) UNSIGNED NOT NULL,
        item_id                 INT(10) UNSIGNED NULL,
        exhibit_id              INT(10) UNSIGNED NULL,
        added                   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        modified                TIMESTAMP NULL,
        slug                    VARCHAR(100) NULL,
        title                   MEDIUMTEXT NULL,
        item_title              MEDIUMTEXT NULL,
        body                    MEDIUMTEXT NULL,
        tags                    TEXT NULL,
        start_date              VARCHAR(100) NULL,
        end_date                VARCHAR(100) NULL,
        after_date              VARCHAR(100) NULL,
        before_date             VARCHAR(100) NULL,

        PRIMARY KEY             (id),

        INDEX                   (added),
        INDEX                   (exhibit_id, item_id),

        FULLTEXT INDEX          (item_title, title, body, slug),
        FULLTEXT INDEX          (tags)

    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

SQL
);

}
