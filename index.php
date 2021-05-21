<?php

$loader = require "vendor/autoload.php";
$loader->addPsr4('App\\', __DIR__ . '/src');

use App\Service\EstimatedDeliveryService;
use App\Utils;

try {
    Utils::initEnvData();
} catch (Exception $ex) {
    echo '<h3>' . $ex->getMessage() . '</h3>';
}

$zipCode = filter_input(INPUT_GET, 'zip-code');
    $startDate = filter_input(INPUT_GET, 'start-date');
    $endDate = filter_input(INPUT_GET, 'end-date');

    $lastMonth = date('Y-m-d', strtotime('-1 months'));
    $lastThreeMonths = date('Y-m-d', strtotime('-3 months'));

    $estimation = null;
    $notFoundMessage = null;
    if ($zipCode) {
        try {
            $startDate = new DateTime($startDate, new DateTimeZone(Utils::DEFAULT_DATE_TIME_ZONE));
            $endDate = new DateTime($endDate, new DateTimeZone(Utils::DEFAULT_DATE_TIME_ZONE));

            $estimatedDelivery = new EstimatedDeliveryService($zipCode, $startDate, $endDate);
            $estimation = $estimatedDelivery->getEstimatedDelivery();
            if ($estimation === null) {
                $notFoundMessage = 'Could not find historical data for estimation!';
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
?>

<!DOCTYPE HTML>
<head>
    <title>AdoreMe - Mihai Caragheorghiev</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <script src="assets/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
</head>
<body>
    <main>
        <h1 class="text-center">Estimate Delivery:</h1>
        <br/>
        <div class="container-sm">
            <div class="col-xs-12">
                <div class="input-group">
                    <label for="zip-code"></label>
                    <input placeholder="ZIP CODE..." id="zip-code" class=form-control" type="text" name="zip-code" />
                    <button class="btn btn-primary" onclick="getEstimate('last-month')">Estimate using Last Month</button>
                    <button class="btn btn-primary" onclick="getEstimate('last-3-months')">Estimate using Last 3 Months</button>
                </div>
            </div>
            <br/>
            <div class="form-group row">
                <div class="col-6">
                    <label for="custom-start-date">Historical Data Start Date:</label>
                </div>
                <div class="col-6">
                    <input id="custom-start-date" class=form-control" type="date" name="start-date" />
                </div>
            </div>
            <div class="form-group row">
                <div class="col-6">
                    <label for="custom-end-date">Historical Data End Date:</label>
                </div>
                <div class="col-6">
                    <input id="custom-end-date" class=form-control" type="date" name="send-date" />
                </div>
            </div>
            <button class="btn btn-primary" onclick="getEstimate('custom')">Estimate Custom Range</button>
            </div>
            <br/>
            <div class="container">
                <?php
                if ($estimation) { ?>
                    <div class="alert alert-info" role="alert">
                        Estimated delivery on <strong><?php echo $estimation->format('Y-m-d');?></strong>
                    </div>
                <?php }  ?>
                <?php
                if ($notFoundMessage) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $notFoundMessage; ?>
                    </div>
                <?php }  ?>
        </div>
    </main>
    <script>
        function getEstimate(estimationMode) {
            let lastMonth =  '<?php echo $lastMonth; ?>';
            let lastThreeMonths = '<?php echo $lastThreeMonths; ?>';
            let hrefString = '';

            let zipCode = document.getElementById('zip-code').value;
            if (zipCode !== undefined && zipCode !== null && zipCode !== '') {
                hrefString = '?zip-code=' + zipCode;
                switch (estimationMode) {
                    case 'last-month':
                        hrefString += '&start-date=' + lastMonth;
                        break;

                    case 'last-3-months':
                        hrefString += '&start-date=' + lastThreeMonths;
                        break;

                    case 'custom':
                        let customStartDate = document.getElementById('custom-start-date').value;
                        let customEndDate = document.getElementById('custom-end-date').value;

                        console.log(customStartDate);
                        console.log(customEndDate);

                        if (
                            (customStartDate !== undefined && customStartDate !== null && customStartDate !== '')
                            ||
                            (customEndDate !== undefined && customEndDate !== null && customEndDate !== '')
                        ) {
                            hrefString += '&start-date=' + customStartDate + '&end-date=' + customEndDate;
                        } else {
                            alert('Complete the custom START DATE and END DATE!');
                        }

                        break

                    default :
                        alert('getEstimate case not implemented!');
                        break;
                }

                window.location.href = hrefString;
            } else {
                alert('Complete the ZIP CODE!');
            }
        }
    </script>
</body>
