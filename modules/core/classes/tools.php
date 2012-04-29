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
 * Tools Class
 * Here are useful methods of Parsimony
 */
class tools {

    /**
     * Convert relative URL to Absolute URL 
     * @static function
     * @param string $txt
     * @param string $basePath
     * @return string
     */
    public static function absolute_url($txt, $basePath) {
	$needles = array('href="', 'src="', 'background="');
	$new_txt = '';
	if (substr($basePath, -1) != '/')
	    $basePath .= '/';
	$new_base_url = $basePath;
	$base_url_parts = parse_url($basePath);
	foreach ($needles as $needle) {
	    while ($pos = strpos($txt, $needle)) {
		$pos += strlen($needle);
		if (substr($txt, $pos, 7) != 'http://' && substr($txt, $pos, 8) != 'https://' && substr($txt, $pos, 6) != 'ftp://' && substr($txt, $pos, 9) != 'mailto://') {
		    if (substr($txt, $pos, 1) == '/')
			$new_base_url = $base_url_parts['scheme'] . '://' . $base_url_parts['host'];

		    $new_txt .= substr($txt, 0, $pos) . $new_base_url;
		} else {
		    $new_txt .= substr($txt, 0, $pos);
		}
		$txt = substr($txt, $pos);
	    }
	    $txt = $new_txt . $txt;
	    $new_txt = '';
	}
	return $txt;
    }

    /**
     * Copy an entire dir to another
     * @static function
     * @param string $dir2copy
     * @param string $dir_paste
     */
    public static function copy_dir($dir2copy, $dir_paste) {
	$dir2copy = './' . $dir2copy;
	$dir_paste = './' . $dir_paste;
	if (is_dir($dir2copy)) {
	    if ($dh = opendir($dir2copy)) {
		while (($file = readdir($dh)) !== false) {
		    if (!is_dir($dir_paste))
			mkdir($dir_paste, 0777
			);
		    if (is_dir($dir2copy . $file) && $file != '..' &&
			    $file != '.')
			self::copy_dir($dir2copy . $file . '/', $dir_paste . $file . '/');
		    elseif ($file != '..' && $file != '.')
			copy($dir2copy . $file, $dir_paste . $file);
		}
		closedir($dh);
	    }
	}
    }

    /**
     * Sanitize a String in order to generate clean URL
     * @static function
     * @param string $url
     * @return string
     */
    public static function sanitizeString($url) {
        $url = mb_strtolower($url, 'UTF-8');
        $url = iconv('UTF-8', 'ASCII//TRANSLIT', $url);
        $url = str_replace(array("'", '"', ';', ',', ':', ' ', '_'), '-', $url);
        $url = preg_replace("/[^a-z0-9-]/", '', $url);
	$url = str_replace('---', '-', $url);
        $url = str_replace('--', '-', $url);
	$url = trim($url, '-');
	return $url;
    }
    
        /**
     * Sanitize a String in order to generate clean Name
     * @static function
     * @param string $name
     * @return string
     */
    public static function sanitizeTechString($name) {
        $name = preg_replace('@[^a-z0-9_]@', '', mb_strtolower($name, 'UTF-8'));
	return $name;
    }

    /**
     * Create Directory event if parent directory doesn't exist
     * @static functionn fai
     * @param string $directory
     * @param integer $mask optional
     * @return bool
     */
    public static function createDirectory($directory, $mask=0755) {
	if (!file_exists($directory))
	    return mkdir($directory, $mask, TRUE);
    }

    /**
     * Replace native file_put_contents function in order to add backup feature
     * @static function
     * @param string $file
     * @param string $content
     * @param bool $backup optional
     * @return bool
     */
    public static function file_put_contents($file, $content, $backup=true) {
	if ($backup && defined('PROFILE')) {
	    $dir_backup = 'profiles/' . PROFILE . '/backup/' . dirname($file) . '/';
	    if (!is_dir($dir_backup))
		self::createDirectory($dir_backup);
	    file_put_contents($dir_backup . basename($file) . '-' . time() . '.bak', $content);
            foreach (glob($dir_backup.'*.bak') as $filename) {
                if ( filemtime($filename) <= time()-60*60*24*4 ) {
                    unlink($filename);
                }
            }
	}
	self::createDirectory(dirname($file));
	if(empty($content)) $content = ' ';
	if (file_put_contents($file, $content)) { 
	    return TRUE;
	} else {
            throw new \Exception(t('Can\'t write this file : '.$file, FALSE));
        }
    }

    /**
     * Remove a dir and all his child dir ...etc
     * @static function
     * @param string $dir
     * @return bool
     */
    public static function rmdir($dir) {
	if (is_dir($dir)) {
	    $objects = scandir($dir);
	    foreach ($objects as $object) {
		if ($object != '.' && $object != '..') {
		    if (filetype($dir . '/' . $object) == 'dir')
			self::rmdir($dir . '/' . $object); else
			unlink($dir . '/' . $object);
		}
	    }
	    reset($objects);
	    return rmdir($dir);
	}
    }

