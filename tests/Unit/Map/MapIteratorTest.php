<?php
namespace PhillipsData\Csv\Tests\Unit\Map;

use SplFileObject;
use PHPUnit_Framework_TestCase;
use PhillipsData\Csv\Map\MapIterator;

/**
 * @coversDefaultClass \PhillipsData\Csv\Map\MapIterator
 */
class MapIteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return SplFileObject
     */
    private function getIterator()
    {
        $filename = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Fixtures'
            . DIRECTORY_SEPARATOR . 'iterator.csv';
        return new SplFileObject($filename, 'r');
    }

    /**
     * @covers ::__construct
     * @covers ::current
     */
    public function testCurrent()
    {
        $iterator = $this->getIterator();

        $map_iterator = new MapIterator(
            $iterator,
            function ($line, $key, $iterator) {
                return strtoupper($line);
            }
        );

        $this->assertInstanceOf(
            '\PhillipsData\Csv\Map\MapIterator',
            $map_iterator
        );

        // There are three lines in the file (2 CSV lines + 1 blank line)
        $total = 0;
        foreach ($map_iterator as $line) {
            $total++;
        }
        $this->assertEquals(3, $total);
    }
}
