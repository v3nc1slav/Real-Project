<?php
 
/**
 * @copyright: Bizlink
 * @jira: 
 * @author: Ventsislav Verchov vverchov@bizlink-solutions.eu
 */
 
 
$app->post('/massupdate/', function ($req, $res, $args) use ($app) {
 
        $parsedBody = $req->getParsedBody();
        $input = $parsedBody['body'];
        $model = $input['model'];
        $update = $input['updateItams'];
        $fields = $input['fields'];
 
        foreach ($update as $value) {
 
            //Get Bean
            $bean = BeanFactory::getBean("$model", $value);
            $log = "Item with name: $bean->name and id: $value is update: ";
 
            foreach($fields as $field){
                foreach($field as $key=>$value2){
 
                $bean->$key = $value2;
 
                $log = $log. "field $key - new value $value2; ";
                }
 
                //Save
                $bean->save();
 
                //Log
                $GLOBALS['log']->fatal("$log");
            }
        }
        echo true; 
    }
);