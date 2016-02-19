<?php
namespace PhillipsData\Csv\Tests;

use PhillipsData\Csv\Factory;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \PhillipsData\Csv\Writer
 */
class WriterTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieves an instance of the Writer
     *
     * @return \PhillipsData\Csv\Writer
     */
    private function getWriter()
    {
        return Factory::writer(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fixtures'
            . DIRECTORY_SEPARATOR . 'writer.csv',
            ',',
            '"',
            '\\'
        );
    }

    /**
     * Retrieves an instance of the Reader
     *
     * @return \PhillipsData\Csv\Reader
     */
    private function getReader()
    {
        return Factory::reader(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fixtures'
            . DIRECTORY_SEPARATOR . 'writer.csv',
            ',',
            '"',
            '\\',
            false
        );
    }

    /**
     * @covers ::write
     * @covers ::writeRow
     * @covers ::isWritable
     * @covers ::output
     * @covers ::__construct
     * @covers ::__destruct
     * @uses \PhillipsData\Csv\Factory::writer
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     * @uses \PhillipsData\Csv\Reader::input
     * @uses \PhillipsData\Csv\Reader::setHeader
     * @uses \PhillipsData\Csv\Reader::fetch
     * @uses \PhillipsData\Csv\Reader::getAssocIterator
     * @uses \PhillipsData\Csv\Reader::applyFilter
     * @uses \PhillipsData\Csv\Reader::applyFormat
     * @uses \PhillipsData\Csv\Reader::getFilterIterator
     * @uses \PhillipsData\Csv\Reader::getFormatIterator
     * @uses \PhillipsData\Csv\Reader::getIterator
     * @uses \PhillipsData\Csv\Map\MapIterator::__construct
     * @uses \PhillipsData\Csv\Map\MapIterator::current
     */
    public function testWrite()
    {
        $writer = $this->getWriter();
        $reader = $this->getReader();
        $data = $this->getData();

        // Reset the CSV file, ensure it is empty
        $this->reset($writer);
        $total = 0;
        foreach ($reader->fetch() as $item) {
            $total++;
        }
        $this->assertEquals(0, $total);

        // Write all rows
        $writer->write($data);
        foreach ($reader->fetch() as $item) {
            $total++;
        }
        $this->assertGreaterThan(0, $total);
    }

    /**
     * @covers ::write
     * @covers ::output
     * @covers ::__construct
     * @covers ::__destruct
     * @uses \PhillipsData\Csv\Factory::writer
     * @uses \PhillipsData\Csv\Factory::fileObject
     * @expectedException InvalidArgumentException
     */
    public function testWriteException()
    {
        $writer = $this->getWriter();

        // Exception, string is an invalid argument
        $writer->write("some data");
    }

    /**
     * Test writing with formatters and filters
     *
     * @covers ::write
     * @covers ::writeRow
     * @covers ::isWritable
     * @covers ::filter
     * @covers ::format
     * @covers ::output
     * @covers ::__construct
     * @covers ::__destruct
     * @uses \PhillipsData\Csv\Factory::writer
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     * @uses \PhillipsData\Csv\Reader::input
     * @uses \PhillipsData\Csv\Reader::setHeader
     * @uses \PhillipsData\Csv\Reader::fetch
     * @uses \PhillipsData\Csv\Reader::getAssocIterator
     * @uses \PhillipsData\Csv\Reader::applyFilter
     * @uses \PhillipsData\Csv\Reader::applyFormat
     * @uses \PhillipsData\Csv\Reader::getFilterIterator
     * @uses \PhillipsData\Csv\Reader::getFormatIterator
     * @uses \PhillipsData\Csv\Reader::getIterator
     * @uses \PhillipsData\Csv\Map\MapIterator::__construct
     * @uses \PhillipsData\Csv\Map\MapIterator::current
     */
    public function testWriteFilters()
    {
        $writer = $this->getWriter();
        $reader = $this->getReader();
        $data = $this->getData();

        // Reset the CSV file, ensure it is empty
        $this->reset($writer);
        $total = 0;
        foreach ($reader->fetch() as $item) {
            $total++;
        }
        $this->assertEquals(0, $total);

        // Add a formatter for each CSV line written
        $writer->format(function ($line, $key, $iterator) {
            $values = [];
            foreach ($line as $cell) {
                $values[] = $this->format($cell);
            }

            return $values;
        });

        // Add a filter for each CSV line written
        $writer->filter(function ($line, $key, $iterator) {
            // Only write lines whose first column contains numbers
            return (preg_match('/[0-9]+/', $line[0]));
        });

        // Write all rows
        $writer->write($data);

        // The first line should not have been written due to the filter
        $write_data = $data;
        unset($write_data[0]);
        $write_data = array_values($write_data);

        // Ensure only the filtered lines were written, and they were formatted
        foreach ($reader->fetch() as $i => $line) {
            $total++;

            foreach ($line as $j => $cell) {
                // The values should be different since $cell was formatted
                $this->assertNotEquals(
                    $write_data[$i][$j],
                    $cell
                );

                // The values should be identical once formatted
                $this->assertEquals(
                    $this->format($write_data[$i][$j]),
                    $cell
                );
            }
        }

        // The CSV data has 3 lines, but only 2 should have been written
        $this->assertEquals(2, $total);
    }

    /**
     * @covers ::writeRow
     * @covers ::isWritable
     * @covers ::output
     * @covers ::__construct
     * @covers ::__destruct
     * @uses \PhillipsData\Csv\Writer::write
     * @uses \PhillipsData\Csv\Factory::writer
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     * @uses \PhillipsData\Csv\Reader::input
     * @uses \PhillipsData\Csv\Reader::setHeader
     * @uses \PhillipsData\Csv\Reader::fetch
     * @uses \PhillipsData\Csv\Reader::getAssocIterator
     * @uses \PhillipsData\Csv\Reader::applyFilter
     * @uses \PhillipsData\Csv\Reader::applyFormat
     * @uses \PhillipsData\Csv\Reader::getFilterIterator
     * @uses \PhillipsData\Csv\Reader::getFormatIterator
     * @uses \PhillipsData\Csv\Reader::getIterator
     * @uses \PhillipsData\Csv\Map\MapIterator::__construct
     * @uses \PhillipsData\Csv\Map\MapIterator::current
     */
    public function testWriteRow()
    {
        $writer = $this->getWriter();
        $reader = $this->getReader();
        $data = $this->getData();

        // Reset the CSV file, ensure it is empty
        $this->reset($writer);
        $total = 0;
        foreach ($reader->fetch() as $item) {
            $total++;
        }
        $this->assertEquals(0, $total);

        // Write all rows
        $total = 0;
        foreach ($data as $row) {
            $writer->writeRow($row);
            $total++;

            $temp_total = 0;
            foreach ($reader->fetch() as $item) {
                $temp_total++;
            }
            $this->assertEquals($total, $temp_total);
        }
    }

    /**
     * Resets the writer CSV to an empty file
     *
     * @param \PhillipsData\Csv\Writer $writer
     * @return \PhillipsData\Csv\Writer
     */
    private function reset($writer)
    {
        $writer->write([]);

        return $writer;
    }

    /**
     * @return array An array of data for a CSV
     */
    public function getData()
    {
        return [
            ['colA', 'colB'],
            ['A1"', 'B1'],
            ['A2', 'B,2']
        ];
    }

    /**
     * @param string $text Text to format
     * @return string The formatted text
     */
    private function format($text)
    {
        return strtolower($text);
    }
}
