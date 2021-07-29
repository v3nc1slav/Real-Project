<?php
include_once(__DIR__ . "../../update_date_indexed_mass.php");
 
distributed_to_companie();
 
function distributed_to_companie(){
    global $db, $logfile;
    $updateArryAccount= [];
    $today = date("Y-m-d");
    $logfile = fopen(dirname(__FILE__) ."/Log_distributed_to_companie_$today.log", "w");
    $query = "SELECT a.id As id, ac.employees_last_year_c As employees, ac.bzlnk_state_id_c As area, ac.bzlnk_municipality_id_c As municipality
    FROM accounts a
    INNER JOIN accounts_cstm ac ON ac.id_c = a.id 
    WHERE a.assigned_user_id = '1' AND a.deleted = 0"; 
    $account = $db->query($query);
 
    while($row =$db->fetchByAssoc($account)){
        $id= $row['id'];
        $employees = $row['employees'];
        $areaId = $row['area'];
        $municipalityId = $row['municipality'];
 
        $beanAccount = BeanFactory::getBean("Accounts", $id);
        $beanAccount->load_relationship("tom04_users_portfolio_accounts_1");
        $relarr = $beanAccount->tom04_users_portfolio_accounts_1->get();
        if(count($relarr) > 0){
            continue;
        }
        handleTerritoryAndPorfolio($beanAccount,$employees,$areaId,$municipalityId,$logfile);
        $beanAccount->save();
        array_push($updateArryAccount, $beanAccount->id);
    }
    UpdateDateIndexed::update_date_indexeds("accounts", $updateArryAccount);
    fclose($logfile);
    return true;
}
 
