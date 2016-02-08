<?php
namespace PhillipsData\Csv;

use SplFileObject;

/**
 * CSV Writer
 */
class Writer extends AbstractModifier
{
    protected $file;

    /**
     * Set the output file
     *
     * @param \SplFileObject $file
     */
    public function output(SplFileObject $file)
    {
        $this->file = $file;
    }

    /**
     * Writes the data as a CSV file
     *
     * @param array|Iterator $data
     */
    public function write($data)
    {
        foreach ($data as $row) {
            $this->writeRow($row);
        }
    }

    /**
     * Writes the array to the file
     *
     * @param array $row
     */
    public function writeRow(array $row)
    {
        #
        # TODO: Filter
        #

        #
        # TODO: Format
        #


        $this->file->fputcsv($row);
    }
}
