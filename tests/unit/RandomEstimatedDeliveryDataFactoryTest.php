<?php

use App\Factory\RandomEstimatedDeliveryDataFactory;

class RandomEstimatedDeliveryDataFactoryTest extends \Codeception\Test\Unit
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

    public function setTestGenerateZipCodesData(): array
    {
        return [
            [
                'data' => [
                    'numberOfLines' => 5000,
                    'numberOfZipCodes' => 5
                ],
                'expected' => 5
            ]
        ];
    }

    /**
     * @dataProvider setTestGenerateZipCodesData
     * @param $data
     * @param $expected
     * @throws ReflectionException
     * @throws Exception
     */
    public function testGenerateZipCodes($data, $expected): void
    {
        $estimatedDeliveryDataFactory = new RandomEstimatedDeliveryDataFactory(
            $data['numberOfLines'],
            $data['numberOfZipCodes']
        );

        $class = new ReflectionClass($estimatedDeliveryDataFactory);
        $method = $class->getMethod('generateZipCodes');
        $method->setAccessible(true);

        $result = $method->invokeArgs($estimatedDeliveryDataFactory, []);

        self::assertCount($expected, $result);
    }

    public function setTestDateDifferenceIsBetweenData(): array
    {
        return [
            [
                'data' => [
                    'min' => 3,
                    'max' => 14,
                    'numberOfLines' => 1000
                ],
            ]
        ];
    }

    /**
     * @dataProvider setTestDateDifferenceIsBetweenData
     * @throws ReflectionException
     * @throws Exception
     */
    public function testDateDifferenceIsBetween($data): void
    {
        $estimatedDeliveryDataFactory = new RandomEstimatedDeliveryDataFactory(
            $data['numberOfLines'],
            123
        );

        $class = new ReflectionClass($estimatedDeliveryDataFactory);
        $methodShipment = $class->getMethod('getRandomShipmentDate');
        $methodShipment->setAccessible(true);
        $methodDelivered = $class->getMethod('getRandomDeliveredDate');
        $methodDelivered->setAccessible(true);

        for ($i = 0; $i <= $data['numberOfLines']; $i++) {
            $date1 = $methodDelivered->invokeArgs($estimatedDeliveryDataFactory,[]);
            $date2 = $methodShipment->invokeArgs($estimatedDeliveryDataFactory, [$date1]);

            $interval = $date1->diff($date2)->format('%a');
            self::assertGreaterThanOrEqual($data['min'], $interval);
            self::assertLessThanOrEqual($data['max'], $interval);
        }
    }
}
