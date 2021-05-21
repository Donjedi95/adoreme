<?php

namespace App\Service;

use App\Datasource\EstimatedDeliveryDataSource;
use App\Utils;
use DateInterval;
use DateTime;
use Exception;

class EstimatedDeliveryService
{
    private string $zipcode;
    private ?DateTime $startDate;
    private ?DateTime $endDate;

    /**
     * @throws Exception
     */
    public function __construct(string $zipcode, ?DateTime $startDate = null, ?DateTime $endDate = null)
    {
        $this->zipcode = $zipcode;
        if (!$startDate) {
            $startDate = new DateTime('-1 month', new \DateTimeZone(Utils::DEFAULT_DATE_TIME_ZONE));
        }

        $this->endDate = $endDate;
        $this->startDate = $startDate;
    }

    /**
     * @throws Exception
     */
    public function getEstimatedDelivery(): ?DateTime
    {
        $historicalData = (new EstimatedDeliveryDataSource())->getHistoricalData(
            $this->zipcode,
            $this->startDate,
            $this->endDate
        );

        if (empty($historicalData)) {
            return null;
        }

        $deliveryDays = $this->calculateEstimatedDeliveryDaysByHistoricalData($historicalData);
        $deliveryDate = new DateTime('now', new \DateTimeZone(Utils::DEFAULT_DATE_TIME_ZONE));
        $deliveryDate->add(new DateInterval('P' . $deliveryDays . 'D'));

        while(in_array((int)$deliveryDate->format('N'), Utils::NON_WORKING_DAYS, true)) {
            $deliveryDate->add(new DateInterval('P1D'));
        }

        return $deliveryDate;
    }

    /**
     * @throws Exception
     */
    protected function calculateEstimatedDeliveryDaysByHistoricalData($historicalData): string
    {
        $totalDeltaDays = 0;
        foreach ($historicalData as $data) {
            $weekendDays = 0;
            $shipmentDateTime = new DateTime($data['shipment_date']);
            $deliveredDateTime = new DateTime($data['delivered_date']);
            $dateInterval = $deliveredDateTime->diff($shipmentDateTime);

            // If there are non_working_days, we exclude them from the estimation
            $this->shiftNonWorkingDays($shipmentDateTime, $deliveredDateTime, $weekendDays);

            $totalDeltaDays += ($dateInterval->days - $weekendDays);
        }

        return round($totalDeltaDays / count($historicalData));
    }

    protected function shiftNonWorkingDays($date1, $date2, &$weekendDays): void
    {
        while($date1->diff($date2)->format('%a') > 0) {
            $date1->modify('+1 day');
            if (in_array((int)$date1->format('N'), Utils::NON_WORKING_DAYS, true)) {
                $weekendDays++;
            }
        }
    }
}
