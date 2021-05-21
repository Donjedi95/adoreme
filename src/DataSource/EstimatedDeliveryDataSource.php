<?php

namespace App\Datasource;

use App\Utils;
use DateTime;
use Exception;
use PDOException;

class EstimatedDeliveryDataSource extends BaseMySQL
{
    public const TABLE_NAME = 'estimated_delivery';
    public const TABLE_COLUMN_ZIP_CODE = 'zip_code';
    public const TABLE_COLUMN_SHIPMENT_DATE = 'shipment_date';
    public const TABLE_COLUMN_DELIVERED_DATE = 'delivered_date';

    /**
     * @param null|string|DateTime $startDate
     * @throws Exception
     */
    public function getHistoricalData(string $zipCode, $startDate = null, $endDate = null): array
    {
        $sql = 'SELECT * FROM `{table}` WHERE `{zip_code_column}` = :zipCode';

        Utils::formatDate($startDate);
        Utils::formatDate($endDate);

        if ($startDate) {
            $sql .= ' AND `{delivered_date_column}` >= :startDate';

            if ($endDate) {
                $sql .= ' AND `{delivered_date_column}` < :endDate';
            }
        }
        $sql .= ' AND `{delivered_date_column}` IS NOT NULL';

        $sql = strtr(
            $sql,
            [
                '{table}' => self::TABLE_NAME,
                '{zip_code_column}' => self::TABLE_COLUMN_ZIP_CODE,
                '{delivered_date_column}' => self::TABLE_COLUMN_DELIVERED_DATE
            ]
        );

        $statement = $this->getConnection()->prepare($sql);
        $statement->bindParam('zipCode', $zipCode);

        if ($startDate) {
            $statement->bindParam('startDate', $startDate);
            if ($endDate) {
                $statement->bindParam('endDate', $endDate);
            }
        }

        try {
            $statement->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        return $statement->fetchAll();
    }

    /**
     * @param string $zipCode
     * @param $shipmentDate
     * @param $deliveryDate
     * @throws Exception
     */
    public function addHistoricalData(string $zipCode, $shipmentDate, $deliveryDate): void
    {
        Utils::formatDate($shipmentDate);
        Utils::formatDate($deliveryDate);

        $sql = 'CALL add_estimated_delivery(:zipCode, :shipmentDate, :deliveryDate)';

        $statement = $this->getConnection()->prepare($sql);
        $statement->bindParam('zipCode', $zipCode);
        $statement->bindParam('shipmentDate', $shipmentDate);
        $statement->bindParam('deliveryDate', $deliveryDate);

        try {
            $statement->execute();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
}
