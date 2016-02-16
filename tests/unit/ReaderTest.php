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
    private function getReader()
    {
        return Factory::reader(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'reader.csv');
    }

    /**
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testInput()
    {
        $this->assertInstanceOf(
            '\PhillipsData\Csv\Reader',
            $this->getReader()
        );
    }

    /**
     * @covers ::seek
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testSeek()
    {
        $this->assertNull($this->getReader()->seek(0));
    }

    /**
     * @covers ::current
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testCurrent()
    {
        $this->assertInternalType('array', $this->getReader()->current());
    }

    /**
     * @covers ::key
     * @covers ::next
     * @covers ::rewind
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
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

    /**
     * @covers ::next
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testNext()
    {
        $this->assertNull($this->getReader()->next());
    }

    /**
     * @covers ::rewind
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testRewind()
    {
        $this->assertNull($this->getReader()->rewind());
    }

    /**
     * @covers ::valid
     * @covers ::seek
     * @covers ::next
     * @covers ::input
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
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

    /**
     * @covers ::format
     * @covers ::current
     * @covers ::input
     * @covers ::key
     * @covers ::next
     * @covers ::rewind
     * @covers ::valid
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testFormat()
    {
        $reader = $this->getReader();
        // Determine defaults for a line
        $default_lines = [];
        foreach ($reader as $line) {
            $default_lines[] = $line;
        }

        // Format each CSV line
        $reader->format(function ($line, $key) {
            $values = [];
            foreach ($line as $cell) {
                $values[] = $this->format($cell);
            }

            return $values;
        });

        // Check each cell has been formatted
        foreach ($reader as $i => $line) {
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
     * @covers ::accept
     * @covers ::current
     * @covers ::input
     * @covers ::key
     * @covers ::next
     * @covers ::rewind
     * @covers ::valid
     * @uses \PhillipsData\Csv\Factory::reader
     * @uses \PhillipsData\Csv\Factory::fileObject
     */
    public function testFilter()
    {
        $reader = $this->getReader();
        // Determine defaults for a line
        $default_lines = [];
        foreach ($reader as $line) {
            $default_lines[] = $line;
        }

        $reader->accept(function ($line, $key) {
            // Only return values where the second column contains even numbers
            return (preg_match('/[02468]+/', $line[1]));
        });

        // Check that the CSV contains only the matching rows
        $total_lines = 0;
        foreach ($reader as $i => $line) {
            $total_lines++;

            // CSV contains 4 rows, the third of which should not match
            $this->assertContains($i, [0, 1, 3]);
        }

        // CSV contains 4 rows, of which 3 should match
        $this->assertEquals(3, $total_lines);

        // CSV contains no matching rows
        $reader = $this->getReader();
        $reader->accept(function ($line, $key) {
            return false;
        });

        // CSV contains 0 rows
        $total = 0;
        foreach ($reader as $i => $line) {
            $total++;
        }
        $this->assertEquals(0, $total);

        // CSV contains 4 rows
        $reader = $this->getReader();
        $reader->accept(function ($line, $key) {
            return true;
        });

        $total = 0;
        foreach ($reader as $i => $line) {
            $total++;
        }
        $this->assertEquals(4, $total);
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
