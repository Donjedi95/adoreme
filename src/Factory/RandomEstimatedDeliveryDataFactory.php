<?php

namespace App\Factory;

use App\Datasource\EstimatedDeliveryDataSource;
use App\Utils;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

final class RandomEstimatedDeliveryDataFactory
{
    private const MIN_GENERATION_YEAR = 2020;
    private const MAX_GENERATION_YEAR = 2021;
    public const MIN_DATE_DIFFERENCE = 3;
    public const MAX_DATE_DIFFERENCE = 14;

    private int $numberOfLines;
    private int $numberOfZipCodes;

    /**
     * RandomEstimatedDeliveryDataFactory constructor.
     * @param int $numberOfLines
     * @param int $numberOfZipCodes
     * @throws Exception
     */
    public function __construct(int $numberOfLines, int $numberOfZipCodes)
    {
        if ($numberOfLines < 1) {
            throw new Exception('Number Of Lines can\'t be smaller than 1');
        }

        if ($numberOfZipCodes < 1) {
            throw new Exception('Number Of Zip Codes can\'t be smaller than 1');
        }

        if ($numberOfLines < $numberOfZipCodes) {
            throw new Exception('Number Of Lines can\'t be smaller than the Number Of Zip Codes');
        }

        $this->numberOfLines = $numberOfLines;
        $this->numberOfZipCodes = $numberOfZipCodes;
    }

    /**
     * @throws Exception
     */
    public function generate(): void
    {
        $zipCodes = $this->generateZipCodes();
        $equalSplit = (int)($this->numberOfLines / $this->numberOfZipCodes);
        $remaining = $this->numberOfLines % $this->numberOfZipCodes;

        foreach ($zipCodes as $key => $zipCode) {
            if (array_key_last($zipCodes) === $key) {
                $randomAssignedLines = $equalSplit + $remaining;
            } else {
                $randomAssignedLines = random_int(1, $equalSplit + $remaining);
            }
            $remaining = $remaining + $equalSplit - $randomAssignedLines;

            $this->generateEstimatedDelivery($zipCode, $randomAssignedLines);
        }
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function generateZipCodes(): array
    {
        $zipCodes = [];
        $numberOfZipCodes = $this->numberOfZipCodes;

        while ($numberOfZipCodes > 0) {
            $zipCode = '';
            $zipCodeSize = Utils::MIN_ZIP_CODE_SIZE;
            if (Utils::MIN_ZIP_CODE_SIZE !== Utils::MAX_ZIP_CODE_SIZE) {
                $zipCodeSize = random_int(Utils::MIN_ZIP_CODE_SIZE, Utils::MAX_ZIP_CODE_SIZE);
            }

            while ($zipCodeSize > 0) {
                $zipCode .= random_int(0, 9);
                $zipCodeSize--;
            }

            $zipCodes[] = $zipCode;
            $numberOfZipCodes--;
        }

        return $zipCodes;
    }

    /**
     * @throws Exception
     */
    protected function generateEstimatedDelivery(string $zipCode, int $lines): void
    {
        while ($lines > 0) {
            $deliveredDate = $this->getRandomDeliveredDate();
            $shipmentDate = $this->getRandomShipmentDate($deliveredDate);
            $estimatedDeliveryDataSource = new EstimatedDeliveryDataSource();

            $estimatedDeliveryDataSource->addHistoricalData($zipCode, $shipmentDate, $deliveredDate);
            $lines--;
        }
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    protected function getRandomDeliveredDate(): DateTime
    {
        $dateToday = new DateTime('now', new DateTimeZone(Utils::DEFAULT_DATE_TIME_ZONE));
        while(in_array((int)$dateToday->format('N'), Utils::NON_WORKING_DAYS, true)) {
            $dateToday->sub(new DateInterval('P1D'));
        }

        $todayDay = $dateToday->format('d');
        $todayMonth = $dateToday->format('m');

        $year = self::MIN_GENERATION_YEAR;
        if (self::MAX_GENERATION_YEAR !== self::MIN_GENERATION_YEAR) {
            $year = random_int(self::MIN_GENERATION_YEAR, self::MAX_GENERATION_YEAR);
        }

        if ($year === (int)$dateToday->format('Y')) {
            $randomMonth = random_int(1, $todayMonth);
            if ($randomMonth === (int)$todayMonth) {
                $randomDay = random_int(1, $todayDay);
            } else {
                $randomDay = random_int(1, cal_days_in_month(CAL_GREGORIAN,$randomMonth,$year));
            }
        } else {
            $randomMonth = random_int(1, 12);
            $randomDay = random_int(1, cal_days_in_month(CAL_GREGORIAN,$randomMonth,$year));
        }

        return DateTime::createFromFormat('Y-m-d', $year . '-' . $randomMonth . '-' . $randomDay);
    }

    /**
     * @param DateTime $deliveredDate
     * @return DateTime
     * @throws Exception
     */
    protected function getRandomShipmentDate(DateTime $deliveredDate): DateTime
    {
        $randomSubtraction = random_int(self::MIN_DATE_DIFFERENCE, self::MAX_DATE_DIFFERENCE);
        $shipmentDate = clone($deliveredDate);
        $shipmentDate->sub(new DateInterval('P' . $randomSubtraction .'D'));

        while(in_array((int)$shipmentDate->format('N'), Utils::NON_WORKING_DAYS, true)) {
            if ($randomSubtraction >= ((1 + self::MAX_DATE_DIFFERENCE + self::MIN_DATE_DIFFERENCE) / 2)) {
                $shipmentDate->add(new DateInterval('P1D'));
            } else {
                $shipmentDate->sub(new DateInterval('P1D'));
            }
        }

        return $shipmentDate;
    }
}
