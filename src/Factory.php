<?php
namespace PhillipsData\Csv;

use SplFileObject;

/**
 * Factory for generating reader/writer objects
 */
class Factory
{
    /**
     * Creates a CSV Reader
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
        $writer = new Writer();
        $file = static::fileObject($filename, 'w')
            ->setCsvControl($delimiter, $enclosure, $escape);
        $writer->output($file);

        return $writer;
    }

    /**
     * Creates a CSV Writer
     *
     * @param string $filename
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @return \PhillipsData\Csv\Reader
     */
    public static function reader(
        $filename,
        $delimiter = ',',
        $enclosure = '"',
        $escape = '\\'
    ) {
        $reader = new Reader();
        $file = static::fileObject($filename)
            ->setCsvControl($delimiter, $enclosure, $escape);
        $reader->input($file);

        return $reader;
    }

    /**
     * Creates an SplFileObject instance
     *
     * @param string $filename
     * @param string $openMode
     * @return \SplFileObject
     */
    protected static function fileOejct($filename, $openMode = 'r')
    {
        return new SplFileObject($filename, $openMode);
    }
}
