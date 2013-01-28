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
 * @authors Julien Gras et BenoÃ®t Lorillot
 * @copyright  Julien Gras et BenoÃ®t Lorillot
 * @version  Release: 1.0
 * @category  Parsimony
 * @package core\classes
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 *  Pagination Class 
 *  Manages pagination
 */
class pagination {

    /** @var integer */
    private $nbRow;

    /** @var integer */
    private $itemsPerPage;

    /** @var integer */
    private $currentPage;

    /** @var integer */
    private $nbPages;

    /** @var @static array */
    private static $cache = array();

    /**
     * Build a pagination object
     * @param string $query
     * @param integer $itemsPerPage
     */
    public function __construct($query, $itemsPerPage=10, $args = array()) {
        $this->itemsPerPage = $itemsPerPage;
        if (!isset(self::$cache[$query]) || !empty($args)) {
            $page = PDOconnection::getDB()->prepare(preg_replace('#select (.*) from#', 'SELECT count(*) FROM', strtolower($query)));
            $page->execute($args);
            if ($page) {
                $page = $page->fetch();
                $this->nbRow = $page[0];
                //echo $this->nbRow;
                self::$cache[$query] = $this->nbRow;
            }
            $this->nbPages = ceil($this->nbRow / $itemsPerPage);
        }
        // Current Page
        $this->currentPage = app::$request->getParam('page');
        if (!$this->currentPage)
            $this->currentPage = 1;
    }

    /**
     * Get current page
     * @return integer
     */
    public function getCurrentPage() {
        return $this->currentPage;
    }

    /**
     * Return the view of pagination
     * @return string
     */
    public function __toString() {
        $html = '<div class="pagination">';
        if ($this->currentPage > 1) {
            $html .= '<a href="?page=1" data-page="1"> << </a>';
        }
        if ($this->itemsPerPage > 1) {
            for ($i = 1; $i <= $this->nbPages; $i++) {
                if ($this->currentPage == $i)
                    $actif = ' active';
                else
                    $actif = '';
                $html .= '<a href="?page=' . $i . '" data-page="' . $i . '" class="' . $actif . '">' . $i . '</a> ';
            }
        }

        if ($this->currentPage < $this->nbPages) {
            $html .= '<a href="?page=' . $this->nbPages . '" data-page="' . $this->nbPages . '"> >> </a>';
        }
        $html .= '</div>';
        return $html;
    }

}

?>