<?php
/**
 * Correção do bug FileReader do WordPress
 * @author      Haarieh <suporte@haarieh.com>
 * @copyright   2010-2010 Haarieh
 * @version     v 0.1 28/05/2010
 * @link        http://haarieh.com
 */ 
 

/* Bugfix WP >= 2.9.x
 * FileReader (wp-includes/streams.php) doesn't exist in WP 2.9.x anymore
 * POMO_FileReader in wp-includes/pomo/streams.php would work, but doesn't provide length attribute.
 * POMO_CachedFileReader may work, but seems to provide unneccessary overhead
 */
class FileReader {
  var $_pos;
  var $_fd;
  var $_length;

  function FileReader($filename) {
    if (file_exists($filename)) {

      $this->_length=filesize($filename);
      $this->_pos = 0;
      $this->_fd = fopen($filename,'rb');
      if (!$this->_fd) {
	$this->error = 3; // Cannot read file, probably permissions
	return false;
      }
    } else {
      $this->error = 2; // File doesn't exist
      return false;
    }
  }

  function read($bytes) {
    if ($bytes) {
      fseek($this->_fd, $this->_pos);

      // PHP 5.1.1 does not read more than 8192 bytes in one fread()
      // the discussions at PHP Bugs suggest it's the intended behaviour
      while ($bytes > 0) {
        $chunk  = fread($this->_fd, $bytes);
        $data  .= $chunk;
        $bytes -= strlen($chunk);
      }
      $this->_pos = ftell($this->_fd);

      return $data;
    } else return '';
  }

  function seekto($pos) {
    fseek($this->_fd, $pos);
    $this->_pos = ftell($this->_fd);
    return $this->_pos;
  }

  function currentpos() {
    return $this->_pos;
  }

  function length() {
    return $this->_length;
  }

  function close() {
    fclose($this->_fd);
  }
} // FileReader


?>
