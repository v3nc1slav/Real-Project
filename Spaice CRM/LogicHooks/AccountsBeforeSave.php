<?php
 
class AccountsBeforeSave
{
	function set_portfolio_if_new($bean, $event, $arguments)	{
		if (empty($bean->fetched_row['id']) && !empty($bean->id)) {
			global $db, $current_user;
			$employeCount = $bean->employees_last_year_c;
			$areaId = $bean->bzlnk_state_id_c;
			$municipalityId = $bean->bzlnk_municipality_id_c;
			$TELESALES = "telesales";
			$SALES = "sales";
			$sofiaTempPortfolioId = "3bbfe417-a071-94a0-049b-87731dcc07a0";
			$amindPortfolioId = "8dc91bdc-6b4e-50d5-8cd7-c892525c5bff";
			$sofiaAreaId = "5f100804-a678-3f1a-aad8-5379fa580217";
			$sofiaCapitalAreaId = "7d44d492-c70d-ee46-12ac-5379fa074dd0";
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
				$p_name = $result['p_name'];
				// serializedRow::logAssignedPortfoliosAndTerritories($bean,$employeCount,$result);
				$bean->tom04_users_portfolio_accounts_1->add($p_id);
				$bean->assigned_user_id = $userId;
				$bean->spiceacl_primary_territory = $spt;
				$bean->spiceacl_territories_hash = $sth;
			} else {
				$porfolioType = null;
				if ($employeCount > 30) {
					$porfolioType = $SALES;
				} else {
					$porfolioType = $TELESALES;
				}
				// check if area is other than SOFIA CITY and SOFIA AREA
				if ($areaId != $sofiaCapitalAreaId && $areaId != $sofiaAreaId) {
					//find the porfolio with correct territory and type & assign data from it to account
					$state = $this->getStateFromId($areaId);
					$GLOBALS['log']->fatal("state:  $state");
					$query = "SELECT p.id as p_id, p.name as p_name, p.name as p_name, p.assigned_user_id as auid, p.spiceacl_primary_territory as spt, p.spiceacl_territories_hash as sth
			 		FROM  tom04_users_portfolio p 
					WHERE p.portfolio_type = '$porfolioType' AND p.state = '$state' and p.private = 0";
					$result = $db->fetchByAssoc($db->query($query));
					$p_id = $result['p_id'];
					$userId = $result['auid'];
					$spt = $result['spt'];
					$sth = $result['sth'];
					$GLOBALS['log']->fatal("$p_id $userId $spt $sth");
					//serializedRow::logAssignedPortfoliosAndTerritories($bean,$employeCount,$result);
					$bean->tom04_users_portfolio_accounts_1->add($p_id);
					$bean->assigned_user_id = $userId;
					$bean->spiceacl_primary_territory = $spt;
					$bean->spiceacl_territories_hash = $sth;
				} else if ($areaId == $sofiaCapitalAreaId) {
					$query = "SELECT p.id as p_id, p.name as p_name, p.name as p_name, p.assigned_user_id as auid, p.spiceacl_primary_territory as spt, p.spiceacl_territories_hash as sth
			FROM  tom04_users_portfolio p 
		   WHERE p.id = '$sofiaTempPortfolioId'";
					$result = $db->fetchByAssoc($db->query($query));
					$p_id = $result['p_id'];
					$userId = $result['auid'];
					$spt = $result['spt'];
					$sth = $result['sth'];
					// serializedRow::logAssignedPortfoliosAndTerritories($bean,$employeCount,$result);
					$bean->tom04_users_portfolio_accounts_1->add($p_id);
					$bean->assigned_user_id = $userId;
					$bean->spiceacl_primary_territory = $spt;
					$bean->spiceacl_territories_hash = $sth;
					//account is assigned to sofia temp portfolio
				} else if ($areaId == $sofiaAreaId) {
					//search for porfolios by the municipality
					$state = $this->getMunicipalityFromId($municipalityId);
					$query = "SELECT p.id as p_id, p.name as p_name, p.name as p_name, p.assigned_user_id as auid, p.spiceacl_primary_territory as spt, p.spiceacl_territories_hash as sth
		   FROM  tom04_users_portfolio p 
		   WHERE p.portfolio_type = '$porfolioType' AND p.municipality = '$state' and p.private = 0";
					$result = $db->fetchByAssoc($db->query($query));
					$p_id = $result['p_id'];
					$userId = $result['auid'];
					$spt = $result['spt'];
					$sth = $result['sth'];
					$bean->tom04_users_portfolio_accounts_1->add($p_id);
					// serializedRow::logAssignedPortfoliosAndTerritories($bean,$employeCount,$result);
					$bean->assigned_user_id = $userId;
					$bean->spiceacl_primary_territory = $spt;
					$bean->spiceacl_territories_hash = $sth;
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
					// serializedRow::logAssignedPortfoliosAndTerritories($bean,$employeCount,$result);
					$bean->tom04_users_portfolio_accounts_1->add($p_id);
					$bean->assigned_user_id = $userId;
					$bean->spiceacl_primary_territory = $spt;
					$bean->spiceacl_territories_hash = $sth;
				}
			}
			$oldUser = BeanFactory::getBean('Users', $userId);
			$oldAssignedUserMail = $oldUser->emailAddress->getPrimaryAddress($oldUser);
 
			$newReportingTo = BeanFactory::getBean('Users', $oldUser->reports_to_id);
			$newAssignedUserMail = $newReportingTo->emailAddress->getPrimaryAddress($newReportingTo);
 
			include_once('include/SugarPHPMailer.php');
			$mail = new SugarPHPMailer;
			$mail->Subject = "Прехвърляне на организация  $seed->name";
			$mail->setMailerForSystem();
			$mail->prepForOutbound();
			$date_modified = TimeDate::getInstance()->getNow()->asDb();
			$GLOBALS['log']->fatal(gettype($date_modified));
			$date_modified = date("d-m-Y H:i:m", strtotime($date_modified . " + 2 hours"));
			$date_modified = explode(" ", $date_modified);
			$date = $date_modified[0];
			$time = $date_modified[1];
			$mail->Body = " На $date г. в $time беше направено прехвърляне на Организация: \"$seed->name\", $seed->bulstat_c  от <strong>$oldUser->name</strong> към <strong>$user->name</strong>.<br>Прехвърлянето беше извършено от <strong>$current_user->name</strong>.";
			$mail->AddAddress($newAssignedUserMail);
			if ($userId != $current_user->id) {
				$mail->AddAddress($oldAssignedUserMail);
			}
			$mail->ContentType = "text/html";
			$mail->Send();
		}
	}
 
	private function getMunicipalityFromId($id){
		switch ($id) {
			case 'b85r4979-047c-b70c-4fd4-537b67c18f72':
				return 'admin';
              //  ...........
		}
	}
	private function getStateFromId($id){
		switch ($id) {
			case '3ef1100a-178a-8826-c1c5-5379gaa4ae86':
				return 'vidin';
			//..........
		}
	}
 
	function update_accounts_group_c($bean)	{
		$id = $bean->id;
 
		$newName =  $bean->bzlnk_groups_accounts_1_name;
		$bean->group_c = $newName;
 
		$GLOBALS['log']->fatal("account $id -> set fild group_c on $newName");
	}
 
	function update_accounts_filds($bean){
		$id = $bean->id;
 
		$bean->city_c = $bean->related_billing_city_c;
		$bean->municipality_c = $bean->address_municipality_c;
		$bean->area_c = $bean->address_area_c;
 
		$GLOBALS['log']->fatal("account $bean->name with id: $id -> set fild city_c on  $bean->related_billing_city_c");
		$GLOBALS['log']->fatal("account $bean->name with id: $id -> set fild municipality_c on  $bean->address_municipality_c");
		$GLOBALS['log']->fatal("account $bean->name with id: $id -> set fild area_c on  $bean->address_area_c");
	}
}
 