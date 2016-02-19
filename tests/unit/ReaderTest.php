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
    private function getReader($headers = false)
    {
        return Factory::reader(
            dirname(__FILE__) . DIRECTORY_SEPARATOR . 'reader.csv',
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
     * @covers \PhillipsData\Csv\AbstractCsv::__construct
     * @covers \PhillipsData\Csv\AbstractCsv::__destruct
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
     * @uses \PhillipsData\Csv\Map\MapIterator::__construct
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     * @uses \PhillipsData\Csv\AbstractCsv::__construct
     * @uses \PhillipsData\Csv\AbstractCsv::__destruct
     * @uses \PhillipsData\Csv\AbstractCsv::getIterator
     * @uses \PhillipsData\Csv\AbstractCsv::filter
     * @uses \PhillipsData\Csv\AbstractCsv::format
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
     * @covers ::seek
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     *
    public function testSeek()
    {
        $this->assertNull($this->getReader()->seek(0));
    }
     *
     */

    /**
     * @covers ::current
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     *
    public function testCurrent()
    {
        $this->assertInternalType('array', $this->getReader()->current());
    }
     *
     */

    /**
     * @covers ::key
     * @covers ::next
     * @covers ::rewind
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     *
    public function testKey()
    {
        $reader = $this->getReader();

        $this->assertEquals(0, $reader->key());

        $reader->next();
        $this->assertEquals(1, $reader->key());

        $reader->next();
        $this->assertEquals(2, $reader->key());

        $reader->rewind();
        $this->assertEquals(0, $reader->key());
    }
     *
     */

    /**
     * @covers ::next
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     *
    public function testNext()
    {
        $this->assertNull($this->getReader()->next());
    }
     *
     */

    /**
     * @covers ::rewind
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     *
    public function testRewind()
    {
        $this->assertNull($this->getReader()->rewind());
    }
     *
     */

    /**
     * @covers ::valid
     * @covers ::seek
     * @covers ::next
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     *
    public function testValid()
    {
        $reader = $this->getReader();

        $this->assertTrue($reader->valid());

        // CSV file has at least 1 line
        $reader->next();
        $this->assertTrue($reader->valid());

        // CSV file does not have 1000 lines
        $reader->seek(1000);
        $this->assertFalse($reader->valid());
    }
     *
     */

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
     * @uses \PhillipsData\Csv\Map\MapIterator::__construct
     * @uses \PhillipsData\Csv\Map\MapIterator::current
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     * @uses \PhillipsData\Csv\AbstractCsv::__construct
     * @uses \PhillipsData\Csv\AbstractCsv::__destruct
     * @uses \PhillipsData\Csv\AbstractCsv::getIterator
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
        foreach ($reader->fetch() as $i => $line) {
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
     * @uses \PhillipsData\Csv\Map\MapIterator::__construct
     * @uses \PhillipsData\Csv\Map\MapIterator::current
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     * @uses \PhillipsData\Csv\AbstractCsv::__construct
     * @uses \PhillipsData\Csv\AbstractCsv::__destruct
     * @uses \PhillipsData\Csv\AbstractCsv::getIterator
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
     * @param string $text Text to format
     * @return string The formatted text
     */
    private function format($text)
    {
        return strtoupper($text);
    }
}
