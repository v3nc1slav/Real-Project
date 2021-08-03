<?php
 
/**
 * @copyright: Bizlink
 * @jira: 
 * @author: Ventsislav Verchov vverchov@bizlink-solutions.eu
 */
 
 
$app->post(
    '/massdeleted/',
    function ($req, $res, $args) use ($app) {
 
        $parsedBody = $req->getParsedBody();
        $input = $parsedBody['body'];
        $model = $input['model'];
        $deleted = $input['deletedItams'];
 
        foreach ($deleted as &$value) {
            //Get Bean
            $bean = BeanFactory::getBean("$model", $value);
            //Set deleted to true
            $bean->mark_deleted($value);
            //Save
            $bean->save();
            //Log
            $GLOBALS['log']->fatal("Item with name: $bean->name and id: $value is deleted");
        }
        echo true; 
    }
);
 
RAW Paste Data