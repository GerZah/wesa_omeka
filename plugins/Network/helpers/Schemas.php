<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */


function in_schema()
{
    $db = get_db();
    $db->query(<<<SQL

    CREATE TABLE IF NOT EXISTS {$db->prefix}network_exhibits (
        id                      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        owner_id                INT(10) UNSIGNED NOT NULL,
        added                   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        modified                TIMESTAMP NULL,
        published               TIMESTAMP NULL,
        item_query              TEXT NULL,
        title                   TEXT NULL,
        public                  TINYINT(1) NOT NULL,
        graph_structure         TINYINT(1) NOT NULL,
        color_item_types        TINYINT(1) NOT NULL,
        all_items               TINYINT(1) NOT NULL,
        all_relations           TINYINT(1) NOT NULL,
        selected_relations      TEXT NULL,
        item_references         TEXT NULL,

        PRIMARY KEY             (id)

    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

SQL
);
    $db->query(<<<SQL

    CREATE TABLE IF NOT EXISTS {$db->prefix}network_records (

        id                      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        owner_id                INT(10) UNSIGNED NOT NULL,
        item_id                 INT(10) UNSIGNED NULL,
        item_type_id            INT(10) UNSIGNED NULL,
        exhibit_id              INT(10) UNSIGNED NULL,
        added                   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        modified                TIMESTAMP NULL,
        title                   MEDIUMTEXT NULL,
        item_title              MEDIUMTEXT NULL,
        body                    MEDIUMTEXT NULL,
        start_date              VARCHAR(100) NULL,
        end_date                VARCHAR(100) NULL,
        after_date              VARCHAR(100) NULL,
        before_date             VARCHAR(100) NULL,

        PRIMARY KEY             (id),
        INDEX                   (added),
        INDEX                   (exhibit_id, item_id),
        FULLTEXT INDEX          (item_title, title, body)

    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

SQL
);
}
