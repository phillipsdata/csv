<?php
namespace PhillipsData\Csv\Tests\Unit;

use PhillipsData\Csv\Factory;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass \PhillipsData\Csv\Factory
 */
class FactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers ::writer
     * @covers ::fileObject
     * @uses \PhillipsData\Csv\AbstractCsv::__construct
     * @uses \PhillipsData\Csv\AbstractCsv::__destruct
     * @uses \PhillipsData\Csv\Writer::output
     */
    public function testWriter()
    {
        $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Fixtures'
            . DIRECTORY_SEPARATOR . 'writer.csv';

        $this->assertInstanceOf(
            '\PhillipsData\Csv\Writer',
            Factory::writer($file)
        );
    }

    /**
     * @covers ::reader
     * @covers ::fileObject
     * @uses \PhillipsData\Csv\AbstractCsv::__construct
     * @uses \PhillipsData\Csv\AbstractCsv::__destruct
     * @uses \PhillipsData\Csv\Reader::input
     * @uses \PhillipsData\Csv\Reader::setHeader
     */
    public function testReader()
    {
        $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Fixtures'
            . DIRECTORY_SEPARATOR . 'reader.csv';

        $this->assertInstanceOf(
            '\PhillipsData\Csv\Reader',
            Factory::reader($file)
        );
    }
}
