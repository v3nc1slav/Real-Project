<?php
 
/**
 * @copyright: 
 * @jira: T-170
 * @author: Ventsislav Verchov 
 */
 
 
$app->get('/prefilleddatabulstat/{bulstat}/', function ($req, $res, $args) use ($app) {
    global $db, $current_user;
    $bulstat = $args['bulstat'];
    $id = $args['id'];
 
    $query = "SELECT a.name AS namee, a.id as acc_id,ac.bzlnk_cities_id_c AS city, b.name AS city2
        FROM accounts_cstm ac 
        LEFT JOIN accounts a ON a.id =  ac.id_c AND a.deleted = 0
        LEFT JOIN bzlnk_cities b ON b.id = ac.bzlnk_cities_id_c AND b.deleted = 0
        WHERE ac.bulstat_c ='$bulstat' AND a.deleted IS NOT NULL";
 
    $data = $db->query($query);
    $returnObj = [];
 
    while ($row = $db->fetchByAssoc($data)) {
 
        $returnObj[] = array(
            "accountName" => $row["namee"],
            "accountId"=>$row['acc_id'],
            "cityId" => $row["city"],
            "cityName"=>$row["city2"],
 
        );
    }
    echo json_encode($returnObj);
});