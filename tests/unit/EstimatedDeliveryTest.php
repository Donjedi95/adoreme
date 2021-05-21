<?php

namespace unit;

use App\Service\EstimatedDeliveryService;
use Codeception\Test\Unit;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;

class EstimatedDeliveryTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $loader = require "vendor/autoload.php";
        $loader->addPsr4('App\\', __DIR__ . '/src');
    }

    protected function _after()
    {
    }

    public function setTestShiftNonWorkingDaysData(): array
    {
        return [
            [
                'data' => [
                    'date1' => '2021-05-01',
                    'date2' => '2021-05-31'
                ],
                'expected' => 9
            ],
            [
                'data' => [
                    'date1' => '2021-05-17',
                    'date2' => '2021-05-21'
                ],
                'expected' => 0
            ]
        ];
    }

    /**
     * @dataProvider setTestShiftNonWorkingDaysData
     * @param $data
     * @param $expected
     * @throws Exception
     */
    public function testShiftNonWorkingDays($data, $expected): void
    {
        $estimatedDelivery = new EstimatedDeliveryService('123456');

        $weekendDays = 0;
        $class = new ReflectionClass($estimatedDelivery);
        $method = $class->getMethod('shiftNonWorkingDays');
        $method->setAccessible(true);

        $date1 = new \DateTime($data['date1']);
        $date2 = new \DateTime($data['date2']);

        $method->invokeArgs($estimatedDelivery, [$date1, $date2, &$weekendDays]);

        self::assertEquals($expected, $weekendDays);
    }
}
