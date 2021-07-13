<?php
/**
 * @copyright:
 * @jira: T-198
 * @author: Ventsislav Verchov 
 */
$app->get('/getdatafromnikd2008/{text}', function ($req, $res, $args) use ($app) {
    global $db;
    $text = $args['text'];
 
    $query = "SELECT gn.name AS groups, gn.id AS groupsId, c.name AS classifications, c.id AS classificationsId , s.name AS sectors, s.id AS sectorsId
    from bzlnk_nkid_2008 bn
    INNER JOIN tom03_group_nkid2008_bzlnk_nkid_2008_1_c tgn ON tgn.tom03_group_nkid2008_bzlnk_nkid_2008_1bzlnk_nkid_2008_idb =  bn.id AND tgn.deleted = 0
    INNER JOIN tom03_group_nkid2008 gn ON gn.id = tgn.tom03_group_nkid2008_bzlnk_nkid_2008_1tom03_group_nkid2008_ida AND gn.deleted = 0
    INNER JOIN tom02_classifications_tom03_group_nkid2008_1_c tc ON tc.tom02_clas99aekid2008_idb = gn.id AND tc.deleted = 0
    INNER JOIN tom02_classifications c ON c.id = tc.tom02_clas90fdcations_ida AND c.deleted = 0
    INNER JOIN tom01_sectors_tom02_classifications_1_c ts ON ts.tom01_sectors_tom02_classifications_1tom02_classifications_idb = c.id AND ts.deleted = 0
    INNER JOIN tom01_sectors s ON s.id = ts.tom01_sectors_tom02_classifications_1tom01_sectors_ida AND ts.deleted = 0
    WHERE bn.name = '$text' AND bn.deleted = 0";
 
    $data = $db->query($query);
    $array = [];
 
    while ($row = $db->fetchByAssoc($data)) {
 
        $array[] = array(
            "groups" => $row["groups"],
            "groupsId" => $row["groupsId"],
            "classifications"=>$row['classifications'],
            "classificationsId"=>$row['classificationsId'],
            "sectors"=>$row['sectors'],
            "sectorsId"=>$row['sectorsId'],
        );
    }
    echo json_encode($array);
});