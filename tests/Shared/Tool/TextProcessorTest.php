<?php

namespace App\Tests\Shared\Tool;

use PHPUnit\Framework\TestCase;
use App\Shared\Tool\TextProcessor;

class TextProcessorTest extends TestCase
{
    public function testTrimDataWithOneDeepLevel()
    {
        $input = [
            'one' => '  jeden ',
            'two' => [
                'three' => 'trzy  ',
                'four' => '   cztery ',
            ],
        ];
        $expected = [
            'one' => 'jeden',
            'two' => [
                'three' => 'trzy',
                'four' => 'cztery',
            ],
        ];
        $this->assertSame($expected, TextProcessor::trimData($input));
    }

    public function testTrimDataWithTwoDeepLevel()
    {
        $input = [
            'one' => '  jeden ',
            'two' => [
                'three' => [
                    'four' => ' cztery  ',
                    'five' => '   pięć ',
                ],
                'six' => '  sześć ',
            ],
        ];
        $expected = [
            'one' => 'jeden',
            'two' => [
                'three' => [
                    'four' => 'cztery',
                    'five' => 'pięć',
                ],
                'six' => 'sześć',
            ],
        ];
        $this->assertSame($expected, TextProcessor::trimData($input));
    }

    public function testTrimDataWithEmptyArray()
    {
        $this->assertSame([], TextProcessor::trimData([]));
    }

    public function testTrimDataWithArrayWithEmptyArray()
    {
        $this->assertSame([[]], TextProcessor::trimData([[]]));
    }

    public function testTrimDataWithArrayWithNull()
    {
        $this->assertSame([null], TextProcessor::trimData([null]));
    }
}

