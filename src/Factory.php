<?php
namespace PhillipsData\Csv;

use SplFileObject;

/**
 * Factory for generating reader/writer objects
 */
class Factory
{
    /**
     * Creates a CSV Writer
     *
     * @param string $filename
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return \PhillipsData\Csv\Reader
     */
    public static function writer(
        $filename,
        $delimiter = ',',
        $enclosure = '"',
        $escape = '\\'
    ) {
        $file = static::fileObject($filename, 'w');
        $file->setCsvControl($delimiter, $enclosure, $escape);
        $writer = Writer::output($file);

        return $writer;
    }

    /**
     * Creates a CSV Reader
     *
     * @param string $filename
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @param bool $withHeader
     * @return \PhillipsData\Csv\Reader
     */
    public static function reader(
        $filename,
        $delimiter = ',',
        $enclosure = '"',
        $escape = '\\',
        $withHeader = true
    ) {
        $file = static::fileObject($filename);
        $file->setCsvControl($delimiter, $enclosure, $escape);
        $reader = Reader::input($file, $withHeader);

        return $reader;
    }

    /**
     * Creates an SplFileObject instance
     *
     * @param string $filename
     * @param string $openMode
     * @return \SplFileObject
     */
    protected static function fileObject($filename, $openMode = 'r')
    {
        return new SplFileObject($filename, $openMode);
    }
}