    /**
     * Convert a string to MP3 with google translate
     * @static function
     * @param string $str
     * @param string $directory optional
     * @param string $lang optional
     * @return string
     */
    public static function stringToMP3($str, $directory='cache/mp3/', $lang='fr') {
	if (!is_dir($directory))
	    mkdir($directory, 0755);
	$url = 'http://translate.google.fr/translate_tts?q=' . $str . '&tl=' . $lang;
	$content = urlencode($str);
	$mp3file = $directory . tools::sanitizeString($content) . '.mp3';
	if (!file_exists($mp3file)) {
	    $mp3 = file_get_contents($url);
	    $obj = file_put_contents($mp3file, $mp3);
	}
	return '<div style="display:none"><object type="application/x-shockwave-flash" data="' . BASE_PATH . 'dewplayer.swf" width="200" height="20" id="dewplayer" name="dewplayer"> <param name="wmode" value="transparent" /><param name="movie" value="' . BASE_PATH . 'dewplayer.swf" /> <param name="flashvars" value="mp3=' . BASE_PATH . $mp3file . '&amp;autostart=1&amp;volume=99" /> </object></div>';
    }

    /**
     * Truncate a string
     * @static function
     * @param string $str
     * @param integer $maxCharacter
     * @param string $after optional
     * @return string
     */
    public static function truncate($str, $maxCharacter, $after = '...') {
	if (strlen($str) > $maxCharacter) {
	    $str = substr($str, 0, $maxCharacter);
	    return $str . $after;
	}
	return $str;
    }
    
        /**
     * Serialize an object in a file
     * @return bool
     */
    public static function serialize($filename,$obj) {
       return \tools::file_put_contents($filename. '.' .\app::$config['dev']['serialization'], serialize($obj));
    }
    
        /**
     * Unserialize an object of a file
     * @return bool
     */
    public static function unserialize($filename) {
        return unserialize(file_get_contents($filename. '.' .\app::$config['dev']['serialization']));
    }
    
    /**
     * Reset a new password to an user and send by email
     * @param $userMail mail
     */
    public static function sendMail($to, $from, $replyTo, $subject, $body) {
        include 'lib/phpmailer/class.phpmailer.php';
        $mailer = new \PHPMailer();

        if(\app::$config['mail']['type'] == 'smtp'){
            $mailer->IsSMTP();
            $mailer->Host = \app::$config['mail']['server'];
            $mailer->Port = \app::$config['mail']['port'];
        }elseif ( \app::$config['mail']['type']=='sendmail') {
            $mailer->IsSendmail();
        }elseif ( \app::$config['mail']['type']=='sendmail') {
            $mailer->IsQmail();
        }
	
	$mailer->AddAddress($to);
        $mailer->SetFrom($from);
        $mailer->AddReplyTo($replyTo);
        $mailer->Subject = $subject;
        $mailer->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        $mailer->MsgHTML($body);

        return $mailer->Send();
        
    }

    /* public static function toxml($obj,$writer, $level=0) {
      $writer->setIndent($level);
      if (is_array($obj)) {
      foreach ($obj AS $key => $value) {
      if (!is_array($value) && !is_object($value)) {
      $writer->writeElement($key, urlencode($value));
      }
      self::toxml($value,$writer, $level + 2);
      }
      } elseif (is_object($obj)) {
      $reflect = new \ReflectionClass($obj);
      $props = $reflect->getFields();
      $writer->startElement('block');
      $writer->writeAttribute('class', get_class($obj));
      $writer->writeAttribute('id', $obj->getId());
      foreach ($props as $prop) {
      $methodName = 'get' . ucfirst($prop->getName());
      if ($reflect->hasMethod($methodName)) {
      $object = call_user_func(array($obj, $methodName));
      if (!is_array($object) && !is_object($object) && $prop->getName() != 'id')
      $writer->writeElement($prop->getName(), $object);
      if (is_array($object) || is_object($object)) {
      $writer->startElement($prop->getName());
      }
      self::toxml($object,$writer, $level + 1);
      if (is_array($object) || is_object($object)) {
      $writer->endElement();
      }
      }
      }
      $writer->endElement();
      }
      }

      public static function RecurseXML($xml, $parent="") {
      global $cont;
      $bkls = array();
      foreach ($xml->blocks->block as $key => $value) {
      $class = (string) $value->attributes()->class;
      $block = new $class((string) $value->attributes()->id);
      foreach ((array) $value->configs AS $keyconf => $config) {
      $block->setConfig($keyconf, urldecode((string) $config));
      }
      if ($value->children()) {
      $block->setBlocks(self::RecurseXML($value->children()));
      }
      $bkls[(string) $value->attributes()->id] = $block;
      }
      return $bkls;
      } */
}

?>
