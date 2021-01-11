<?php
class DirectZip
{
    private static $BUFFER_SIZE = 4194304; // 4MiB

    private $currentOffset;
    private $entries;

    public function open($filename)
    {
        set_time_limit(0);
        ini_set('zlib.output_compression', 'Off');
        header('Pragma: no-cache');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        header('Cache-Control: no-store');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; '
            . 'filename="' . rawurlencode($filename) . '"; '
            . 'filename*=UTF-8\'\'' . rawurlencode($filename));
        $this->currentOffset = 0;
        $this->entries = array();
    }

    public function addEmptyDir($dirname)
    {
        if ($this->addFile('php://temp', $dirname.'/') === false) {
            return false;
        }
    }

    public function addFromString($localname, $contents)
    {
        $tmp = tempnam(sys_get_temp_dir(), __CLASS__);

        $pointer = @fopen($tmp, 'wb');
        if ($pointer === false) {
            @unlink($tmp);
            return false;
        }

        fwrite($pointer, $contents);
        $result = $this->addFile($tmp, $localname);

        fclose($pointer);
        @unlink($tmp);

        if ($result === false) {
            return false;
        }
    }

    public function addFile($filename, $localname = null)
    {
        $entry = new DirectZipEntry($localname ?: basename($filename), $this->currentOffset);
        if ($entry->open($filename) === false) {
            return false;
        }

        $this->entries[] = $entry;

        ob_start();

        self::write(0x504B0304, 'N'); // sig entry
        self::writeEntryStat($entry);
        echo $entry->name;

        $this->currentOffset += strlen(ob_get_flush());

        while (!feof($entry->pointer)) {
            $buffer = @fread($entry->pointer, self::$BUFFER_SIZE);
            echo $buffer;
            flush();
            $this->currentOffset += strlen($buffer);
        }

        $entry->close();
    }

    public function close()
    {
        ob_start();

        foreach ($this->entries as $entry) {
            self::write(0x504B0102, 'N'); // sig index
            self::write(0); // os: fat
            self::writeEntryStat($entry);
            self::write(0); // comment len
            self::write(0); // disk # start
            self::write(0); // internal attr
            self::write(0, 'V'); // external attr
            self::write($entry->offset, 'V');
            echo $entry->name;
        }

        $length = strlen(ob_get_flush());

        self::write(0x504B0506, 'N'); // sig end
        self::write(0); // disk number
        self::write(0); // disk # index start
        self::write(count($this->entries)); // disk entries
        self::write(count($this->entries)); // total entries
        self::write($length, 'V');
        self::write($this->currentOffset, 'V');
        self::write(0); // comment len
        flush();
    }

    private static function writeEntryStat($entry) {
        self::write(substr($entry->name, -1) == '/' ? 20 : 10);
        self::write(2048); // flags: unicode filename
        self::write(0); // compression: store
        self::write($entry->mtime, 'V');
        self::write($entry->crc, 'V');
        self::write($entry->size, 'V'); // compressed size
        self::write($entry->size, 'V'); // uncompressed size
        self::write(strlen($entry->name));
        self::write(0); // extra field len
    }

    private static function write($binary, $format = 'v')
    {
        echo pack($format, $binary);
    }
}

class DirectZipEntry
{
    public $offset;
    public $pointer;

    public $name;
    public $crc;
    public $size;
    public $mtime;

    public function __construct($name, $offset)
    {
        $this->offset = $offset;
        $this->name = $name;
    }

    public function open($filename)
    {
        $this->pointer = @fopen($filename, 'rb');
        if ($this->pointer === false) {
            return false;
        }

        list(, $this->crc) = unpack('N', hash_file('crc32b', $filename, true));

        $fstat = fstat($this->pointer);
        $this->size = $fstat['size'];

        $mtime = $filename == 'php://temp' ? time() : $fstat['mtime'];
        $this->mtime = self::toFAT32Timestamp($mtime);
    }

    public function close()
    {
        fclose($this->pointer);
    }

    private static function toFAT32Timestamp($unixTimestamp)
    {
        list($y, $m, $d, $h, $i, $s) = explode('-', @date('Y-m-d-H-i-s', $unixTimestamp));
        return ($y - 1980) << 25 | $m << 21 | $d << 16
            | $h << 11 | $i << 5 | $s >> 1;
    }
}
?>