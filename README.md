# How to:

## Clone the project in a folder

## Create your .env file based on .env-template. Please use MySQL as a DATABASE

## Run Database Queries:
  - Create the TABLE 'estimated_delivery with query from assets/docs/data_source_mysql.sql
  - Create the PROCEDURE with query from assets/docs/data_source_mysql.sql

## Open your web server for the new project (ex with built in PHP Webserver)
    php -S localhost:8000

## Generate Random estimated_delivery by using http://localhost/generate_delivery_data.php?lines={no_of_lines}&zip-codes={no_of_zipcodes}. Ex:
    http://localhost/generate_delivery_data.php?lines=5000&zip-codes=5
   
   - You can use queries from assets/docs/data_source_mysql.sql to check the generated data statistics

## Use the http://localhost/index.php To fill in the data and get an estimated delivery date

## To run Unit tests go to root project folder and use:
    php codecept.phar run unit
