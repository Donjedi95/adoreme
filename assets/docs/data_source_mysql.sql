-- CREATE TABLE

CREATE TABLE `estimated_delivery` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `zip_code` VARCHAR(15) NOT NULL,
    `shipment_date` DATE NOT NULL,
    `delivered_date` DATE DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `estimated_delivery_zip_code_IDX` (`zip_code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- CREATE PROCEDURE add_estimated_delivery
CREATE PROCEDURE add_estimated_delivery (
    IN p_zip_code VARCHAR(15),
    IN p_shipment_date DATE,
    IN p_delivered_date DATE
) BEGIN
    INSERT INTO `estimated_delivery`(zip_code, shipment_date, delivered_date)
    VALUE (p_zip_code, p_shipment_date, p_delivered_date);
END;

-- EXAMPLE
SET @shipment_date = '2021-02-01';
SET @delivered_date = '2021-02-08';
CALL add_estimated_delivery('022143', str_to_date(@shipment_date, '%Y-%m-%d'), str_to_date(@delivered_date, '%Y-%m-%d'));

-- SELECTION TO CHECK
SELECT COUNT(*)
FROM estimated_delivery ed;

-- SEE all zip_codes and how many lines they have
SELECT `zip_code`, COUNT(`zip_code`)
FROM `estimated_delivery`
GROUP BY `zip_code`;

-- CHECK to see there are dates only 3 to 14 days apart
SELECT `id`, DATEDIFF(`delivered_date`, `shipment_date`) as day_difference
FROM `estimated_delivery`
HAVING day_difference < 3 or day_difference > 14;