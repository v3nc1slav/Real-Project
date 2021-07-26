<?php
 
$hook_version = 1;
$hook_array = array();
// position, file, function 
$hook_array['before_save'] = array();
$hook_array['before_save'][] = array(1, 'set_groups', 'custom/modules/Accounts/_CustomLogicHooks/AccountsBeforeSave.php', 'AccountsBeforeSave', 'set_portfolio_if_new');
$hook_array['before_save'][] = array(2, 'Update in Accounts fild group_c', 'custom/modules/Accounts/_CustomLogicHooks/AccountsBeforeSave.php', 'AccountsBeforeSave', 'update_accounts_group_c');
$hook_array['before_save'][] = array(3, 'Update in Accounts filds city_c, municipality_c, area_c', 'custom/modules/Accounts/_CustomLogicHooks/AccountsBeforeSave.php', 'AccountsBeforeSave', 'update_accounts_filds');

// position, file, function 
$hook_array['after_save'] = array();