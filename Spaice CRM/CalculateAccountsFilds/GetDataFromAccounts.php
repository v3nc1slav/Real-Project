<?php
/**
 * @copyright: 
 * @jira: T-199
 * @author: Ventsislav Verchov 
 */
$app->get('/getdatafromaccounts/{id}', function ($req, $res, $args) use ($app) {
    global $db;
    $id = $args['id'];
 
    $query = "SELECT bm.id AS municipalityId, bm.name AS municipality, bs.id AS areaaId, bs.name AS areaa, bz.name AS zip, br.id AS regionId, br.name AS region
    FROM bzlnk_cities c 
    INNER JOIN bzlnk_municipality bm ON bm.id = c.bzlnk_municipality_id_c AND bm.deleted = 0
    INNER JOIN bzlnk_state bs ON bs.id = bm.bzlnk_state_id_c AND bs.deleted=0
    INNER JOIN bzlnk_zip_codes bz ON bz.bzlnk_city_id = c.id AND bz.deleted = 0
    INNER JOIN bzlnk_regions br ON br.id = bs.bzlnk_regions_id_c AND br.deleted =0
    WHERE c.id = '$id' AND c.deleted = 0";
 
    $data = $db->query($query);
    $array = [];
 
    while ($row = $db->fetchByAssoc($data)) {
 
        $array[] = array(
            "municipalityId" => $row["municipalityId"],
            "municipality" => $row["municipality"],
            "zipCode"=>$row['zip'],
            "state"=>$row['areaa'],
            "stateId"=>$row['areaaId'],
            "regionId"=>$row['regionId'],
            "region"=>$row['region'],
        );
    }
     echo json_encode($array);
});
 