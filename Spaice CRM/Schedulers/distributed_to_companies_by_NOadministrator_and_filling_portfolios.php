<?php
 
distributed_to_campaign_noAccounts();
 
function distributed_to_campaign_noAccounts(){
    global $db, $logfile;
    $today = date("Y-m-d");
    $logfile = fopen(dirname(__FILE__) ."/Log_distributed_to_campaign_by_NOadmin-$today.log", "w");
 
    $query = "SELECT a.id As id, ac.employees_last_year_c As employees, ac.bzlnk_state_id_c As area
    FROM accounts a
    INNER JOIN accounts_cstm ac ON ac.id_c = a.id 
    WHERE a.assigned_user_id <> '1' AND a.deleted = 0 and ac.employees_last_year_c >=7  "; 
    $accountId = $db->query($query);
 
    while($row =$db->fetchByAssoc($accountId)){
        $id= $row['id'];
        $employeCount = $row['employees'];
        $areaId = $row['area'];
 
        $beanAccount = BeanFactory::getBean("Accounts", $id);
        $beanAccount->load_relationship("tom04_users_portfolio_accounts_1");
        $relarr = $beanAccount->tom04_users_portfolio_accounts_1->get();
 
        if(count($relarr) > 0){
            continue;
        }
 
        $porfolioType = "";
 
        $employeCount > 30 ?  $porfolioType = "sales" :  $porfolioType = "telesales";
        $state = getStateFrom_Id($areaId);
        $isAssUser = false;
        $queryPortfolio = "SELECT t.id AS id, t.assigned_user_id AS AssId
        from tom04_users_portfolio t
        WHERE t.deleted = 0 and t.portfolio_type = '$porfolioType' AND t.state = '$state' and t.private = 0";  
 
        $result = $db->query($queryPortfolio);
        while ($row = $db->fetchByAssoc($result)) {
            if ($beanAccount->assigned_user_id === $row['AssId']) {
                $porId = $row['id'];
                $isAssUser =  true;
                setPorfolio($beanAccount, $porId);
                continue;
            }
        }
 
        if (!$isAssUser) {
            $porfolioType === "telesales" ? $porfolioType = "sales" :  $porfolioType = "telesales";
 
            $queryPortfolio = "SELECT t.id AS id, t.assigned_user_id AS AssId
            from tom04_users_portfolio t
            WHERE t.deleted = 0 and t.portfolio_type = '$porfolioType' AND t.state = '$state' and t.private = 0";  
 
            $result = $db->query($queryPortfolio);
            while ($row = $db->fetchByAssoc($result)) {
                if ($beanAccount->assigned_user_id === $row['AssId']) {
                    $porId = $row['id'];
                    $isAssUser =  true;
                    setPorfolio($beanAccount, $porId);
                    continue;
                }
            }
        }
 
        if (!$isAssUser) {
            $newQuery = "SELECT t.id AS id
                 from tom04_users_portfolio t
                 WHERE t.deleted = 0 AND t.private = 1 and t.assigned_user_id = '$beanAccount->assigned_user_id' Limit 1"; 
 
            $resultQuery = $db->query($newQuery);     
            while ($row = $db->fetchByAssoc($resultQuery)) {
                $portId = $row['id'];
                setPorfolio($beanAccount, $portId);
            }  
        }
    }
    fclose($logfile);
    return true;
}
 
function setPorfolio($beanAccount, $p_id){
    global $logfile;
 
    $beanPortfolio = BeanFactory::getBean("TOM04_Users_portfolio", $p_id);
    $beanAccount->load_relationship("tom04_users_portfolio_accounts_1");
    $beanAccount->tom04_users_portfolio_accounts_1->add($beanPortfolio);
 
    $GLOBALS['log']->fatal("$beanAccount->name, with id $beanAccount->id set porfolio $p_id." );
    fwrite($logfile,"$beanAccount->name, with id $beanAccount->id set porfolio $p_id.\n");
};
 
 
function getStateFrom_Id($id){
    switch ($id) {
      case'3ef1100a-178a-8826-c6c5-5379raa4ae86':return'vidin';
        ........
    }
  }