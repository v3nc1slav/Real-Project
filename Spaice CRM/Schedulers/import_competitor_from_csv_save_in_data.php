<?php
 
/**
 * @copyright: Bizlink
 * @jira: T-145
 * @author: Ventsislav Verchov 
 */
 
importCompetitorFromCsvSaveInData();
 
function importCompetitorFromCsvSaveInData(){
        global $db;
 
        $row = 1;
        $today = date("Y-m-d");
        $logfile = fopen(dirname(__FILE__) ."/not_found_accounts_$today.log", "w");
 
       if (($csv_file = fopen(dirname(__FILE__) ."/competitors_import/2021-01-26_All-clients-competition.csv", "r")) !== FALSE) {
 
           $GLOBALS['log']->fatal("read File");
 
            while (($data = fgetcsv($csv_file)) !== FALSE) {
                   if ($row > 2) {
 
                        $bulstat = $data[2]; //BG 000 000 000
                        $bulstatSkip = substr($data[2], 2); //000 000 0000
                        $accountsName = $data[1];
                        $competitorsName = strtoupper( $data[0]);
 
                        $queryAccountsCodSkip = "SELECT ac.bulstat_c AS cod, a.id AS id
                        FROM accounts a 
						left join accounts_cstm ac on a.id=ac.id_c
                        WHERE a.deleted=0  
						and ac.bulstat_c = '$bulstatSkip'";
 
                        $wasInside = false;
 
                        $dataAccountCodSkip = $db->query($queryAccountsCodSkip);  
                        while($rowDB = $db->fetchByAssoc($dataAccountCodSkip)) {
							$GLOBALS['log']->fatal("Create competitor for company with bulstat: $bulstatSkip");
                            $wasInside = true;
                            addCompetition($competitorsName, $rowDB['id']);
                        }
 
                        if (!$wasInside) {
 
                            fwrite($logfile,"Account: $accountsName which Bulstat: $bulstatSkip not found.\n");
                            $GLOBALS['log']->fatal("Account: $accountsName which Bulstat: $bulstatSkip not found.");
                        }
                    }
                    $row++;
                }
                $GLOBALS['log']->fatal("Last updated row: $row");
            }
 
            fclose($csv_file);
            fclose($logfile);
 
   return true;
}
 
function addCompetition($name, $id){
    $year = '2020';
 
    $accountBean =  BeanFactory::getBean("Accounts",$id);
    $competitorBean =  BeanFactory::newBean("TOM06_Competitors");
 
    $competitorBean->name = $year;
    $competitorBean->created_by = 1;
    $competitorBean->description = " ";
    $competitorBean->tags = " ";
    $competitorBean->competitor = $name;
    $competitorBean->services = " ";
 
    $competitorBean->save();

    $competitorBean->load_relationship("tom06_competitors_accounts");  
    $competitorBean->tom06_competitors_accounts->add($accountBean); 
}