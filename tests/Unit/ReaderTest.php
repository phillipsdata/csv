<?php
namespace PhillipsData\Csv\Tests;

use PhillipsData\Csv\Factory;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \PhillipsData\Csv\Reader
 */
class ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Retrieves an instance of the Reader
     *
     * @return \PhillipsData\Csv\Reader
     */
    private function getReader($headers = false, $missing_headers = false)
    {
        $file_name = 'reader' . ($missing_headers ? '_headers' : '') . '.csv';
        return Factory::reader(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'fixtures'
            . DIRECTORY_SEPARATOR . $file_name,
            ',',
            '"',
            '\\',
            $headers
        );
    }

    /**
     * @dataProvider inputProvider
     * @covers ::input
     * @covers ::setHeader
     * @covers ::__construct
     * @covers ::__destruct
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testInput($headers)
    {
        $this->assertInstanceOf(
            '\PhillipsData\Csv\Reader',
            $this->getReader($headers)
        );
    }

    /**
     * @see ::testInput
     */
    public function inputProvider()
    {
        return array(
            array(false),
            array(true)
        );
    }

    /**
     * @covers ::fetch
     * @covers ::applyFilter
     * @covers ::getAssocIterator
     * @covers ::getFilterIterator
     * @covers ::getFormatIterator
     * @covers ::applyFormat
     * @covers ::input
     * @covers ::filter
     * @covers ::format
     * @covers ::__construct
     * @covers ::__destruct
     * @covers ::getIterator
     * @uses \PhillipsData\Csv\Map\MapIterator::__construct
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testFetch()
    {
        $reader = $this->getReader();
        $this->assertInstanceOf(
            'Iterator',
            $reader->fetch()
        );
    }

    /**
     * @dataProvider inputProvider
     * @covers ::format
     * @covers ::input
     * @covers ::fetch
     * @covers ::applyFilter
     * @covers ::getAssocIterator
     * @covers ::getFilterIterator
     * @covers ::getFormatIterator
     * @covers ::applyFormat
     * @covers ::setHeader
     * @covers ::__construct
     * @covers ::__destruct
     * @covers ::getIterator
     * @uses \PhillipsData\Csv\Map\MapIterator::__construct
     * @uses \PhillipsData\Csv\Map\MapIterator::current
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testFormat($headers)
    {
        $reader = $this->getReader($headers);
        // Determine defaults for a line
        $default_lines = [];
        foreach ($reader as $line) {
            $default_lines[] = $line;
        }

        // Format each CSV line
        $reader->format(function ($line, $key, $iterator) {
            $values = [];
            foreach ($line as $cell) {
                $values[] = $this->format($cell);
            }

            return $values;
        });

        // Check each cell has been formatted
        $total = 0;
        foreach ($reader->fetch() as $i => $line) {
            $total++;

            foreach ($line as $j => $cell) {
                // The values should be different until formatted
                $this->assertNotEquals(
                    $default_lines[$i][$j],
                    $cell
                );

                // The values should be identical once formatted
                $this->assertEquals(
                    $this->format($default_lines[$i][$j]),
                    $cell
                );
            }
        }

        // CSV should have formatted every line
        $this->assertEquals(($headers ? 3 : 4), $total);
    }

    /**
     * @dataProvider inputProvider
     * @covers ::filter
     * @covers ::input
     * @covers ::fetch
     * @covers ::applyFilter
     * @covers ::getAssocIterator
     * @covers ::getFilterIterator
     * @covers ::getFormatIterator
     * @covers ::applyFormat
     * @covers ::setHeader
     * @covers ::__construct
     * @covers ::__destruct
     * @covers ::getIterator
     * @uses \PhillipsData\Csv\Map\MapIterator::__construct
     * @uses \PhillipsData\Csv\Map\MapIterator::current
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testFilter($headers)
    {
        $reader = $this->getReader($headers);
        // Determine defaults for a line
        $default_lines = [];
        foreach ($reader as $line) {
            $default_lines[] = $line;
        }

        $reader->filter(function ($line, $key, $iterator) {
            // Only return values where the second column contains even numbers
            $index = (array_key_exists(1, $line) ? 1 : 'Heading 2');
            return (preg_match('/[02468]+/', $line[$index]));
        });

        // Check that the CSV contains only the matching rows
        $total_lines = 0;
        foreach ($reader->fetch() as $i => $line) {
            $total_lines++;

            // CSV contains 4 rows, the third of which should not match
            $this->assertContains($i, [0, 1, 3]);
        }

        // CSV contains 4 rows, of which 3 should match
        $expected_total = ($headers ? 2 : 3);
        $this->assertEquals($expected_total, $total_lines);

        // CSV contains no matching rows
        $reader = $this->getReader($headers);
        $reader->filter(function ($line, $key, $iterator) {
            return false;
        });

        // CSV contains 0 rows
        $total = 0;
        foreach ($reader->fetch() as $i => $line) {
            $total++;
        }
        $this->assertEquals(0, $total);

        // CSV contains 4 rows
        $reader = $this->getReader($headers);
        $reader->filter(function ($line, $key, $iterator) {
            return true;
        });

        $total = 0;
        foreach ($reader->fetch() as $i => $line) {
            $total++;
        }
        $expected_total = ($headers ? 3 : 4);
        $this->assertEquals($expected_total, $total);
    }

    /**
     * Checks formatting when the CSV has fewer headings than columns
     *
     * @covers ::format
     * @covers ::getAssocIterator
     * @covers ::input
     * @covers ::fetch
     * @covers ::getFilterIterator
     * @covers ::getFormatIterator
     * @covers ::applyFormat
     * @covers ::applyfilter
     * @covers ::setHeader
     * @covers ::__construct
     * @covers ::__destruct
     * @covers ::getIterator
     * @uses \PhillipsData\Csv\Map\MapIterator::__construct
     * @uses \PhillipsData\Csv\Map\MapIterator::current
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testFormatHeaders()
    {
        $reader = $this->getReader(true, true);

        // Determine defaults for a line
        $default_lines = [];
        foreach ($reader as $line) {
            $default_lines[] = $line;
        }

        // Format each CSV line
        $reader->format(function ($line, $key, $iterator) {
            $values = [];
            foreach ($line as $cell) {
                $values[] = $this->format($cell);
            }

            return $values;
        });

        // Add another formatter
        $reader->format(function ($line, $key, $iterator) {
            $values = [];
            foreach ($line as $cell) {
                $values[] = $this->formatHyphens($cell);
            }

            return $values;
        });

        // Check each cell has been formatted
        foreach ($reader->fetch() as $i => $line) {
            foreach ($line as $j => $cell) {
                // The values should be different until formatted
                $this->assertNotEquals(
                    $default_lines[$i][$j],
                    $cell
                );

                $formatted_cell = $this->format($default_lines[$i][$j]);
                $formatted_cell = $this->formatHyphens($formatted_cell);

                // The values should be identical once formatted
                $this->assertEquals(
                    $formatted_cell,
                    $cell
                );
            }
        }
    }

    /**
     * @param string $text Text to format
     * @return string The formatted text
     */
    private function format($text)
    {
        return strtoupper($text);
    }

    /**
     *
     * @param string $text Text to format
     * @return string The formatted text;
     */
    private function formatHyphens($text)
    {
        return '-' . $text . '-';
    }
}
