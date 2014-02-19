<?php

/**
 * Parsimony
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@parsimony-cms.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 * @authors Julien Gras et Benoît Lorillot
 * @copyright Julien Gras et Benoît Lorillot
 * 
 * @category Parsimony
 * @package core\classes
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 * PDOconnection Class 
 * Provides an access for databases building and storing a PHP Data Object.
 */
class PDOconnection extends \PDO {

    /** @var @static $db */
    public static $db = FALSE;

    /**
     * Build a PDO connection
     * @param optional string $dbChange 
     */
    public function __construct($dbChange=FALSE) {
        if (!self::$db || is_array($dbChange)) {
            try {
                if (is_array($dbChange))
                    $db = $dbChange;
                else
                    $db = app::$config['db'];
                parent::__construct('mysql:host=' . $db['host'] . ';port=' . $db['port'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass']);
                self::setAttribute(\PDO::ERRMODE_EXCEPTION, \PDO::FETCH_ASSOC);
                self::exec('SET CHARACTER SET utf8');
                self::$db = $this;
            } catch (Exception $e) {
                throw new Exception(t('DBConnection failed', False));
            }
        }
    }

    public static function getDB() {
        if (!self::$db) {
            new PDOconnection();
            }
        return self::$db;
    }

}

?>