function handleTerritoryAndPorfolio($bean, $employeCount, $areaId, $municipalityId,$logfile)
{
    global $db;
    $TELESALES = "telesales";
    $SALES = "sales";
    $sofiaTempPortfolioId = 'c1eb2b41-d86d-er5d-85de-acf06840d35d';
    $amindPortfolioId = '1b876b81-cc5c-2c3b-a5e4-8725f58a6ba2';
    $sofiaAreaId = "5f100804-a678-3f1e-aad8-5379fa580217";
    $sofiaCapitalAreaId = "7d44d492-c74d-ee46-12ac-5379fa074dd0"; 
    $bean->load_relationship("tom04_users_portfolio_accounts_1");
    if ($employeCount < 7) {
        // assign this account to admin porfolio
        $bean->tom04_users_portfolio_accounts_1->add($amindPortfolioId);
        $query = "SELECT p.id as p_id, p.name as p_name, p.name as p_name, p.assigned_user_id as auid, p.spiceacl_primary_territory as spt, p.spiceacl_territories_hash as sth
                    FROM  tom04_users_portfolio p 
                    WHERE p.id = '$amindPortfolioId'";
        $result = $db->fetchByAssoc($db->query($query));
        $p_id = $result['p_id'];
        $userId = $result['auid'];
        $spt = $result['spt'];
        $sth = $result['sth'];
        $bean->tom04_users_portfolio_accounts_1->add($p_id);
        $bean->assigned_user_id = $userId;
        $bean->spiceacl_primary_territory = $spt;
        $bean->spiceacl_territories_hash = $sth;
         $bean->tom04_users_portfolio_accounts_1tom04_users_portfolio_ida = $sth;
        $GLOBALS['log']->fatal("$bean->name, set user on $userId, and territory on $spt" );
        fwrite($logfile,"$bean->name, set user on $userId, and territory on $spt.\n");
    } else {
        $porfolioType = "";
        if ($employeCount > 30) {
            $porfolioType = $SALES;
        } else {
            $porfolioType = $TELESALES;
        }
        // check if area is other than SOFIA CITY and SOFIA AREA
        if ($areaId != $sofiaCapitalAreaId && $areaId != $sofiaAreaId) {
            //find the porfolio with correct territory and type & assign data from it to account
            $state = getStateFrom_Id($areaId);
            $query = "SELECT p.id as p_id, p.name as p_name, p.name as p_name, p.assigned_user_id as auid, p.spiceacl_primary_territory as spt, p.spiceacl_territories_hash as sth
                FROM  tom04_users_portfolio p 
                WHERE p.portfolio_type = '$porfolioType' AND p.state = '$state' and p.private = 0";
            $result = $db->fetchByAssoc($db->query($query));
            $p_id = $result['p_id'];
            $userId = $result['auid'];
            $spt = $result['spt'];
            $sth = $result['sth'];
            $bean->tom04_users_portfolio_accounts_1->add($p_id);
            $bean->assigned_user_id = $userId;
            $bean->spiceacl_primary_territory = $spt;
            $bean->spiceacl_territories_hash = $sth;
            $bean->tom04_users_portfolio_accounts_1tom04_users_portfolio_ida = $sth;
            $GLOBALS['log']->fatal("$bean->name, set user on $userId, and territory on $spt" );
            fwrite($logfile,"$bean->name, set user on $userId, and territory on $spt with porfolio type $porfolioType.\n");
        } else if ($areaId == $sofiaCapitalAreaId) {
            $query = "SELECT p.id as p_id, p.name as p_name, p.name as p_name, p.assigned_user_id as auid, p.spiceacl_primary_territory as spt, p.spiceacl_territories_hash as sth
                    FROM  tom04_users_portfolio p 
                    WHERE p.id = '$sofiaTempPortfolioId'";
            $result = $db->fetchByAssoc($db->query($query));
            $p_id = $result['p_id'];
            $userId = $result['auid'];
            $spt = $result['spt'];
            $sth = $result['sth'];
            $bean->tom04_users_portfolio_accounts_1->add($p_id);
            $bean->assigned_user_id = $userId;
            $bean->spiceacl_primary_territory = $spt;
            $bean->spiceacl_territories_hash = $sth;
             $bean->tom04_users_portfolio_accounts_1tom04_users_portfolio_ida = $sth;
            $GLOBALS['log']->fatal("$bean->name, set user on $userId, and territory on $spt" );
            fwrite($logfile,"$bean->name, set user on $userId, and territory on $spt with porfolio type $porfolioType.\n");
            //account is assigned to sofia temp portfolio
        } else if ($areaId == $sofiaAreaId) {
            //search for porfolios by the municipality
            $state = getMunicipalityFrom_Id($municipalityId);
            $query = "SELECT p.id as p_id, p.name as p_name, p.name as p_name, p.assigned_user_id as auid, p.spiceacl_primary_territory as spt, p.spiceacl_territories_hash as sth
                        FROM  tom04_users_portfolio p 
                        WHERE p.portfolio_type = '$porfolioType' AND p.municipality = '$state' and p.private = 0";
            $result = $db->fetchByAssoc($db->query($query));
            $p_id = $result['p_id'];
            $userId = $result['auid'];
            $spt = $result['spt'];
            $sth = $result['sth'];
            $bean->tom04_users_portfolio_accounts_1->add($p_id);
            $bean->assigned_user_id = $userId;
            $bean->spiceacl_primary_territory = $spt;
            $bean->spiceacl_territories_hash = $sth;
             $bean->tom04_users_portfolio_accounts_1tom04_users_portfolio_ida = $sth;
            $GLOBALS['log']->fatal("$bean->name, set user on $userId, and territory on $spt" );
            fwrite($logfile,"$bean->name, set user on $userId, and territory on $spt with porfolio type $porfolioType.\n");
        } else {
            $bean->tom04_users_portfolio_accounts_1->add($amindPortfolioId);
            $query = "SELECT p.id as p_id, p.name as p_name, p.name as p_name, p.assigned_user_id as auid, p.spiceacl_primary_territory as spt, p.spiceacl_territories_hash as sth
                        FROM  tom04_users_portfolio p 
                        WHERE p.id '$amindPortfolioId'";
            $result = $db->fetchByAssoc($db->query($query));
            $p_id = $result['p_id'];
            $userId = $result['auid'];
            $spt = $result['spt'];
            $sth = $result['sth'];
            $p_name = $result['p_name'];
            $bean->tom04_users_portfolio_accounts_1->add($p_id);
            $bean->assigned_user_id = $userId;
            $bean->spiceacl_primary_territory = $spt;
            $bean->spiceacl_territories_hash = $sth;
             $bean->tom04_users_portfolio_accounts_1tom04_users_portfolio_ida = $sth;
            $GLOBALS['log']->fatal("$bean->name, set user on $userId, and territory on $spt" );
            fwrite($logfile,"$bean->name, set user on $userId, and territory on $spt with porfolio type $porfolioType.\n");
        }
    }
    $bean->spiceacl_secondary_territories = "";
}
 
function getStateFrom_Id($id){
    switch ($id) {
      case'3ef1100a-148a-8826-c1c5-5376faa4aw86':return'vidin';
                    ...........
    }
  }
 
  function getMunicipalityFrom_Id($id){
    switch ($id) {
      case'b85c4979-047c-b73c-4fd4-537b67c18f72':return'admin';
            .........
    }
  }