<?php
namespace wapmorgan\FileTypeDetector;

use \Exception;

class ContentStream {
    protected $openedOutside = false;
    protected $fp;
    protected $read = array();

    public function __construct($source) {
        // open regular file
        if (is_string($source) && file_exists($source)) {
            $this->fp = fopen($source, 'rb');
        }
        // open stream
        else if (is_resource($source) && get_resource_type($source) == 'stream') {
            $this->fp = $source;
            $this->openedOutside = true;
            // cache all data if stream is not seekable
            $meta = stream_get_meta_data($source);
            if (!$meta['seekable']) {
                while (!feof($source))
                    $this->read[] = ord(fgetc($source));
            }
        } else {
            throw new Exception('Unknown source: '.var_export($source, true).' ('.gettype($source).')');
        }
    }

    public function checkBytes($offset, $ethalon) {
        if ($offset < 0) {
            $stat = fstat($this->fp);
            $offset = $stat['size'] + $offset;
        }
        if (!is_array($ethalon)) $ethalon = $this->convertToBytes($ethalon);
        foreach ($ethalon as $i => $byte) {
            if (!isset($this->read[$offset+$i])) {
                fseek($this->fp, $offset+$i, SEEK_SET);
                $this->read[$offset+$i] = ord(fgetc($this->fp));
            }
            if ($this->read[$offset+$i] !== $byte)
                return false;
        }
        return true;
    }

    public function convertToBytes($string) {
        $bytes = array();
        $l = strlen($string);
        for ($i = 0; $i < $l; $i++)
            $bytes[$i] = ord($string[$i]);
        return $bytes;
    }

    public function find($offset, array $bytes, $maxDepth = 512, $reverse = false) {
        if ($offset < 0) {
            $stat = fstat($this->fp);
            $offset = $stat['size'] + $offset;
        }
        $i = 0;
        while (abs($i) <= $maxDepth) {
            $i = $reverse ? $i - 1 : $i + 1;

            if (!isset($this->read[$offset+$i])) {
                fseek($this->fp, $offset+$i, SEEK_SET);
                $this->read[$offset+$i] = ord(fgetc($this->fp));
            }

            foreach ($bytes as $j => $byte) {
                if (is_string($byte)) $byte = ord($byte);
                if ($this->read[$offset+$i+$j] != $byte)
                    continue(2);

            }
            return true;
        }
        return false;
    }

    public function __destruct() {
        if (!$this->openedOutside)
            fclose($this->fp);
    }
}
