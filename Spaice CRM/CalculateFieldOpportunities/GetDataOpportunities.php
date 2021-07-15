<?php
 
/**
 * @copyright: 
 * @jira: T-134
 * @author: Ventsislav Verchov 
 */
 
    $app->get('/oppertunitiesgetrelatedoffers/{id}', function($req, $res, $args) use ($app) {
        global $db,$current_user;
        $id = $args['id'];
 
        $query="SELECT COUNT(op.id) AS num
        FROM opportunities op
        INNER JOIN bzlnk_offers_opportunities_c ofop ON ofop.bzlnk_offers_opportunitiesopportunities_ida = op.id AND ofop.deleted=0
        INNER JOIN bzlnk_offers of ON of.id= ofop.bzlnk_offers_opportunitiesbzlnk_offers_idb and of.deleted = 0
        WHERE op.id ='$id'";
 
        $data = $db->query($query);
 
        while ($row = $db->fetchByAssoc($data)) {
            echo json_encode($row['num']);
        }
 
    });
 
    $app->get('/oppertunitiesgetspiceattachments/{id}', function($req, $res, $args) use ($app) {
        global $db,$current_user;
        $id = $args['id'];
 
        $query="SELECT COUNT(s.id) AS num
        FROM spiceattachments s
        WHERE s.bean_type = 'Opportunities' AND s.bean_id ='$id' AND s.deleted = 0";
 
        $data = $db->query($query);
 
        while ($row = $db->fetchByAssoc($data)) {
 
            echo json_encode($row['num']);
        }
    });
 
    $app->get('/oppertunitiesgetcontracts/{id}', function($req, $res, $args) use ($app) {
        global $db,$current_user;
        $id = $args['id'];
 
        $query="SELECT COUNT(tc.id) AS num
        from tom08_contracts tc
        INNER JOIN opportunities_tom08_contracts_1_c tcp ON tcp.opportunities_tom08_contracts_1tom08_contracts_idb = tc.id AND tcp.deleted=0
        INNER JOIN opportunities o ON o.id = tcp.opportunities_tom08_contracts_1opportunities_ida AND o.deleted = 0
        WHERE o.id = '$id'";
 
        $data = $db->query($query);
 
        while ($row = $db->fetchByAssoc($data)) {
 
            echo json_encode($row['num']);
        }
    });
RAW Paste Data
