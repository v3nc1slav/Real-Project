<?php
 
/**
 * @copyright: 
 * @jira: T-133
 * @author: Ventsislav Verchov 
 */

$app->post('/BZLNK_Offers/createPDF/Naredba7', function ($req, $res, $args) use ($app) {
    include_once 'Naredba7.php';
    $postBody = $req->getParsedBody();
    $id = $postBody['id'];
    Naredba_7($id);
 
});