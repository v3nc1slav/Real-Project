<?php

$job_strings[6] = 'import_competitor_from_csv_save_in_data';

$job_strings[9] = 'distributed_to_companies_by_administrator_and_filling_portfolios';
$job_strings[10] = 'distributed_to_companies_by_NOadministrator_and_filling_portfolios';
 

function import_competitor_from_csv_save_in_data()
{
    include 'import_competitor_from_csv_save_in_data.php';
    return true;
}
 
function distributed_to_companies_by_administrator_and_filling_portfolios()
{
    include 'distributed_to_companies_by_administrator.php';
    return true;
}
 
function distributed_to_companies_by_Noadministrator_and_filling_portfolios()
{
    include 'distributed_to_companies_by_NOadministrator.php';
    return true;
}
 