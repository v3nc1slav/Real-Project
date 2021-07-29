<?php
 /**
 * @copyright: 
 * @jira:
 * @author: Ventsislav Verchov 
 */

class UpdateDateIndexed
{
   static function update_date_indexeds($model, $arr){
        global $db;
        if (count($arr)===0) {
            return;
        }
        $value = join('\',\'',$arr);
        $value =  "'".$value."'";
            $query = "UPDATE $model SET date_indexed = NULL WHERE id in($value)";
            $db->query($query);
 
            $GLOBALS['log']->fatal("--------Date indexed in model $model for IDs $value are reset---------");
    }
}
