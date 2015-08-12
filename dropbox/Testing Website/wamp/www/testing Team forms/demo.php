<?php 

  // speed things up with gzip, ob_start() required for csv downloads 
if(!ob_start('ob_gzhandler')) 
    ob_start(); 
  
header('Content-Type: text/html; charset=utf-8'); 
include('home.php');   
include('masterlist.php'); 

  
echo " 
<html> 
<head> 
    <meta charset='UTF-8' /> 
    <link rel='stylesheet' type='text/css' href='style.css'  /> 
  <script src='//code.jquery.com/jquery-1.10.2.js'></script>
</head> 
<body> 
";  
  
  
// enter your database host, name, username, and password 
$db_host = 'localhost'; 
$db_name = 'mysql'; 
$db_user = 'root'; 
$db_pass = ''; 
  
  
// connect with pdo  
try { 
    $dbh = new PDO("mysql:host=$db_host;dbname=$db_name;", $db_user, $db_pass); 
} 
catch(PDOException $e) { 
    die('pdo connection error: ' . $e->getMessage()); 
} 
  
  
// create LM object, pass in PDO connection 
$lm = new lazy_mofo($dbh);  
  
  
// table name for updates, inserts and deletes 
$lm->table = 'masterlistnew'; 
  
  
// identity / primary key for table 
$lm->identity_name = 'test_id'; 
  
  
// optional, define grid sort order 
$lm->grid_default_order_by = 'test_id desc'; 
  
  
  

//$lm->rename = array('testing_id' => 'Testing Platform'); 
  
//$lm->rename = array('test_status_id' => 'Test Status'); 
 //$lm->rename = array('sites' => 'Site Section'); 
  

$lm->rename = array('testing_id' => 'Testing Platform','test_status_id' => 'Test Status','sites' => 'Site Section','text'=>'Test name','actual_launch_date'=>'Launch Date','target_end_date'=>'Deactivate Date'); 
  
//$lm->rename = array('test_status_id' => 'Test Status'); 

$lm->form_input_control = array('photo' => '--image', 'testing_id' => 'select testing_id,testing_platform from testing; --select', 'test_status_id' => 'select test_status_id,test_status from test; --select'); 
  
  
// optional, define editable input controls on the grid 
$lm->grid_input_control = array('is_active' => '--checkbox'); 
  
  
// optional, define output control on the grid; make email clickable and the photo a clickable link 
$lm->grid_output_control = array('wiki_page' => '--var','JIRA' => '--var','text' => '--headcol','test_id' => '--headcol1','current_comment' => '--comment','mbox_name'=>'--comment','requested_party' => '--contact','learnings' => '--learnings'); 
  
  
// optional, query for grid(). if the last column selected is the primary key identity, then the [edit] and [delete] links are displayed 
$lm->grid_sql = "select m.text,m.test_id ,m.site,m.wiki_page,m.sites, m.JIRA,t.testing_platform , m.requested_party ,m.testing_team_lead,m.FED_DAC ,m.test_owner,m.priority,m.actual_launch_date,m.target_end_date, c.test_status, m.current_comment, m.begin_date, m.end_date, m.winning_experience_name, m.final_success_metric, m.control_baseline, m.best_alternative, m.lift_, m.confidence_level, m.alternative_exp_implemented, m.outcome, m.annualized_benefit,m.mbox_name, m.learnings, from masterlistnew m 
left join test c on m.test_status_id = c.test_status_id left join testing t on m.testing_id = t.testing_id
order by m.test_id desc"; 
  
  
// optional, define what is displayed on edit form 
$lm->form_sql = 'select text,test_id ,site, wiki_page,sites, JIRA,testing_id ,requested_party,testing_team_lead ,FED_DAC , test_owner,priority,actual_launch_date,target_end_date,test_status_id, current_comment, begin_date, end_date, winning_experience_name,final_success_metric, control_baseline, best_alternative, lift_, confidence_level, alternative_exp_implemented, outcome, annualized_benefit,mbox_name, learnings from masterlistnew where test_id = :test_id'; 
$lm->form_sql_param = array(':test_id' => intval(@$_REQUEST['test_id'])); 


  
$lm->grid_sql = "select m.text,m.test_id, m.site,m.wiki_page,m.sites, m.JIRA, t.testing_platform ,m.requested_party,m.testing_team_lead,m.FED_DAC ,m.test_owner,m.priority,m.actual_launch_date,m.target_end_date, c.test_status, m.current_comment, m.begin_date, m.end_date, m.winning_experience_name, m.final_success_metric, m.control_baseline, m.best_alternative, m.lift_, m.confidence_level, m.alternative_exp_implemented, m.outcome, m.annualized_benefit,m.mbox_name, m.learnings,m.test_id from 
    masterlistnew m 
    left   
    join   test c on m.test_status_id = c.test_status_id left join testing t on m.testing_id = t.testing_id
    where  coalesce(c.test_status, '') like :_search
    order  by m.test_id desc";
$lm->grid_sql_param[':_search'] = '%' . trim(@$_REQUEST['_search']) . '%';  







// optional, display a related table under the edit record form 
$lm->child_title = 'Sub Masters'; 
$lm->child_table = 'sub_master'; 
$lm->child_identity_name = 'sub_master_id'; 
$lm->child_parent_identity_name = 'test_id'; 
$lm->child_input_control = array('photo' => '--image'); 
  
// use the lm controller 
$lm->run(); 
  
  
echo "</body></html>";
?>

