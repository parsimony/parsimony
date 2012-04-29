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
 * to contact@parsimony.mobi so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Parsimony to newer
 * versions in the future. If you wish to customize Parsimony for your
 * needs please refer to http://www.parsimony.mobi for more information.
 *
 *  @authors Julien Gras et Benoît Lorillot
 *  @copyright  Julien Gras et Benoît Lorillot
 *  @version  Release: 1.0
 * @category  Parsimony
 * @package core\classes
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace core\classes;

/**
 *  Upload Class 
 *  Manages upload
 */
class upload {

    /** @var string New File name */
    private $fileName;

    /** @var string directory name where the file is uploaded */
    private $target;

    /** @var integer allowed maximum size  to upload */
    private $maxSize;

    /** @var array allowed Types  */
    private $type = array();

    /**
     * Init the upload
     * @param interger $size
     * @param string $type
     * @param string $path
     */
    public function __construct($size, $type, $path) {
        $this->maxSize = $size;
        switch ($type) {
            case 'image':
                $this->type = array('gif', 'jpeg', 'jpg', 'png');
                break;
            case 'audio':
                $this->type = array('wav', 'mpeg', 'wma', 'ogg', 'mid', 'mp3');
                break;
            case 'video':
                $this->type = array('mpeg', 'mp4', 'wmv', 'avi');
                break;
            default:
                $this->type = array();
                $types = explode('|', strtolower($type));
                foreach ($types AS $type) {
                    array_push($this->type, trim($type));
                }
        }
        $this->target = $path;
        $this->fileName = '';
    }

    /**
     * Check if the file is an image
     * @param string $file
     * @return bool
     */
    public function isItImage($file) {
        if ($img = @GetImageSize($file)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Upload a file
     * @param string $file
     * @return string|false
     */
    public function upload($file) {
        if ($file["error"] == 0) {
            if (!empty($this->target)) {
                if ($file['size'] <= $this->maxSize) {
                    if (empty($this->fileName)) {
                        $this->fileName = $file['name'];
                    }
                    $fichier_info = pathinfo($this->target . $this->fileName);
                    $extension = $fichier_info['extension'];
                    foreach ($this->type AS $type) {
                        if ($extension == $type || $type = 'all') {
                            $upload_ok = 'ok';
                        }
                    }
                    if (!empty($this->fileName)) {
                        $root = $fichier_info['filename'];
                        $nbn = 0;
                        while (file_exists($this->target . $this->fileName)) {
                            $this->fileName = $root . '_' . $nbn . '.' . $extension;
                            $nbn++;
                        }
                        if ($upload_ok == 'ok') {
                            if (!is_dir($this->target))
                                \tools::createDirectory($this->target);
                            if (!move_uploaded_file($file['tmp_name'], $this->target . $this->fileName)) {
                                throw new Exception(t('Error : No file Uploaded', FALSE));
                            } else {
                                return $this->fileName;
                            }
                        } else {
                            throw new Exception(t('Error : The file format is invalid', FALSE));
                        }
                    } else {
                         throw new Exception(t('Error : the filename can\'t be empty', FALSE));
                    }
                } else {
                    throw new Exception(t('The uploaded file exceeds the MAX_FILE_SIZE :', FALSE). $this->maxSize . t('bytes'));
                }
            } else {
                throw new Exception(t('The target folder doesn\'t exist :', FALSE). $this->target);
            }
        } else {
            throw new Exception(t('The uploaded file exceeds the MAX_FILE_SIZE :', FALSE). $this->maxSize . t('bytes'));
        }
    }

}

?>