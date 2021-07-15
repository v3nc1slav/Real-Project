<?php
 
/**
 * @copyright: 
 * @jira: T-171
 * @author: Ventsislav Verchov 
 */
 
include_once(__DIR__ . "../../update_date_indexed_mass.php");
 
$app->post('/distributeleads/', function ($req, $res, $args) use ($app) {
    global $db, $current_user;
    $updateArryLeads= [];
 
    $parsedBody = $req->getParsedBody();
    $input = $parsedBody['body'];
    $dataInput = $input['data'];
    $GLOBALS['log']->fatal($dataInput);
 
    $query = "SELECT t.id as tid, t.name AS tname, u.user_name AS namee, u.id AS id
        from users u
        inner join spiceaclterritories t on t.assigned_user_id = u.id
        where u.deleted = 0 AND u.user_team_options = 'nebula'";
 
    $data = $db->query($query);
    $array = [];
 
    while ($row = $db->fetchByAssoc($data)) {
 
        $array[] = array(
            "accountName" => $row["namee"],
            "accountId"=>$row['id'],
            "territoryId"=>$row['tid'],
            "territoryName"=>$row['tname'],
        );
    }
 
    for ($i=0; $i<count($dataInput); $i++) { 
        $beanLeads = BeanFactory::getBean("Leads", $dataInput[$i]);
       if ($beanLeads->status_lead_c === 'Awaiting' && $beanLeads->type_c === 'fuels') {
            $beanLeads->status_lead_c="Assigned";
            if ($i>count($array)) {
                $i=$i%count($array);
                $beanLeads->assigned_user_id=$array[$i]['accountId'];
                $beanLeads->assigned_user_name=$array[$i]['accountName'];
                $beanLeads->spiceacl_primary_territory=$array[$i]['territoryId'];
                $beanLeads->spiceacl_primary_territory_name=$array[$i]['territoryName'];
            }
            else{
                $beanLeads->assigned_user_id=$array[$i]['accountId'];
                $beanLeads->assigned_user_name=$array[$i]['accountName'];
                $beanLeads->spiceacl_primary_territory=$array[$i]['territoryId'];
                $beanLeads->spiceacl_primary_territory_name=$array[$i]['territoryName'];
            }
            $beanLeads->save();
            array_push($updateArryLeads, $beanLeads->id);
       }
    }
    UpdateDateIndexed::update_date_indexeds("leads", $updateArryLeads);
 
    echo json_encode("OK");
});
