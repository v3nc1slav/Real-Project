<?PHP
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
 
/**
 * @copyright: 
 * @jira: L
 * @author: Ventsislav Verchov
 */
 
 
class CampaignContactApi extends SugarApi
{
    public function registerApiRest()
    {
        return array(
            'get_wallets_count_accounts' => array(
                'reqType' => 'GET',
                'path' => array('Campaigns', 'get-wallets-count-accounts'),
                'pathVars' => array('module', ''),
                'method' => 'get_wallets_count_accounts',
                'shortHelp' => 'Getting wallets from campaign',
            ),
            'get_all_wallet_count_without_call_accounts' => array(
                'reqType' => 'GET',
                'path' => array('Campaigns', 'get-wallets-count-without-call-accounts'),
                'pathVars' => array('module', ''),
                'method' => 'get_all_wallet_count_without_call_accounts',
                'shortHelp' => 'Getting wallets from campaign',
            ),
            'get_all_wallet_count_without_meeting_accounts' => array(
                'reqType' => 'GET',
                'path' => array('Campaigns', 'get-wallets-count-without-meeting-accounts'),
                'pathVars' => array('module', ''),
                'method' => 'get_all_wallet_count_without_meeting_accounts',
                'shortHelp' => 'Getting wallets from campaign',
            ),
            'get_wallets_count_by_result_accounts' => array(
                'reqType' => 'GET',
                'path' => array('Campaigns', 'get-wallets-count-by-result-accounts'),
                'pathVars' => array('module', ''),
                'method' => 'get_wallets_count_by_result_accounts',
                'shortHelp' => 'Getting wallets from campaign',
            ),
            'get_wallets_count_by_result_m_accounts' => array(
                'reqType' => 'GET',
                'path' => array('Campaigns', 'get-wallets-count-by-result-m-accounts'),
                'pathVars' => array('module', ''),
                'method' => 'get_wallets_count_by_result_m_accounts',
                'shortHelp' => 'Getting wallets from campaign',
            ),
            'get_wallet_accounts' => array(
                'reqType' => 'GET',
                'path' => array('Campaigns', 'get-wallet-accounts'),
                'pathVars' => array('module', ''),
                'method' => 'get_wallet_accounts',
                'shortHelp' => 'Getting wallets from campaign',
            ),
            'get_wallets_accounts' => array(
                'reqType' => 'GET',
                'path' => array('Campaigns', 'get-wallets-accounts'),
                'pathVars' => array('module', ''),
                'method' => 'get_wallets_accounts',
                'shortHelp' => 'Getting wallets from campaign',
            )
        );
    }
    public function get_wallets_count_by_result_m_accounts($api, $args){
        global $db,$current_user;
        $campaingId = $args['campaignId'];
        $result =  $args['result'];
        $contactsQuerry = "SELECT count(distinct(accounts_ld14_wallets_1accounts_ida)) as call_count 
        from ld14_wallets w
                LEFT JOIN ld14_wallets_cstm wc ON wc.id_c = w.id
                INNER JOIN accounts_ld14_wallets_1_c aw ON w.id = aw.accounts_ld14_wallets_1ld14_wallets_idb AND aw.deleted=0
                INNER JOIN campaigns_ld14_wallets_1_c cw ON cw.campaigns_ld14_wallets_1ld14_wallets_idb = w.id AND cw.deleted=0
                INNER JOIN campaigns cam ON cam.id = cw.campaigns_ld14_wallets_1campaigns_ida AND cw.deleted = 0
                INNER JOIN accounts a ON a.id= aw.accounts_ld14_wallets_1accounts_ida AND a.deleted = 0
                LEFT JOIN users u ON u.id=w.assigned_user_id
                WHERE w.deleted=0
        and campaigns_ld14_wallets_1campaigns_ida = '$campaingId'
        and wc.campaign_result_meeting_c = '$result'";
    if($current_user->isAdmin() != 1){
 
        $contactsQuerry .=" AND (SELECT COUNT( t.team_id)
        FROM accounts a1
        INNER JOIN team_sets_teams t ON t.team_set_id = a1.team_set_id
        WHERE a1.id = a.id
        AND t.team_id IN (SELECT t1.id FROM users u1
                left join team_memberships tm1 on u1.id=tm1.user_id and tm1.deleted=0
                left join teams t1 ON t1.id=tm1.team_id
                WHERE t1.deleted=0
                AND u1.id='$current_user->id'))>0";
    }
        if (empty($args['user_id']) == false) {
            $userid = $args['user_id'];
            $contactsQuerry .= " AND u.id='$userid'";
        }
        if ($args['call_number'] === "0" || empty($args['call_number']) == false) {
            $callNumber = $args['call_number'];
            $contactsQuerry .= " AND (
                     SELECT COUNT(DISTINCT(c1.id))
                FROM calls c1
                INNER JOIN campaigns_calls_1_c cc1 ON cc1.campaigns_calls_1calls_idb = c1.id AND cc1.deleted = 0
                INNER JOIN campaigns c ON c.id = cc1.campaigns_calls_1campaigns_ida AND c.deleted = 0
                INNER JOIN accounts a2 ON a2.id = c1.parent_id 
                WHERE c1.deleted=0  AND c1.status = 'Held' AND c.id ='$campaingId' and a2.id = a.id
                )=$callNumber";
        }
        if ($args['meeting_number'] === "0" || empty($args['meeting_number']) == false) {
            $meetingNumber = $args['meeting_number'];
            $contactsQuerry .= " AND (
                 SELECT COUNT(DISTINCT(m1.id))
                    FROM meetings m1
                    INNER JOIN campaigns_meetings_1_c cc1 ON cc1.campaigns_meetings_1meetings_idb = m1.id AND cc1.deleted = 0
                    INNER JOIN campaigns c ON c.id = cc1.campaigns_meetings_1campaigns_ida AND c.deleted = 0
                    INNER JOIN accounts a1 ON a1.id = m1.parent_id 
                    WHERE m1.deleted=0 AND m1.status = 'Held' AND c.id ='$campaingId' AND a1.id=a.id
                )=$meetingNumber";
        }
        if (empty($args['results']) == false) {
            $results = $args['results'];
            $contactsQuerry .= " AND w.campaign_result=$results";
        }
        if (empty($args['resultsm']) == false) {
            $resultsm = $args['resultsm'];
            $contactsQuerry .= " AND wc.campaign_result_meeting_c=$resultsm";
        }
 
        if (empty($args['team_id']) == false) {
            $team = $args['team_id'];
            $contactsQuerry .= " AND w.team_id='$team'";
        }
        $result = $db->query($contactsQuerry);
        return  $db->fetchByAssoc($result);
    }
    public function get_wallets_count_by_result_accounts($api, $args){
        global $db,$current_user;
        $campaingId = $args['campaignId'];
        $result =  $args['result'];
        $contactsQuerry = "SELECT count(distinct(accounts_ld14_wallets_1accounts_ida)) as call_count 
        from ld14_wallets w
                LEFT JOIN ld14_wallets_cstm lc ON lc.id_c = w.id
                INNER JOIN accounts_ld14_wallets_1_c aw ON w.id = aw.accounts_ld14_wallets_1ld14_wallets_idb AND aw.deleted=0
                INNER JOIN campaigns_ld14_wallets_1_c cw ON cw.campaigns_ld14_wallets_1ld14_wallets_idb = w.id AND cw.deleted=0
                INNER JOIN campaigns cam ON cam.id = cw.campaigns_ld14_wallets_1campaigns_ida AND cw.deleted = 0
                INNER JOIN accounts a ON a.id= aw.accounts_ld14_wallets_1accounts_ida AND a.deleted = 0
                LEFT JOIN users u ON u.id=w.assigned_user_id
                WHERE w.deleted=0
        and campaigns_ld14_wallets_1campaigns_ida = '$campaingId'
        and w.campaign_result = '$result'";
    if($current_user->isAdmin() != 1){
 
        $contactsQuerry .=" AND (SELECT COUNT( t.team_id)
        FROM accounts a1
        INNER JOIN team_sets_teams t ON t.team_set_id = a1.team_set_id
        WHERE a1.id = a.id
        AND t.team_id IN (SELECT t1.id FROM users u1
                left join team_memberships tm1 on u1.id=tm1.user_id and tm1.deleted=0
                left join teams t1 ON t1.id=tm1.team_id
                WHERE t1.deleted=0
                AND u1.id='$current_user->id'))>0";
    }
        if (empty($args['user_id']) == false) {
            $userid = $args['user_id'];
            $contactsQuerry .= " AND u.id='$userid'";
        }
        if ($args['call_number'] === "0" || empty($args['call_number']) == false) {
            $callNumber = $args['call_number'];
            $contactsQuerry .= " AND (
                     SELECT COUNT(DISTINCT(c1.id))
                FROM calls c1
                INNER JOIN campaigns_calls_1_c cc1 ON cc1.campaigns_calls_1calls_idb = c1.id AND cc1.deleted = 0
                INNER JOIN campaigns c ON c.id = cc1.campaigns_calls_1campaigns_ida AND c.deleted = 0
                INNER JOIN accounts a2 ON a2.id = c1.parent_id 
                WHERE c1.deleted=0 AND c1.`status` = 'Held' AND c.id ='$campaingId' and a2.id = a.id
                )=$callNumber";
        }
        if ($args['meeting_number'] === "0" || empty($args['meeting_number']) == false) {
            $meetingNumber = $args['meeting_number'];
            $contactsQuerry .= " AND (
                 SELECT COUNT(DISTINCT(m1.id))
                    FROM meetings m1
                    INNER JOIN campaigns_meetings_1_c cc1 ON cc1.campaigns_meetings_1meetings_idb = m1.id AND cc1.deleted = 0
                    INNER JOIN campaigns c ON c.id = cc1.campaigns_meetings_1campaigns_ida AND c.deleted = 0
                    INNER JOIN accounts a1 ON a1.id = m1.parent_id 
                    WHERE m1.deleted=0 AND m1.status = 'Held' AND c.id ='$campaingId' AND a1.id=a.id
                )=$meetingNumber";
        }
        if (empty($args['results']) == false) {
            $results = $args['results'];
            $contactsQuerry .= " AND w.campaign_result=$results";
        }
        if (empty($args['resultsm']) == false) {
            $resultsm = $args['resultsm'];
            $contactsQuerry .= " AND lc.campaign_result_meeting_c=$resultsm";
        }
        if (empty($args['team_id']) == false) {
            $team = $args['team_id'];
            $contactsQuerry .= " AND w.team_id='$team'";
        }
        $result = $db->query($contactsQuerry);
        return  $db->fetchByAssoc($result);
    }

    public function get_all_wallet_count_without_call_accounts($api, $args)
    {
        $campaingId = $args['campaignId'];
        global $db, $current_user;
        $contactsQuerry = "SELECT count(distinct(a.id)) as call_count
        FROM ld14_wallets w
        LEFT JOIN ld14_wallets_cstm lc ON lc.id_c = w.id
        INNER JOIN accounts_ld14_wallets_1_c aw ON w.id = aw.accounts_ld14_wallets_1ld14_wallets_idb AND aw.deleted=0
        INNER JOIN accounts a ON a.id= aw.accounts_ld14_wallets_1accounts_ida and a.deleted = 0
        INNER JOIN campaigns_ld14_wallets_1_c cw ON cw.campaigns_ld14_wallets_1ld14_wallets_idb = w.id AND cw.deleted=0
        INNER JOIN campaigns cam ON cam.id = cw.campaigns_ld14_wallets_1campaigns_ida AND cw.deleted = 0
        LEFT JOIN users u ON u.id=w.assigned_user_id
        WHERE w.deleted=0 AND cam.deleted=0 AND cam.id='$campaingId'
        and
        (
        SELECT COUNT(DISTINCT(c1.id))
        FROM calls c1
        INNER JOIN campaigns_calls_1_c cc1 ON cc1.campaigns_calls_1calls_idb = c1.id AND cc1.deleted = 0
        INNER JOIN accounts a1 ON a1.id = c1.parent_id
        WHERE a1.deleted=0 AND c1.status = 'Held' AND cc1.campaigns_calls_1campaigns_ida = '$campaingId' AND a1.id=a.id
        ) = 0";
 
   if($current_user->isAdmin() != 1){

    $contactsQuerry .=" AND (SELECT COUNT( t.team_id)
    FROM accounts a1
    INNER JOIN team_sets_teams t ON t.team_set_id = a1.team_set_id
    WHERE a1.id = a.id
    AND t.team_id IN (SELECT t1.id FROM users u1
            left join team_memberships tm1 on u1.id=tm1.user_id and tm1.deleted=0
            left join teams t1 ON t1.id=tm1.team_id
            WHERE t1.deleted=0
            AND u1.id='$current_user->id'))>0";
}
        if (empty($args['user_id']) == false) {
            $userid = $args['user_id'];
            $contactsQuerry .= " AND u.id='$userid'";
        }
        if ($args['call_number'] === "0" || empty($args['call_number']) == false) {
            $callNumber = $args['call_number'];
            $contactsQuerry .= " AND (
                SELECT COUNT(DISTINCT(c1.id))
                FROM calls c1
                INNER JOIN campaigns_calls_1_c cc1 ON cc1.campaigns_calls_1calls_idb = c1.id AND cc1.deleted = 0
                INNER JOIN campaigns c ON c.id = cc1.campaigns_calls_1campaigns_ida AND c.deleted = 0
                INNER JOIN accounts a2 ON a2.id = c1.parent_id 
                WHERE c1.deleted=0 AND c1.`status` = 'Held' AND c.id ='$campaingId' and a2.id = a.id
                )=$callNumber";
        }
        if ($args['meeting_number'] === "0" || empty($args['meeting_number']) == false) {
            $meetingNumber = $args['meeting_number'];
            $contactsQuerry .= " AND (
                 SELECT COUNT(DISTINCT(m1.id))
                    FROM meetings m1
                    INNER JOIN campaigns_meetings_1_c cc1 ON cc1.campaigns_meetings_1meetings_idb = m1.id AND cc1.deleted = 0
                    INNER JOIN campaigns c ON c.id = cc1.campaigns_meetings_1campaigns_ida AND c.deleted = 0
                    INNER JOIN accounts a1 ON a1.id = m1.parent_id 
                    WHERE m1.deleted=0 AND m1.status = 'Held' AND c.id ='$campaingId' AND a1.id=a.id
                )=$meetingNumber";
        }
        if (empty($args['results']) == false) {
            $results = $args['results'];
            $contactsQuerry .= " AND w.campaign_result=$results";
        }
        if (empty($args['resultsm']) == false) {
            $resultsm = $args['resultsm'];
            $contactsQuerry .= " AND lc.campaign_result_meeting_c=$resultsm";
        }
        if (empty($args['team_id']) == false) {
            $team = $args['team_id'];
            $contactsQuerry .= " AND w.team_id='$team'";
        }
 
        $result = $db->query($contactsQuerry);
        return  $db->fetchByAssoc($result);
    }

    public function get_wallets_count_accounts($api, $args)
    {
        $campaingId = $args['campaignId'];
        global $db,$current_user;
        $contactsQuerry = "SELECT COUNT(DISTINCT(accounts_ld14_wallets_1accounts_ida)) AS call_count
        FROM ld14_wallets w
        LEFT JOIN ld14_wallets_cstm lc ON lc.id_c = w.id
        INNER JOIN accounts_ld14_wallets_1_c aw ON w.id = aw.accounts_ld14_wallets_1ld14_wallets_idb AND aw.deleted=0
        INNER JOIN campaigns_ld14_wallets_1_c cw ON cw.campaigns_ld14_wallets_1ld14_wallets_idb = w.id AND cw.deleted=0
        INNER JOIN campaigns cam ON cam.id = cw.campaigns_ld14_wallets_1campaigns_ida AND cw.deleted = 0
        INNER JOIN accounts a ON a.id= aw.accounts_ld14_wallets_1accounts_ida
        WHERE w.deleted=0
        and campaigns_ld14_wallets_1campaigns_ida = '$campaingId'";
    if($current_user->isAdmin() != 1){
 
        $contactsQuerry .=" AND (SELECT COUNT( t.team_id)
        FROM accounts a1
        INNER JOIN team_sets_teams t ON t.team_set_id = a1.team_set_id
        WHERE a1.id = a.id
        AND t.team_id IN (SELECT t1.id FROM users u1
                left join team_memberships tm1 on u1.id=tm1.user_id and tm1.deleted=0
                left join teams t1 ON t1.id=tm1.team_id
                WHERE t1.deleted=0
                AND u1.id='$current_user->id'))>0";
    }
        if (empty($args['user_id']) == false) {
            $userid = $args['user_id'];
            $contactsQuerry .= " AND u.id='$userid'";
        }
        if ($args['call_number'] === "0" || empty($args['call_number']) == false) {
            $callNumber = $args['call_number'];
            $contactsQuerry .= " AND (
                 SELECT COUNT(DISTINCT(c1.id))
                    FROM calls c1
                    INNER JOIN campaigns_calls_1_c cc1 ON cc1.campaigns_calls_1calls_idb = c1.id AND cc1.deleted = 0
                    INNER JOIN campaigns c ON c.id = cc1.campaigns_calls_1campaigns_ida AND c.deleted = 0
                    INNER JOIN accounts a1 ON a1.id = c1.parent_id 
                    WHERE c1.deleted=0 AND c1.`status` = 'Held' AND c.id ='$campaingId' AND a1.id=a.id
                )=$callNumber";
        }
        if ($args['meeting_number'] === "0" || empty($args['meeting_number']) == false) {
            $meetingNumber = $args['meeting_number'];
            $contactsQuerry .= " AND (
                 SELECT COUNT(DISTINCT(m1.id))
                    FROM meetings m1
                    INNER JOIN campaigns_meetings_1_c cc1 ON cc1.campaigns_meetings_1meetings_idb = m1.id AND cc1.deleted = 0
                    INNER JOIN campaigns c ON c.id = cc1.campaigns_meetings_1campaigns_ida AND c.deleted = 0
                    INNER JOIN accounts a1 ON a1.id = m1.parent_id 
                    WHERE m1.deleted=0 AND m1.status = 'Held' AND c.id ='$campaingId' AND a1.id=a.id
                )=$meetingNumber";
        }
        if (empty($args['results']) == false) {
            $results = $args['results'];
            $contactsQuerry .= " AND w.campaign_result=$results";
        }
        if (empty($args['resultsm']) == false) {
            $resultsm = $args['resultsm'];
            $contactsQuerry .= " AND lc.campaign_result_meeting_c=$resultsm";
        }
        if (empty($args['team_id']) == false) {
            $team = $args['team_id'];
            $contactsQuerry .= " AND w.team_id='$team'";
        }
        $result = $db->query($contactsQuerry);
        return  $db->fetchByAssoc($result);
    }
 
    public function get_wallet_accounts($api, $args)
    {
        $campaingId = $args['campaignId'];
        $walletId = $args['walletId'];
        global $db, $current_user;
        $contactsQuerry = "SELECT 
        a.name AS accName,
        w.id AS wallet_id,
        lc.campaign_result_meeting_c AS meet_res,
        lc.description_meeting_c AS meet_desc,
        w.campaign_result AS wallet_result,
        w.description AS wallet_description,
        u.id AS u_id,
        u.user_name AS u_name,
        a.id AS accId,
        (SELECT cl1.activity_date
            FROM campaign_log cl1
           where cl1.campaign_id= a.id and cl1.target_id=a.id and cl1.activity_type='targeted' and cl1.deleted=0
           order by cl1.activity_date desc
           limit 1) as last_date, 
        (
            SELECT COUNT(DISTINCT(m1.id))
            FROM meetings m1
           INNER JOIN campaigns_meetings_1_c cm1 ON cm1.campaigns_meetings_1meetings_idb = m1.id AND cm1.deleted = 0
           INNER JOIN accounts a1 ON a1.id = m1.parent_id
           WHERE a1.deleted=0 AND m1.`status` = 'Held' AND cm1.campaigns_meetings_1campaigns_ida = '$campaingId' AND a1.id=a.id
        ) AS meeting_number,
        (SELECT COUNT(DISTINCT(c1.id))
            FROM calls c1
           INNER JOIN campaigns_calls_1_c cc1 ON cc1.campaigns_calls_1calls_idb = c1.id AND cc1.deleted = 0
           INNER JOIN accounts a1 ON a1.id = c1.parent_id
           WHERE a1.deleted=0 AND c1.`status` = 'Held' AND cc1.campaigns_calls_1campaigns_ida = '$campaingId' AND a1.id=a.id
        ) AS call_number,
        (SELECT c2.id
           FROM calls c2
           INNER JOIN campaigns_calls_1_c cc2 ON cc2.campaigns_calls_1calls_idb = c2.id AND cc2.deleted = 0
           WHERE c2.deleted = 0 AND c2.`status` = 'Planned' AND c2.parent_id=a.id AND cc2.campaigns_calls_1campaigns_ida = '$campaingId'
           ORDER BY c2.date_start ASC
           LIMIT 1 
        ) AS planned_call_id,
        (
        SELECT m2.id
        FROM meetings m2
        INNER JOIN campaigns_meetings_1_c cm2 ON cm2.campaigns_meetings_1meetings_idb = m2.id AND cm2.deleted = 0
        WHERE m2.deleted = 0 AND m2.`status` = 'Planned' AND m2.parent_id=a.id AND cm2.campaigns_meetings_1campaigns_ida = '$campaingId'
        ORDER BY m2.date_start ASC
        LIMIT 1 )planned_meeting_id,
        (
        SELECT c3.name
           FROM calls c3
           INNER JOIN campaigns_calls_1_c cc3 ON cc3.campaigns_calls_1calls_idb = c3.id AND cc3.deleted = 0
           WHERE c3.deleted = 0 AND c3.`status` = 'Planned' AND c3.parent_id=a.id AND cc3.campaigns_calls_1campaigns_ida = '$campaingId'
           ORDER BY c3.date_start ASC
           LIMIT 1
        ) AS planned_call_name,
        (
           SELECT m3.name
           FROM meetings m3
           INNER JOIN campaigns_meetings_1_c cm3 ON cm3.campaigns_meetings_1meetings_idb= m3.id AND cm3.deleted = 0
           WHERE m3.deleted = 0 AND m3.`status` = 'Planned' AND m3.parent_id=a.id AND cm3.campaigns_meetings_1campaigns_ida = '$campaingId'
           ORDER BY m3.date_start ASC
           LIMIT 1)
           AS planned_meeting_name,
        (
        SELECT c4.id
           FROM calls c4
           INNER JOIN campaigns_calls_1_c cc4 ON cc4.campaigns_calls_1calls_idb = c4.id AND cc4.deleted = 0
           WHERE c4.deleted = 0 AND c4.`status` = 'Held' AND c4.parent_id=a.id AND cc4.campaigns_calls_1campaigns_ida ='$campaingId'
           ORDER BY c4.date_end DESC
            LIMIT 1
        ) AS held_call_id,
        (SELECT m4.id
           FROM meetings m4
           INNER JOIN campaigns_meetings_1_c cm4 ON cm4.campaigns_meetings_1meetings_idb = m4.id AND cm4.deleted = 0
           WHERE m4.deleted = 0 AND m4.`status` = 'Held' AND m4.parent_id=a.id AND cm4.campaigns_meetings_1campaigns_ida ='$campaingId'
           ORDER BY m4.date_end DESC
            LIMIT 1) AS held_meeting_id,
        (
        SELECT c5.name
           FROM calls c5
           INNER JOIN campaigns_calls_1_c cc5 ON cc5.campaigns_calls_1calls_idb = c5.id AND cc5.deleted = 0
           WHERE c5.deleted = 0 AND c5.`status` = 'Held' AND c5.parent_id=a.id AND cc5.campaigns_calls_1campaigns_ida ='$campaingId'
           ORDER BY c5.date_end DESC
           LIMIT 1
        ) AS held_call_name,
        (SELECT m5.name
           FROM meetings m5
           INNER JOIN campaigns_meetings_1_c cm5 ON cm5.campaigns_meetings_1meetings_idb = m5.id AND cm5.deleted = 0
           WHERE m5.deleted = 0 AND m5.`status` = 'Held' AND m5.parent_id=a.id AND cm5.campaigns_meetings_1campaigns_ida ='$campaingId'
           ORDER BY m5.date_end DESC
           LIMIT 1) AS held_meeting_name
        FROM ld14_wallets w
        LEFT JOIN ld14_wallets_cstm lc ON lc.id_c = w.id
        INNER JOIN campaigns_ld14_wallets_1_c cw ON cw.campaigns_ld14_wallets_1ld14_wallets_idb = w.id AND cw.deleted=0
        INNER JOIN campaigns cam ON cam.id = cw.campaigns_ld14_wallets_1campaigns_ida AND cam.deleted=0
        LEFT JOIN users u ON u.id=w.assigned_user_id
        INNER JOIN accounts_ld14_wallets_1_c ac ON ac.accounts_ld14_wallets_1ld14_wallets_idb = w.id AND ac.deleted = 0
        INNER JOIN accounts a ON a.id=ac.accounts_ld14_wallets_1accounts_ida
        INNER JOIN campaigns_ld14_wallets_1_c cl ON cl.campaigns_ld14_wallets_1ld14_wallets_idb =  w.id AND cl.deleted = 0
        WHERE w.deleted=0 
      AND cam.deleted=0
        AND cam.id='$campaingId'
        AND w.id = '$walletId'";
        $result = $db->query($contactsQuerry);
        return  $db->fetchByAssoc($result);
    }
 
    public function get_wallets_accounts($api, $args)
    {
        $campaingId = $args['campaignId'];
        $walletId = $args['walletId'];
        global $db, $current_user;
        $contactsQuerry = "SELECT 
        a.id AS accId,
        a.name AS accName,
        a.assigned_user_id AS assUser, 
        l.name AS ldName,
        l.date_entered as created_on,
        l.id AS wallet_id,
        lc.campaign_result_meeting_c AS meet_res,
        lc.description_meeting_c AS meet_desc,
        l.campaign_result AS wallet_result,
        l.description AS wallet_description,
        u.id AS u_id,
        u.user_name AS u_name,
        cam.id As campaignId,
        cam.name As campaignName,
        (SELECT cl1.activity_date
            FROM campaign_log cl1
           where cl1.campaign_id= a.id and cl1.target_id=a.id and cl1.activity_type='targeted' and cl1.deleted=0
           order by cl1.activity_date desc
           limit 1) as last_date, 
        (
            SELECT COUNT(DISTINCT(m1.id))
            FROM meetings m1
           INNER JOIN campaigns_meetings_1_c cm1 ON cm1.campaigns_meetings_1meetings_idb = m1.id AND cm1.deleted = 0
           INNER JOIN accounts a1 ON a1.id = m1.parent_id
           WHERE a1.deleted=0 AND m1.`status` = 'Held' AND cm1.campaigns_meetings_1campaigns_ida = '$campaingId' AND a1.id=a.id
        ) AS meeting_number,
        (SELECT COUNT(DISTINCT(c1.id))
            FROM calls c1
           INNER JOIN campaigns_calls_1_c cc1 ON cc1.campaigns_calls_1calls_idb = c1.id AND cc1.deleted = 0
           INNER JOIN accounts a1 ON a1.id = c1.parent_id
           WHERE a1.deleted=0 AND c1.`status` = 'Held' AND cc1.campaigns_calls_1campaigns_ida = '$campaingId' AND a1.id=a.id
        ) AS call_number,
        (SELECT c2.id
           FROM calls c2
           INNER JOIN campaigns_calls_1_c cc2 ON cc2.campaigns_calls_1calls_idb = c2.id AND cc2.deleted = 0
           WHERE c2.deleted = 0 AND c2.`status` = 'Planned' AND c2.parent_id=a.id AND cc2.campaigns_calls_1campaigns_ida = '$campaingId'
           ORDER BY c2.date_start ASC
           LIMIT 1 
        ) AS planned_call_id,
        (
        SELECT m2.id
        FROM meetings m2
        INNER JOIN campaigns_meetings_1_c cm2 ON cm2.campaigns_meetings_1meetings_idb = m2.id AND cm2.deleted = 0
        WHERE m2.deleted = 0 AND m2.`status` = 'Planned' AND m2.parent_id=a.id AND cm2.campaigns_meetings_1campaigns_ida = '$campaingId'
        ORDER BY m2.date_start ASC
        LIMIT 1 )planned_meeting_id,
        (
        SELECT c3.name
           FROM calls c3
           INNER JOIN campaigns_calls_1_c cc3 ON cc3.campaigns_calls_1calls_idb = c3.id AND cc3.deleted = 0
           WHERE c3.deleted = 0 AND c3.`status` = 'Planned' AND c3.parent_id=a.id AND cc3.campaigns_calls_1campaigns_ida = '$campaingId'
           ORDER BY c3.date_start ASC
           LIMIT 1
        ) AS planned_call_name,
        (
           SELECT m3.name
           FROM meetings m3
           INNER JOIN campaigns_meetings_1_c cm3 ON cm3.campaigns_meetings_1meetings_idb= m3.id AND cm3.deleted = 0
           WHERE m3.deleted = 0 AND m3.`status` = 'Planned' AND m3.parent_id=a.id AND cm3.campaigns_meetings_1campaigns_ida = '$campaingId'
           ORDER BY m3.date_start ASC
           LIMIT 1)
           AS planned_meeting_name,
        (
        SELECT c4.id
           FROM calls c4
           INNER JOIN campaigns_calls_1_c cc4 ON cc4.campaigns_calls_1calls_idb = c4.id AND cc4.deleted = 0
           WHERE c4.deleted = 0 AND c4.`status` = 'Held' AND c4.parent_id=a.id AND cc4.campaigns_calls_1campaigns_ida ='$campaingId'
           ORDER BY c4.date_end DESC
            LIMIT 1
        ) AS held_call_id,
        (SELECT m4.id
           FROM meetings m4
           INNER JOIN campaigns_meetings_1_c cm4 ON cm4.campaigns_meetings_1meetings_idb = m4.id AND cm4.deleted = 0
           WHERE m4.deleted = 0 AND m4.`status` = 'Held' AND m4.parent_id=a.id AND cm4.campaigns_meetings_1campaigns_ida ='$campaingId'
           ORDER BY m4.date_end DESC
            LIMIT 1) AS held_meeting_id,
        (
        SELECT c5.name
           FROM calls c5
           INNER JOIN campaigns_calls_1_c cc5 ON cc5.campaigns_calls_1calls_idb = c5.id AND cc5.deleted = 0
           WHERE c5.deleted = 0 AND c5.`status` = 'Held' AND c5.parent_id=a.id AND cc5.campaigns_calls_1campaigns_ida ='$campaingId'
           ORDER BY c5.date_end DESC
           LIMIT 1
        ) AS held_call_name,
        (SELECT m5.name
           FROM meetings m5
           INNER JOIN campaigns_meetings_1_c cm5 ON cm5.campaigns_meetings_1meetings_idb = m5.id AND cm5.deleted = 0
           WHERE m5.deleted = 0 AND m5.`status` = 'Held' AND m5.parent_id=a.id AND cm5.campaigns_meetings_1campaigns_ida ='$campaingId'
           ORDER BY m5.date_end DESC
           LIMIT 1) AS held_meeting_name
        FROM accounts a
        INNER JOIN accounts_ld14_wallets_1_c al ON al.accounts_ld14_wallets_1accounts_ida = a.id AND al.deleted = 0
        INNER JOIN ld14_wallets l ON l.id = al.accounts_ld14_wallets_1ld14_wallets_idb AND l.deleted = 0
        LEFT JOIN ld14_wallets_cstm lc ON lc.id_c = l.id 
        INNER JOIN campaigns_ld14_wallets_1_c cl ON cl.campaigns_ld14_wallets_1ld14_wallets_idb =  l.id AND cl.deleted = 0
        INNER JOIN campaigns cam ON cam.id = cl.campaigns_ld14_wallets_1campaigns_ida AND cam.deleted=0
        LEFT JOIN users u ON u.id=l.assigned_user_id AND u.deleted = 0
        WHERE cam.id = '$campaingId' AND a.deleted = 0";
 
    if($current_user->isAdmin() != 1){
 
        $contactsQuerry .=" AND (SELECT COUNT( t.team_id)
        FROM accounts a1
        INNER JOIN team_sets_teams t ON t.team_set_id = a1.team_set_id
        WHERE a1.id = a.id
        AND t.team_id IN (SELECT t1.id FROM users u1
                left join team_memberships tm1 on u1.id=tm1.user_id and tm1.deleted=0
                left join teams t1 ON t1.id=tm1.team_id
                WHERE t1.deleted=0
                AND u1.id='$current_user->id'))>0";
    }
 
    if (empty($args['user_id']) == false) {
        $userid = $args['user_id'];
        $contactsQuerry .= " AND u.id='$userid'";
    }
 
    if ($args['call_number'] === "0" || empty($args['call_number']) == false) {
        $callNumber = $args['call_number'];
        $contactsQuerry .= " AND (
             SELECT COUNT(DISTINCT(c1.id))
                FROM calls c1
                INNER JOIN campaigns_calls_1_c cc1 ON cc1.campaigns_calls_1calls_idb = c1.id AND cc1.deleted = 0
                INNER JOIN campaigns c ON c.id = cc1.campaigns_calls_1campaigns_ida AND c.deleted = 0
                INNER JOIN accounts a1 ON a1.id = c1.parent_id 
                WHERE c1.deleted=0 AND c1.`status` = 'Held' AND c.id ='$campaingId' AND a1.id=a.id
            )=$callNumber";
    }
 
    if ($args['meeting_number'] === "0" || empty($args['meeting_number']) == false) {
        $meetingNumber = $args['meeting_number'];
        $contactsQuerry .= " AND (
             SELECT COUNT(DISTINCT(m1.id))
                FROM meetings m1
                INNER JOIN campaigns_meetings_1_c cc1 ON cc1.campaigns_meetings_1meetings_idb = m1.id AND cc1.deleted = 0
                INNER JOIN campaigns c ON c.id = cc1.campaigns_meetings_1campaigns_ida AND c.deleted = 0
                INNER JOIN accounts a1 ON a1.id = m1.parent_id 
                WHERE m1.deleted=0  AND m1.status = 'Held'  AND c.id ='$campaingId' AND a1.id=a.id
            )=$meetingNumber";
    }
 
    if (empty($args['results']) == false) {
        $results = $args['results'];
        $contactsQuerry .= " AND l.campaign_result=$results";
    }
    if (empty($args['resultsm']) == false) {
        $resultsm = $args['resultsm'];
        $contactsQuerry .= " AND lc.campaign_result_meeting_c=$resultsm";
    }
 
    if (empty($args['acc_id']) == false) {
        $account = $args['acc_id'];
        $contactsQuerry .= " AND a.id='$account'";
    }
 
    if (empty($args['team_id']) == false) {
        $team = $args['team_id'];
        $contactsQuerry .= " AND l.team_id='$team'";
    }
        $result = $db->query($contactsQuerry);
        $dataArr = [];
        while ($row = $db->fetchByAssoc($result)) {
            $row['created_on'] = date("d-m-Y", strtotime($row['created_on']));
            $dataArr[] = $row;
        }
        return $dataArr;
    }
 
    public function get_all_wallet_count_without_meeting_accounts($api, $args)
    {
        $campaingId = $args['campaignId'];
        global $db, $current_user;
        $contactsQuerry = "SELECT count(distinct(a.id)) as meeting_count
        FROM ld14_wallets w
        LEFT JOIN ld14_wallets_cstm lc ON lc.id_c = w.id
        INNER JOIN accounts_ld14_wallets_1_c aw ON w.id = aw.accounts_ld14_wallets_1ld14_wallets_idb AND aw.deleted=0
        INNER JOIN accounts a ON a.id= aw.accounts_ld14_wallets_1accounts_ida and a.deleted = 0
        INNER JOIN campaigns_ld14_wallets_1_c cw ON cw.campaigns_ld14_wallets_1ld14_wallets_idb = w.id AND cw.deleted=0
        INNER JOIN campaigns cam ON cam.id = cw.campaigns_ld14_wallets_1campaigns_ida AND cam.deleted=0
        LEFT JOIN users u ON u.id=w.assigned_user_id
        WHERE w.deleted=0 AND cam.deleted=0 AND cam.id='$campaingId'
        and
        (
        SELECT COUNT(DISTINCT(m1.id))
        FROM meetings m1
        INNER JOIN campaigns_meetings_1_c cm1 ON cm1.campaigns_meetings_1meetings_idb = m1.id AND cm1.deleted = 0
        INNER JOIN accounts a1 ON a1.id = m1.parent_id
        WHERE a1.deleted=0 AND m1.status = 'Held' AND cm1.campaigns_meetings_1campaigns_ida = '$campaingId' AND a1.id=a.id
        ) = 0"
		;
 
        if($current_user->isAdmin() != 1){
            // $contactsQuerry .=" AND a.team_id IN (SELECT t1.id FROM users u1
            // left join team_memberships tm1 on u1.id=tm1.user_id and tm1.deleted=0
            // left join teams t1 ON t1.id=tm1.team_id
            // WHERE t1.deleted=0
            // AND u1.id='$current_user->id')";
 
            $contactsQuerry .=" AND (SELECT COUNT( t.team_id)
            FROM accounts a1
            INNER JOIN team_sets_teams t ON t.team_set_id = a1.team_set_id
            WHERE a1.id = a.id
            AND t.team_id IN (SELECT t1.id FROM users u1
                    left join team_memberships tm1 on u1.id=tm1.user_id and tm1.deleted=0
                    left join teams t1 ON t1.id=tm1.team_id
                    WHERE t1.deleted=0
                    AND u1.id='$current_user->id'))>0";
        }
        if (empty($args['user_id']) == false) {
            $userid = $args['user_id'];
            $contactsQuerry .= " AND u.id='$userid'";
        }
        if ($args['call_number'] === "0" || empty($args['call_number']) == false) {
            $callNumber = $args['call_number'];
            $contactsQuerry .= " AND (
                SELECT COUNT(DISTINCT(c1.id))
                FROM calls c1
                INNER JOIN campaigns_calls_1_c cc1 ON cc1.campaigns_calls_1calls_idb = c1.id AND cc1.deleted = 0
                INNER JOIN campaigns c ON c.id = cc1.campaigns_calls_1campaigns_ida AND c.deleted = 0
                INNER JOIN accounts a2 ON a2.id = c1.parent_id 
                WHERE c1.deleted=0 AND c1.`status` = 'Held' AND c.id ='$campaingId' and a2.id = a.id
                )=$callNumber";
        }
 
        if ($args['meeting_number'] === "0" || empty($args['meeting_number']) == false) {
            $meetingNumber = $args['meeting_number'];
            $contactsQuerry .= " AND (
                 SELECT COUNT(DISTINCT(m1.id))
                    FROM meetings m1
                    INNER JOIN campaigns_meetings_1_c cc1 ON cc1.campaigns_meetings_1meetings_idb = m1.id AND cc1.deleted = 0
                    INNER JOIN campaigns c ON c.id = cc1.campaigns_meetings_1campaigns_ida AND c.deleted = 0
                    INNER JOIN accounts a1 ON a1.id = m1.parent_id 
                    WHERE m1.deleted=0 AND m1.status = 'Held' AND c.id ='$campaingId' AND a1.id=a.id
                )=$meetingNumber";
        }
        if (empty($args['results']) == false) {
            $results = $args['results'];
            $contactsQuerry .= " AND w.campaign_result=$results";
        }
        if (empty($args['resultsm']) == false) {
            $resultsm = $args['resultsm'];
            $contactsQuerry .= " AND lc.campaign_result_meeting_c=$resultsm";
        }
        //$GLOBALS['log']->fatal($args['team_id']);
        if (empty($args['team_id']) == false) {
            $team = $args['team_id'];
            $contactsQuerry .= " AND w.team_id='$team'";
        }
 
        $result = $db->query($contactsQuerry);
        return  $db->fetchByAssoc($result);
    }
 
}