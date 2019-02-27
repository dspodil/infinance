<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('libs/nusoap.php');



 
date_default_timezone_set('Europe/Kiev');

require_once 'include/config.php';



// Load the application page template

// Load the database handler

require_once  'database_handler.php'; 
DatabaseHandler::execute("SET time_zone = 'Europe/Kiev';");
 $sql="SELECT value FROM parametersSite where id=7";
 $mbkilogin=DatabaseHandler::GetOne($sql);
  $sql="SELECT value FROM parametersSite where id=8";
 $mbkipassword=DatabaseHandler::GetOne($sql);
  $sql="SELECT value FROM parametersSite where id=9";
 $pvbkilogin=DatabaseHandler::GetOne($sql);
  $sql="SELECT value FROM parametersSite where id=10";
 $pvbkipassword=DatabaseHandler::GetOne($sql);
 $sql="SELECT value FROM parametersSite where id=11";
 $ubkilogin=DatabaseHandler::GetOne($sql);
 $sql="SELECT value FROM parametersSite where id=12";
 $ubkipassword=DatabaseHandler::GetOne($sql);
$nameFolder=date('Ymd');
 $sql="SELECT value FROM parametersSite where id=3";
    $credit_history=DatabaseHandler::GetOne($sql); 
 
 $sql="SELECT value FROM parametersSite where id=3";
   $credit_history=DatabaseHandler::GetOne($sql); 
$sql="SELECT value FROM paramclosecreditperiod where id=1";
   $paramclosecreditperiod=DatabaseHandler::GetOne($sql); 


   
// 1 сервер
/*
 $server_url = '185.53.170.71';
  $usernameftp = 'pozichkadata';
   $passwordftp = 'pozichkadatA';
 $remote_folder = 'xml/ppr_automatic/in';
 */
 $server_url = 'ftp1.pozichka.ua';
  $usernameftp = 'infinance-ftp';
   $passwordftp = '646876a9-d8d6-4ca4-ae6a-7257350790dc';
 $remote_folder = 'xml/ppr_automatic/in';
 
if(is_dir("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in_archive/{$nameFolder}")==false)
	{
		mkdir("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in_archive/{$nameFolder}");
	}
	// set up basic connection
$conn_id = ftp_connect($server_url,21,3600 );

// login with username and password
$login_result = ftp_login($conn_id, $usernameftp , $passwordftp);
ftp_pasv($conn_id, true);

// get contents of the current directory
$contents = ftp_nlist($conn_id, "xml/ppr_automatic/in");

$arr=array();
foreach($contents  as $file) {
    if (($file!='..') and ($file!='.') and (preg_match('/\.xml$/',$file))){
		
		$arr[]=$file;
       
    }
}
// output $contents

 /*$sql="SELECT filename FROM Applications";
 $resArray=DatabaseHandler::GetAll($sql);
 $arrFile=array();
foreach($resArray  as $file) {
$arrFile[]=$file['filename'];

}

 
 $arrdiff=array_diff($arr,$arrFile);*/
 $arrdiff=$arr;

function get_basename($filename)
{
    return preg_replace('/^.+[\\\\\\/]/', '', $filename);
}
 foreach($arrdiff as $file){
	 $applicationid=0;
	 $AutoLim=0;
	 $mbkiemptyf=0;
	  $ubkiemptyf=0;
	 $file=get_basename($file);
	 $inn='';
	 $ident='';
	 $idubki=0;
	 
  $resultMatrixDesicionSec=0;
  $ZoneMatrix=0;
	 //print $file;
	$xml = simplexml_load_file("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}");
	
	$xmlfile=addslashes($xml->asXML());
	if (isset($xml->user_id) and ($xml->user_id<>'')) $user_id=$xml->user_id;//varchar(100)	
		else $user_id=0;
	if (isset($xml->ammout_of_credit) and ($xml->ammout_of_credit<>'')) $ammout_of_credit=$xml->ammout_of_credit;
	else $ammout_of_credit=0;
if (isset($xml->credit_term) and ($xml->credit_term<>'')) $credit_term=$xml->credit_term;
else $credit_term=0;
$date_of_ending=$xml->date_of_ending;
$purpose_of_the_loan=$xml->purpose_of_the_loan;
$application_date=date('Y-m-d', strtotime(str_replace('-', '.',$xml->application_date)));
$type=$xml->type;
$type_tranche=$xml->type_tranche;
if (isset($xml->percentage_of_the_previous_loan) and ($xml->percentage_of_the_previous_loan<>'')) $percentage_of_the_previous_loan=$xml->percentage_of_the_previous_loan;
else $percentage_of_the_previous_loan=0;
$payment_for_longation=$xml->payment_for_longation;
if (isset($xml->total_for_longation) and ($xml->total_for_longation<>'')) $total_for_longation=$xml->total_for_longation;
else $total_for_longation=0;
if (isset($xml->period_for_longation) and ($xml->period_for_longation<>'')) $period_for_longation=$xml->period_for_longation;
else $period_for_longation=0;
if (isset($xml->payment_balance) and ($xml->payment_balance<>'')) $payment_balance=$xml->payment_balance;
else $payment_balance=0;
$payment_balance=str_replace(',','.',$payment_balance);
$contract_number=$xml->contract_number;
$date_of_contract =$xml->date_of_contract ;
$promo_code=$xml->promo_code;
$master_promo_code=$xml->master_promo_code;
$pay_method=$xml->pay_method;
$sql="SET NAMES 'utf8'";
DatabaseHandler::Execute($sql);
 $sql="insert into CreditsClientsForCheckBKI values(null,$user_id,
 $ammout_of_credit,
 $credit_term,
 '$date_of_ending',
 '$purpose_of_the_loan',
 '$application_date',
 '$type',
 '$type_tranche',
 $percentage_of_the_previous_loan,
 '$payment_for_longation',
 $total_for_longation,
 $period_for_longation,
 $payment_balance,
 '$contract_number',
 '$date_of_contract',
 '$promo_code',
 '$master_promo_code',
 '$pay_method'
)";
print $sql;
  DatabaseHandler::Execute($sql);
$sql="SELECT LAST_INSERT_ID()";
$creditid=DatabaseHandler::GetOne($sql);   
$last_name=addslashes($xml->last_name);//varchar(100)	
$first_name=addslashes($xml->first_name);//	varchar(100)	
$patronymic=addslashes($xml->patronymic);//	varchar(100)	
$mobile_phone=addslashes($xml->mobile_phone);//	varchar(100)	
$email=addslashes($xml->email);//	varchar(50)	
$date_of_birth=date('Y-m-d', strtotime(str_replace('-', '.', $xml->date_of_birth)));//	datetime	
print "\n";
print  'e'.$date_of_birth;
print "\t";
print "<br />";
print 'g'.$xml->date_of_birth;
print "\n";
if (isset($xml->age) and ($xml->age<>'')) $age=$xml->age;//	int(11)
else 	$age=0;
$sex=addslashes($xml->sex);//	varchar(10)	
$martial_status=addslashes($xml->martial_status);//	varchar(50)	
if (isset($xml->number_of_childrens) and ($xml->number_of_childrens<>'')) $number_of_childrens=$xml->number_of_childrens;
else $number_of_childrens=0;//	varchar(20)	
$education=addslashes($xml->education);//	varchar(10)	
$vk_com=addslashes($xml->vk_com);//	varchar(50)	
$facebook=addslashes($xml->facebook);//	varchar(50)	
if (isset($xml->inn) and ($xml->inn<>'')) $inn=$xml->inn;//	varchar(10)	
	else $inn=0;
$ident=$inn;
$who_assigned_code=addslashes($xml->who_assigned_code);//	varchar(50)	
$when_entered_inn=date('Y-m-d', strtotime(str_replace('-', '.',$xml->when_entered_inn)));//	datetime	
$series_of_passport=$xml->series_of_passport;//	varchar(2)	
$number_of_passport=$xml->number_of_passport;//	varchar(7)	
$issued_passport=addslashes($xml->issued_passport);//	varchar(100)	
$date_of_issue_passport=date('Y-m-d', strtotime(str_replace('-', '.',$xml->date_of_issue_passport)));//	datetime	
$region_reg=trim(addslashes($xml->region_reg));//	varchar(30)	
$city_reg=(addslashes($xml->city_reg));//	varchar(50)	
$street_reg=addslashes($xml->street_reg);//	varchar(20)	https://github.com/OFFLINE-GmbH/Online-FTP-S3
$street_name_reg=addslashes($xml->street_name_reg);//	varchar(50)	
$house_reg=addslashes($xml->house_reg);//varchar(10)	
$appartment_reg=addslashes($xml->appartment_reg);//	varchar(5)	
$res_reg=addslashes($xml->res_reg);//	varchar(5)	
$region_residence=trim(addslashes($xml->region_residence));//	varchar(30)	
$city_residence=addslashes($xml->city_residence);//	varchar(50)	
$street_residence=addslashes($xml->street_residence);//	varchar(20)	
$street_name_residence=addslashes($xml->street_name_residence);//	varchar(50)	
$house_residence=addslashes($xml->house_residence);//	varchar(10)	
$appartment_residence=addslashes($xml->appartment_residence);//	varchar(5)	
$status_of_residence=addslashes($xml->status_of_residence);//	varchar(20)	
if (isset($xml->term_accommodation) and ($xml->term_accommodation<>'')) $term_accommodation=str_replace(',','.',$xml->term_accommodation);
else $term_accommodation=0;
//$term_accommodation=str_replace(',','.',$xml->term_accommodation);//	int(11)	
$home_phone=addslashes($xml->home_phone);//	varchar(15)	
$home_phone_residence=addslashes($xml->home_phone_residence);//	varchar(15)	
$name_of_contact_person=addslashes($xml->name_of_contact_person);//	varchar(50)	
$relatedness_of_contact_person=addslashes($xml->relatedness_of_contact_person);//	varchar(50)	
$phone_of_contact_person=addslashes($xml->phone_of_contact_person);//	varchar(15)	
$name_of_contact_person_2=addslashes($xml->name_of_contact_person_2);//	varchar(50)	
$relatedness_of_contact_person_2=addslashes($xml->relatedness_of_contact_person_2);//	varchar(50)	
$phone_of_contact_person_2=addslashes($xml->phone_of_contact_person_2);//	varchar(15)	
$movable_property=addslashes($xml->movable_property);//	varchar(5)	
$property=addslashes($xml->property);//	varchar(5)	
$current_work_first=addslashes($xml->current_work_first);// varchar(5)	
$full_name_of_organisation=addslashes($xml->full_name_of_organisation);//	varchar(50)	
$ownership=addslashes($xml->ownership);//	varchar(20)	
$edrpou=$xml->edrpou;//	varchar(10)	
$branch=addslashes($xml->branch);//	varchar(30)	
$number_of_employes=$xml->number_of_employes;//	varchar(50)	
$post=addslashes($xml->post);//	varchar(30)	
if (isset($xml->work_experience) and ($xml->work_experience<>'')) $work_experience=str_replace(',','.',$xml->work_experience);
else $work_experience=0;
if (isset($xml->total_work_experience) and ($xml->total_work_experience<>'')) $total_work_experience=str_replace(',','.',$xml->total_work_experience);
else $total_work_experience=0;
$work_phone=addslashes($xml->work_phone);//	varchar(15)	
if (isset($xml->monthly_income) and ($xml->monthly_income<>'')) $monthly_income=str_replace(',','.',$xml->monthly_income);
else $monthly_income=0;
//	decimal(10,0)	
if (isset($xml->existing_loans) and ($xml->existing_loans<>'')) $existing_loans=addslashes($xml->existing_loans);//	varchar(20)
	else $existing_loans=0;
if (isset($xml->cancelled_loans) and ($xml->cancelled_loans<>'')) $cancelled_loans=addslashes($xml->cancelled_loans);
else $cancelled_loans=0;
if (isset($xml->number_of_credit_cards) and ($xml->number_of_credit_cards<>''))	$number_of_credit_cards=addslashes($xml->number_of_credit_cards);
	else $number_of_credit_cards=0;
$employment_type=addslashes($xml->employment_type);//	varchar(20)	
$number_of_payment_cards=addslashes($xml->number_of_payment_cards);//	varchar(20)	
$date_of_payment_cards=date('Y-m-d', strtotime(str_replace('-', '.',$xml->date_of_payment_cards)));//	datetime	
$source_of_information=addslashes($xml->source_of_information);//	varchar(30)	
$site_source=addslashes($xml->site_source);//	varchar(30)	
if (isset($xml->id_card_number) and ($xml->id_card_number<>'')) $id_card_number=$xml->id_card_number;//	int(11)	
	else $id_card_number='';
if (isset($xml->id_card_record) and ($xml->id_card_record<>'')) $id_card_record=$xml->id_card_record;//	int(11)	
	else $id_card_record='';
   if (isset($xml->id_card_issue_date) and ($xml->id_card_issue_date<>'')) $id_card_issue_date=$xml->id_card_issue_date;//	int(11)	
	else $id_card_issue_date='';
if (isset($xml->id_card_expire_date) and ($xml->id_card_expire_date<>'')) $id_card_expire_date=$xml->id_card_expire_date;//	int(11)	
	else $id_card_expire_date='';
if (isset($xml->id_card_authority) and ($xml->id_card_authority<>'')) $id_card_authority=$xml->id_card_authority;//	int(11)	
	else $id_card_authority=0;

if (isset($xml->result_id) and ($xml->result_id<>'')) $result_id=str_replace('undefined','',str_replace('_','',$xml->result_id));//	int(11)	
	else $result_id=0;

   
$sql="insert into ClientsForCheckBKI values(
null,
'$last_name',
'$first_name',
'$patronymic',
'$mobile_phone',
'$email',
'$date_of_birth',
$age,
'$sex',
'$martial_status',
'$number_of_childrens',
'$education',
'$vk_com',
'$facebook',
'$inn',
'$who_assigned_code',
'$when_entered_inn',
'$series_of_passport',
'$number_of_passport',
'$issued_passport',
'$date_of_issue_passport',
'$region_reg',
'$city_reg',
'$street_reg',
'$street_name_reg',
'$house_reg',
'$appartment_reg',
'$res_reg',
'$region_residence',
'$city_residence',
'$street_residence',
'$street_name_residence',
'$house_residence',
'$appartment_residence',
'$status_of_residence',
$term_accommodation,
'$home_phone',
'$home_phone_residence',
'$name_of_contact_person',
'$relatedness_of_contact_person',
'$phone_of_contact_person',
'$name_of_contact_person_2',
'$relatedness_of_contact_person_2',
'$phone_of_contact_person_2',
'$movable_property',
'$property',
'$current_work_first',
'$full_name_of_organisation',
'$ownership',
'$edrpou',
'$branch',
'$number_of_employes',
'$post',
$work_experience,
$total_work_experience,
'$work_phone',
$monthly_income,
'$existing_loans',
'$cancelled_loans',
'$number_of_credit_cards',
'$employment_type',
'$number_of_payment_cards',
'$date_of_payment_cards',
'$source_of_information',
'$site_source',
$result_id,
$creditid
,'$id_card_number','$id_card_record','$id_card_issue_date','$id_card_expire_date','$id_card_authority')";   


print $sql;
   DatabaseHandler::Execute($sql);
   $sql="select value FROM parametersSite where id=5";
   $activeSystem=DatabaseHandler::GetOne($sql);
  
   $resultMatrixDesicionSec=0;
   $idubki=0;
  $idmbki=0;
   $AutoLim=0; 
if (isset($xml->is_data_changed) and ($xml->is_data_changed<>'')) $is_data_changed=$xml->is_data_changed;//	int(11)	
	else $is_data_changed=0;
if (isset($xml->analytics->short_request) and ($xml->analytics->short_request<>'')) $short_request=$xml->analytics->short_request;//	int(11)	
	else $short_request=0;
DatabaseHandler::execute("SET time_zone = 'Europe/Kiev';");
$sql="insert into Applications(id,inn,creditid,filename,xml,amountOfLoanBegin,is_data_changed,requestid) values(null,'$inn',$creditid,'$file','$xmlfile',$ammout_of_credit,$is_data_changed,$short_request)";
print $sql;
  DatabaseHandler::Execute($sql);
  $sql="select id from Applications where inn='$inn' and creditid=$creditid order by id desc LIMIT 1";
  
$applicationid=DatabaseHandler::GetOne($sql); 
if (isset($xml->analytics->devices->device['agent'])) {
$analytics_agent=addslashes($xml->analytics->devices->device['agent']);
} else {$analytics_agent='';}
if (isset($xml->analytics->ips->ip)) {

$analytics_ip =$xml->analytics->ips->ip;
} else {$analytics_ip='';}
if (isset($xml->analytics->request_time) and ($xml->analytics->request_time<>'')) $analytics_request_time=$xml->analytics->request_time;//	int(11)	
	else $analytics_request_time=0;


 $sql="insert into Analytics values(null,$applicationid,
 
 '$analytics_agent',
 '$analytics_ip',
 $analytics_request_time
)";

  DatabaseHandler::Execute($sql);
$sql="SELECT LAST_INSERT_ID()";
$analyticsid=DatabaseHandler::GetOne($sql);   
foreach ($xml->analytics->inputs->credit_term->value as $node) {
if (isset($node) and ($node<>'')) {$value=addslashes($node);//	int(11)	
	


 $sql="insert into Analytics_credit_term values(null,$analyticsid,
 
 $value

)";

  DatabaseHandler::Execute($sql);
}
}
foreach ($xml->analytics->inputs->amount_of_credit->value as $node) {
if (isset($node) and ($node<>'')) {$value=addslashes($node);//	int(11)	



 $sql="insert into Analytics_amount_of_credit values(null,$analyticsid,
 
 $value

)";

  DatabaseHandler::Execute($sql);
}
}
foreach ($xml->analytics->inputs->phone_of_contact_person->value as $node) {
if (isset($node) and ($node<>'')) {$value=addslashes($node);//	int(11)	
	


 $sql="insert into Analytics_phone_of_contact_person values(null,$analyticsid,
 
 '$value'

)";

  DatabaseHandler::Execute($sql);
}
}
foreach ($xml->analytics->inputs->full_name_of_organisation->value as $node) {
if (isset($node) and ($node<>'')) {$value=addslashes($node);//	int(11)	



 $sql="insert into Analytics_full_name_of_organisation values(null,$analyticsid,
 
 '$value'

)";

  DatabaseHandler::Execute($sql);
}
}     
foreach ($xml->analytics->inputs->read_agreement->value as $node) {
if (isset($node) and ($node<>'')) {$value=addslashes($node);//	int(11)	



 $sql="insert into Analytics_read_agreement values(null,$analyticsid,
 
 '$value'

)";

  DatabaseHandler::Execute($sql);
}
}  
  if ($activeSystem=='1'){ 
   $scoringcardname='';
$scoreScor=0;
$solutScor='';
$typeSc=0;
$statusCheck=0;
$Zone=0;
$match_phone_UBKI=0;
$zone_type=0;
 $carddecisionname='';
 $decisionCard=0;
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Початок перевірки',$applicationid,'$Zone',0)";

  DatabaseHandler::Execute($sql);
   $where="IdentCode='$inn' ";
   $sql="SELECT count(*) FROM BlackLists WHERE $where";

$nRowsBlackList=DatabaseHandler::GetOne($sql); 
if ($nRowsBlackList>0){
	$sql="SELECT Status FROM BlackLists WHERE $where";
$results_array=DatabaseHandler::GetAll($sql);
$statusBlackList=$results_array[0]['Status'];
}
else $statusBlackList=0;
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка BlackList. Код результату=$statusBlackList',$applicationid,'$Zone',0)";
DatabaseHandler::Execute($sql);
	$where="IdentCode='$inn' ";
   $sql="SELECT count(*) FROM 	InternalRates WHERE $where";

$nRowsInternalRates=DatabaseHandler::GetOne($sql); 
if ($nRowsInternalRates>0){
	$sql="SELECT Segment FROM 	InternalRates WHERE $where";
$results_array=DatabaseHandler::GetAll($sql);
$statusInternalRates=$results_array[0]['Segment'];
}
else $statusInternalRates=8;
$sql="SELECT type FROM `segmscoring` where segment=$statusInternalRates";
$typeSc=DatabaseHandler::GetOne($sql);
 $sql="SELECT maxsuma FROM `segmscoring` where segment=$statusInternalRates";
$maxsuma=DatabaseHandler::GetOne($sql);
if (is_null($typeSc)) $typeSc=0;
	$sql="update Applications set segment=$statusInternalRates where creditid=$creditid";
	DatabaseHandler::Execute($sql);
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка InternalRating Код результату=$statusInternalRates',$applicationid,'$Zone',0)";
DatabaseHandler::Execute($sql);
$statusAvto=0;
 $sql="select * FROM HistoryOrder";
   $dd=DatabaseHandler::GetAll($sql); 

   
$where="inn='$inn' and typeDecision='Manual' and manualDecisionid<>0 order by id desc limit 1";
 $sql="SELECT manualDecisionid,reasonDecisionid,dateManualDecision FROM Applications  WHERE $where"; 
 print 'Повторна перевірка';
 print "\n";
print $sql;
$nRows=DatabaseHandler::GetAll($sql);
print_r($nRows);
if(count($nRows)>0){
$povManualDecision=$nRows[0]['manualDecisionid'];
$povManualReason=$nRows[0]['reasonDecisionid'];
print "<br />";
print $povManualReason;
print "<br />"; 
$povManualDate=date('d-m-Y',strtotime($nRows[0]['dateManualDecision']));
$today=date('d-m-Y');

print "<br />";
print_r($dd);
print "<br />";
 $fieldCond='';
for($i = 0; $i < count($dd); ++$i) {
print "<br />";
print $i;
print "<br />";
$fieldid=$dd[$i]['id'];
    $fieldManual=$dd[$i]['manualDecision'];
	 $fieldAllReason=$dd[$i]['allReason'];
	 $fieldReasonDecision=$dd[$i]['reasonDecision'];
	 $fieldAllPeriod=$dd[$i]['allPeriod'];
	 $fieldPeriod=$dd[$i]['Period'];
	  $typeApplication=$dd[$i]['typeApplication'];
	 if ($typeSc==2) {$typeAll='Транші';}
	 if ($typeSc==1) {$typeAll='Нові';}
	 $sql="select name FROM decisionmanualapplicationdetail where id=$fieldReasonDecision";
	 $fieldCond=addslashes(DatabaseHandler::GetOne($sql));
print "<br />";

	 print $povManualDecision;
print "<br />";

print	 $fieldManual;
print "<br />";
print "<br />";

	 print $fieldAllReason;
	 print "<br />";
	 print $povManualReason;

print "<br />";
print $povManualDate;
print "<br />";

	 print $fieldAllPeriod;
print "<br />";	 
print date('d-m-Y', strtotime($povManualDate.' +'.$fieldPeriod.' days'));
print "<br />";
print $today;
print "<br />";
$diff = abs(strtotime($povManualDate) - strtotime($today));
print "<br />";
print $povManualDate;
print "<br />";
print $today;
print "<br />";
$years = floor($diff / (365*60*60*24));
$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
$days = floor($diff / (60*60*24));
print "<br />";
print $days;
print "<br />";
	 if ( (($typeApplication==$typeAll) or ($typeApplication=='Всі')) and($povManualDecision==$fieldManual)and (($fieldAllReason==1) or ($fieldReasonDecision==$povManualReason)) and (($fieldAllPeriod==1) or ($days<=$fieldPeriod))){
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка завершена. Повторна заявка.Автоповторна.Причина=$fieldCond',$applicationid,'$Zone',200)";
  DatabaseHandler::Execute($sql);
	$sql="update Applications set manualDecision='Автоповторна',reasonDecisionid=$fieldReasonDecision where creditid=$creditid";
  DatabaseHandler::Execute($sql);
$statusCheck=3;
$statusAvto=1;
		break; 
	 }


}
}
//перевірка BlackList
if ($statusCheck==0)
{
	$where="IdentCode='$inn' ";
   $sql="SELECT count(*) FROM BlackLists WHERE $where";

$nRowsBlackList=DatabaseHandler::GetOne($sql); 
if ($nRowsBlackList>0){
	$sql="SELECT Status FROM BlackLists WHERE $where";
$results_array=DatabaseHandler::GetAll($sql);
$statusBlackList=$results_array[0]['Status'];
}
else $statusBlackList=0;
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка BlackList. Код результату=$statusBlackList',$applicationid,'$Zone',0)";
//DatabaseHandler::Execute($sql);
	$where="IdentCode='$inn' ";
   $sql="SELECT count(*) FROM 	InternalRates WHERE $where";
$nRowsInternalRates=DatabaseHandler::GetOne($sql); 
if ($nRowsInternalRates>0){
	$sql="SELECT Segment,AutoLim FROM 	InternalRates WHERE $where";
	print $sql;
print "<br />";
$results_array=DatabaseHandler::GetAll($sql);
$statusInternalRates=$results_array[0]['Segment'];
$AutoLim=$results_array[0]['AutoLim'];
	print $AutoLim;
print "<br />";
} else $statusInternalRates=8;
	$typeSc=0;
 $sql="SELECT type FROM `segmscoring` where segment=$statusInternalRates";
$typeSc=DatabaseHandler::GetOne($sql); 
 $sql="select max_period FROM HistoryBKI where id=2";
   $daytranche=DatabaseHandler::GetOne($sql); 
    $sql="SELECT maxsuma FROM `segmscoring` where segment=$statusInternalRates";
$maxsuma=DatabaseHandler::GetOne($sql);
   $sql="select max_period FROM HistoryBKI where id=1";
   $daybki=DatabaseHandler::GetOne($sql); 
   $resultMatrixDesicionUpdateBKI='OK';
if (is_null($typeSc)) $typeSc=0;

	$sql="update Applications set segment=$statusInternalRates,typeSc=$typeSc where creditid=$creditid";

	DatabaseHandler::Execute($sql);
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка InternalRating Код результату=$statusInternalRates',$applicationid,'$Zone',0)";
//DatabaseHandler::Execute($sql);
$where="InternalRating=$statusInternalRates and BlackList=$statusBlackList";
 $sql="SELECT id,Result FROM MatrixDecision WHERE $where";

$nMatrixDecision=DatabaseHandler::GetAll($sql);
print "<br />";
print_r($nMatrixDecision);
print "<br />";
$resultMatrixDesicion=$nMatrixDecision[0]['Result'];
$tzid=$nMatrixDecision[0]['id'];
if (($resultMatrixDesicion=='ОК') and ($typeSc==2)) {
$sql="select IFNULL(decisionAfterBKI,0) decisionAfterBKI  from Applications  where inn='$inn' and creditid<>$creditid and typeSc=2 order by id desc ";
print $sql;
$resGetApplications=DatabaseHandler::GetAll($sql);
if(count($resGetApplications)>0){
if ($resGetApplications[0]['decisionAfterBKI']==3)  {
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перехід на БКІ через умову попередня завка транш і рішення Reject ',$applicationid,'',0)";
DatabaseHandler::Execute($sql);
$resultMatrixDesicionUpdateBKI='БКІ';
}
}
 }
 if (($resultMatrixDesicion=='ОК') and ($typeSc==2)) {

$sql="select IFNULL(CreditInfoId,0) CreditInfoId,IFNULL(id,0) idmbki,DATE_FORMAT(CreatedAt, '%Y-%m-%d') CreatedAt from credit_buroMBKI where inn='$inn'  order by id desc";
print 'Start tranche МБКІ';
$resGetMBKI=DatabaseHandler::GetAll($sql);
print_r($resGetMBKI);
if(count($resGetMBKI)>0){
	
	

		
$now = date('Y-m-d'); // or your date as well
$your_date = $resGetMBKI[0]['CreatedAt'];
$datediff = strtotime($now) - strtotime($your_date);;

$dayLoadMBKI=round($datediff / (60 * 60 * 24));
if ($daytranche<$dayLoadMBKI) 	{
	 
	$resultMatrixDesicionUpdateBKI='БКІ';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Оновлення звіту МБКІ (днів тому) {$dayLoadMBKI}>{$daytranche} ',$applicationid,'$Zone',0)";}
else {
	
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Оновлення звіту МБКІ (днів тому) {$dayLoadMBKI}<={$daytranche} ',$applicationid,'$Zone',0)";
		$idmbki=$resGetMBKI[0]['idmbki'];

	}
DatabaseHandler::Execute($sql);
		} else {$dayLoadMBKI=0;
		$resultMatrixDesicionUpdateBKI='БКІ';
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Оновлення звіту МБКІ (днів тому) {$dayLoadMBKI}<{$daytranche} ',$applicationid,'$Zone',0)";
DatabaseHandler::Execute($sql);
	
	}
	
	$sql="select IFNULL(id,0) idubki,DATE_FORMAT(CreatedAt, '%Y-%m-%d') CreatedAt from credit_buroUBKI where StateCode='$inn'  order by id desc";
print 'Start tranche УБКІ';
$resGetUBKI=DatabaseHandler::GetAll($sql); 
if(count($resGetUBKI)>0){
	

		
$now = date('Y-m-d'); // or your date as well
$your_date = $resGetUBKI[0]['CreatedAt'];
$datediff = strtotime($now) - strtotime($your_date);

$dayLoadUBKI=round($datediff / (60 * 60 * 24));
if ($daytranche<$dayLoadUBKI) {	
	 	$resultMatrixDesicionUpdateBKI='БКІ';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Оновлення звіту УБКІ (днів тому) {$dayLoadUBKI}>{$daytranche} ',$applicationid,'$Zone',0)";}
else {
if (isset($dayLoadMBKI) and ($daytranche>=$dayLoadMBKI)) 	{	 $statusCheck=1; $Zone='WhiteZone';} else {$resultMatrixDesicionUpdateBKI='БКІ';}
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Оновлення звіту УБКІ (днів тому) {$dayLoadUBKI}<={$daytranche} ',$applicationid,'$Zone',0)";
		$idubki=$resGetUBKI[0]['idubki'];

	}
DatabaseHandler::Execute($sql);
		} else {$dayLoadUBKI=0;	 $statusCheck=0; $Zone='';
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Оновлення звіту УБКІ (днів тому) {$dayLoadUBKI}<{$daytranche} ',$applicationid,'$Zone',0)";
DatabaseHandler::Execute($sql);
	$resultMatrixDesicionUpdateBKI='БКІ';
	}
	 } else {
	 
 $statusCheck=1; $Zone='WhiteZone';}
if ($resultMatrixDesicion=='Ref') {$statusCheck=3;$Zone='RedZone';}
if ($resultMatrixDesicion=='БКІ') {$statusCheck=0;$Zone='БКІ';}
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка MatrixDecision Код результату=$resultMatrixDesicion',$applicationid,'$Zone',300)";
print "<br />";
print_r($statusCheck);
print "<br />";
if (($resultMatrixDesicion=='ОК') and ($typeSc==2) and (	$resultMatrixDesicionUpdateBKI=='БКІ')) {
	 print $resultMatrixDesicionUpdateBKI;
  $statusCheck=0; $Zone='БКІ';
  print $Zone;
 }	 
DatabaseHandler::Execute($sql);

if ($resultMatrixDesicion!='ОК'){

 $sql="SELECT id,typeApplication,nameControl,Zone,conditions,fieldApplicationFirst,conditions2,fieldApplicationSecond FROM `matrixofdecisionsthrid`";
   $results=DatabaseHandler::GetAll($sql); 
   print_r($results);
for($i = 0; $i < count($results); ++$i) {
	print $i;
    $fieldAppFirst=$results[$i]['fieldApplicationFirst'];
	 $list_conditions=$results[$i]['conditions'];
	$fieldAppSecond=$results[$i]['fieldApplicationSecond'];
	 $list_conditions2=$results[$i]['conditions2'];
	 $zoneCond=$results[$i]['Zone'];
	 $nameControl=$results[$i]['nameControl'];
	 $arr_first=explode(";",$list_conditions);
	 $arr_second=explode(";",$list_conditions2);
	 $id=$results[$i]['id'];
	 $val_searchFirst="${$fieldAppFirst}";
	  $val_searchSecond="${$fieldAppSecond}";
	  print "\n";
	  print "<br />";
	  print $val_searchFirst;
	  print_r( $arr_first);
	  print "<br />";
	  print $val_searchSecond;
	  print_r( $arr_second);
	    print "\n";
		print "<br />";
		print "\n";
		
		print $id;
		print "<br />";
		print "\n";
		print "\n";
		
		if ($id==21)
{


$sql="select inn,creditid,mobile_phone,number_of_payment_cards from ClientsForCheckBKI where inn='$inn' and creditid<>$creditid order by creditid";
$resTel=DatabaseHandler::GetAll($sql);
if (isset($resTel[0])){
$telOld=$resTel[0]['mobile_phone'];
$paym=$resTel[0]['number_of_payment_cards'];
$creditidold=$resTel[0]['creditid'];

if (($telOld<>$mobile_phone) and ($paym<>$number_of_payment_cards)){
print 'sdds';
if ($statusCheck<2) {$statusCheck=2;}
		  $resultMatrixDesicionSec=1;
		 $Zone='GreyZone';
		 
		
 $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка MatrixDecision1.2 Назва контролю={$nameControl} поле {$fieldAppFirst}=$val_searchFirst {$fieldAppSecond}=$val_searchSecond повтор creditidrepeat=$creditidold' ,$applicationid,'$Zone',3000)";
DatabaseHandler::Execute($sql);
		
}}}
 elseif 	 ($id==29)
{

if (strlen($mobile_phone)>10) $mobile_phone1=substr($mobile_phone,-10);
	else $mobile_phone1='';
if (strlen($home_phone)>10) $home_phone1=substr($home_phone,-10);
	else  $home_phone1='';
if (strlen($home_phone_residence)>10) $home_phone_residence1=substr($home_phone_residence,-10);
	else $home_phone_residence1='';
if (strlen($work_phone)>10) $work_phone1=substr($work_phone,-10);
	else $work_phone1='';
	
	$mobile_phone2=str_replace('+','',$mobile_phone);
		$home_phone2=str_replace('+','',$home_phone);
			$home_phone_residence2=str_replace('+','',$home_phone_residence);
				$work_phone2=str_replace('+','',$work_phone);
		if (strlen($phone_of_contact_person)>10) $phone_of_contact_person1=substr($phone_of_contact_person,-10);
	else $phone_of_contact_person1='';	
		if (strlen($phone_of_contact_person_2)>10) $phone_of_contact_person_2_1=substr($phone_of_contact_perso_2n,-10);
	else $phone_of_contact_person_2_1='';	
	
$sql="select count(*) as cnt from blackphone where right(number,10) in (
'$mobile_phone','$home_phone','$phone_of_contact_person','$phone_of_contact_person1','$phone_of_contact_person_2','$phone_of_contact_person_2_1','$home_phone_residence','$work_phone','$mobile_phone1','$home_phone1','$home_phone_residence1','$work_phone1','$mobile_phone2','$home_phone2','$home_phone_residence2','$work_phone2' and number<>''
) ";
print $sql;
$resTel=DatabaseHandler::GetOne($sql);
if ((isset($resTel)) and  ($resTel>0)){
print $resTel;
 if ($zoneCond=='Grey Zone')
		 {
		 $Zone='GreyZone';
		 }
if ($zoneCond=='Red Zone')
		 {
		 $Zone='RedZone';
		 }		 
		  $resultMatrixDesicionSec=1;
		
 $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка MatrixDecision1.2 Назва контролю={$nameControl} - чорний список телефону' ,$applicationid,'$Zone',3000)";
DatabaseHandler::Execute($sql);
		
}}
 else

{
  if  ($id<>29){

print "check 1<br />";
	  if (in_array($val_searchFirst, $arr_first) && (in_array($val_searchSecond, $arr_second))) {
		 if ($zoneCond=='Grey Zone')
		 {
    echo $zoneCond." ".$nameControl;
		// if ($statusCheck<2) {$statusCheck=2;}
		  //$resultMatrixDesicionSec=1;
		  $ZoneMatrix=1;
		 $Zone='GreyZone';
		 if (isset($fieldAppSecond) and ($fieldAppSecond<>'')){
			 print 'ok';
 $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка MatrixDecision1.2 Назва контролю={$nameControl} поле {$fieldAppFirst}=$val_searchFirst {$fieldAppSecond}=$val_searchSecond',$applicationid,'$Zone',300)";
		 } else {
		 $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка MatrixDecision1.2 Назва контролю={$nameControl} поле {$fieldAppFirst}=$val_searchFirst ',$applicationid,'$Zone',300)";
			 
		 }
DatabaseHandler::Execute($sql);
		 }
		 
			print "<br />";
		if ($zoneCond=='Red Zone')
		 {
    echo $zoneCond." ".$nameControl;
		 $statusCheck=3;
		  $resultMatrixDesicionSec=1;
		 $Zone='RedZone';
		 if (isset($fieldAppSecond) and ($fieldAppSecond<>'')){
 $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка MatrixDecision1.2 Назва контролю={$nameControl} поле {$fieldAppFirst}=$val_searchFirst {$fieldAppSecond}=$val_searchSecond',$applicationid,'$Zone',300)";
		 } else {
		 $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка MatrixDecision1.2 Назва контролю={$nameControl} поле {$fieldAppFirst}=$val_searchFirst ',$applicationid,'$Zone',300)";
			 
		 }
DatabaseHandler::Execute($sql);
		 }
		 
			print "\n";	

}
}
}}
		print "\n";

if ($statusCheck==1) { $Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка MatrixDecision1.2 Рішення=$statusCheck',$applicationid,'$Zone',1333)";

DatabaseHandler::Execute($sql);
}
if ($statusCheck==3) {$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка MatrixDecision1.2 Рішення=$statusCheck',$applicationid,'$Zone',1333)";

DatabaseHandler::Execute($sql);
}
if (($statusCheck==0) and ($resultMatrixDesicionSec<>1)) {$Zone='БКІ';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка MatrixDecision1.2 Рішення=$statusCheck',$applicationid,'$Zone',1333)";

DatabaseHandler::Execute($sql);
}
}
}
print "<br />";
print "check bki";
print $statusCheck;

print "<br />";
$scoringcardname='';
$scoreScor=0;
$solutScor='';
$solutScor='';
$typeSc=0;
 $sql="SELECT type FROM `segmscoring` where segment=$statusInternalRates";
$typeSc=DatabaseHandler::GetOne($sql); 
$sql="SELECT maxsuma FROM `segmscoring` where segment=$statusInternalRates";
$maxsuma=DatabaseHandler::GetOne($sql);
if (is_null($typeSc)) $typeSc=0;

if ($statusAvto==0){
 if (($site_source=='http://pozichka.com/') or ($site_source=='http://private.pozichka.ua/') or ($site_source=='https://private.pozichka.ua/') or ($site_source=='https://pozichka.ua/')) {$site_source='http://pozichka.ua';}
 $sql="select a.id scoringcardid,a.share,a.name scoringcardname from scoringcards a  join  parameterrunscorcard b on a.id=b.scoringcardid where a.date_end>CURDATE() and source='$site_source' and b.BKI=1 and b.status=1 and `type`=$typeSc ";
print "<br />";
 print $sql;
 $results=DatabaseHandler::GetAll($sql); 
print "<br />";
for($i = 0; $i < count($results); ++$i) {
 $scoreScor=0;
 $statusCheckScor=0;
 $decisionAfterScorFirst=0;
 $scoringcardid=$results[$i]['scoringcardid'];
    $scoringcardname=$results[$i]['scoringcardname'];
	$share=$results[$i]['share'];
	$share=100-$share;
	$share=substr($share, 0, 1);
	$a=intval(substr($creditid, -1));
	$b=intval($share);
	if ($b==0) {$b=1;}
	print "Скоринг=";
	print "<br />";
print $a;
	print "<br />";
print $b;
	print "<br />";
	print ($a%$b);
print "<br />";
	print "Кінець скорингу";
		$share=['0','1','2','3','4','5','6','7','8','9'];
	if ($a % $b==0){
		
$sql="select b.id id,c.id idparameter,c.name parameter from  ascoringcard b  join detalniparametruscoringcard c on b.parameterscoringcard_id=c.id 

where  b.scoringcard_id=$scoringcardid and c.BKI=0 ";
print $sql;
 	$resultsd=DatabaseHandler::GetAll($sql);
for($j = 0; $j < count($resultsd); ++$j) {
	 $id=$resultsd[$j]['id'];
	 $idparameter=$resultsd[$j]['idparameter'];
	  $parameter=$resultsd[$j]['parameter'];
$sql="select d.conditions conditions,d.score score from   bscoringcard d 
where  ascoringcard_id=$id and conditions<>'NULL'";
 	$resultsdd=DatabaseHandler::GetAll($sql);
	print_r($resultsdd);
for($k = 0; $k < count($resultsdd); ++$k) {
	$conditions=$resultsdd[$k]['conditions'];
	 $score=$resultsdd[$k]['score'];
	print "<br />";
		 print "param=";
	 print $idparameter;
	 print "<br />";
	if ($idparameter==1)
	{
		
		$sql="select count(*) from CreditsClientsForCheckBKI where id=$creditid and ammout_of_credit {$conditions}";
print "<br />";
	 print $sql;
	 print "<br />";
		$nRowsIn=DatabaseHandler::GetOne($sql); 
		print "<br />";
	 print $nRowsIn;
	 print "<br />";
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);

}			
	//	DatabaseHandler::Execute($sql);

	}
	print "<br />";
	 print_r( $nRowsIn);
	 print "<br />";
if ($idparameter==2)
	{
if (is_null($CreditInfoId)) $CreditInfoId=0;
if (is_null($subjectid)) $subjectid=0;
$sql="select count(a.cnt) from ( SELECT b.dlref as cnt from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid WHERE b.cki_id=$maxubki and (a.dldonor='FIN' or a.dldonor='MFO') and b.dlds between b.dldpf - INTERVAL 180 day and b.dldpf and a.dlamt<=10000
union
SELECT CodeOfContract FROM `contractsMBKI` WHERE `PereodicityOfPayments`='В останній день строку дії договору' and `CreditorType`='Фінансова компанія' and `TotalAmountValue`<=20000 and `SubjectId`=$maxmbki) a 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	if (is_null($nRowsIn) or ($nRowsIn==''))  $nRowsIn=0; 
$os=array('0','1');
if (in_array(substr($conditions, 0, 1), $os)) {
	if ($nRowsIn==$conditions){

		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\"  параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		DatabaseHandler::Execute($sql);
$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	}
} else {
/*$sql="select count(a.cnt) from ( SELECT b.dlref as cnt from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid WHERE b.cki_id=$maxubki and a.dldonor='FIN' and b.dlds between b.dldpf - INTERVAL 180 day and b.dldpf and a.dlamt<=10000
union
SELECT CodeOfContract FROM `contractsMBKI` WHERE `PereodicityOfPayments`='В останній день строку дії договору' and `Creditor`='B-2' and `TotalAmountValue`<=20000 and `SubjectId`=$maxmbki) a 
";	
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql);*/ 
	if ($nRowsIn>1){

		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\"  параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		DatabaseHandler::Execute($sql);
$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	}
}
print "<br />";
print $nRowsIn;
print "<br />";
	
	
	//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);	
}
if ($idparameter==3)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and work_experience {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";

			DatabaseHandler::Execute($sql);
	$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
		//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==4)
	{
		if ($conditions=='="2_Так"') {
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and (property{$conditions} or movable_property{$conditions} )";
		} else {
	$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and (property{$conditions} and movable_property{$conditions} )";
			
		}
		print "<br />";
print $sql;
print $conditions;
print "<br />";
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
		
}	
		//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
		//DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==5)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and current_work_first{$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		
}	
		//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
		//DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==6)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and (cancelled_loans<>'1_Відсутні' and cancelled_loans<>'0_Виберіть відповідь з переліку')";
		print "<br />";
print $sql;
print "<br />";
	$nRowsIn=DatabaseHandler::GetOne($sql);
	print "<br />";
	print $nRowsIn;
	print "<br />";
	if (is_null($nRowsIn) or ($nRowsIn==''))  $nRowsIn=0; 
	// убкі
	
$sql="select count(*) from (select b.dlref as cnt from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$maxubki  and `dlflstat`=2
union
select CodeOfContract  FROM `contractsMBKI` where subjectid=$maxmbki and ContractType='Terminated') a
 ";
print $sql;
$сloseContracts=DatabaseHandler::GetOne($sql);
if(is_null($closeContracts)) $closeContracts=0;
print $сloseContracts;
if ($conditions=='1 and 1'){
if (($nRowsIn>0) and ($сloseContracts>0 )){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		
}

}
if ($conditions=='0 and 1'){
if (($nRowsIn==0) and ($сloseContracts>0 )){
		$scoreScor=$scoreScor+$score;
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		
		}

}
if ($conditions=='1 and 0'){
if (($nRowsIn>0) and ($сloseContracts==0 )){
		$scoreScor=$scoreScor+$score;
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		
		}

}
if ($conditions=='0 and 0'){
if (($nRowsIn==0) and ($сloseContracts==0 )){
		$scoreScor=$scoreScor+$score;
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		
		}

}


print "<br />";
print $conditions;
print $closeContracts;
print "<br />";
 



		print "<br />";
		 print "sd";
	 print $sql;
	 print "<br />";
		//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
		//DatabaseHandler::Execute($sql);
	}
	
print "<br />";
print "1";
print "<br />";
	if ($idparameter==7)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and education {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
				$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
	
}	
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==8)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and number_of_childrens {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
DatabaseHandler::Execute($sql);
	
}	
		//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==9)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and martial_status {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
	
}	
			//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==10)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and employment_type {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}	
		//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==11)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and source_of_information {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}	
		//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==12)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and movable_property {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}	
//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==13)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and property {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}	
			//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==15)
	{
		$sql="SELECT count(distinct a.id)  from crdealUBKI a   
 join deallifeUBKI b on a.id=b.crdealid WHERE b.cki_id=$maxubki and (a.dldonor='FIN' or a.dldonor='MFO') and b.dlds between b.dldpf - INTERVAL 180 day and b.dldpf and a.dlamt<=10000 and a.cki_id=$maxubki 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($conditions=='<1'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
		}

}
if ($conditions=='2')
{
if (($nRowsIn==2) ){
		$scoreScor=$scoreScor+$score;
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
	
		}

}
if ($conditions=='>2'){
if (($nRowsIn>2)){
		$scoreScor=$scoreScor+$score;
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
		}

}

			//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==17)
	{
		$sql="SELECT count(contracts.id) FROM `contractsMBKI` contracts    
 WHERE  `CreditorType` like 'Фінансова компанія%'  and contracts.SubjectId=$maxmbki 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($conditions=='<1'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);

}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);

		}

}
if ($conditions=='2')
{
if (($nRowsIn==2) ){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);

		}

}
if ($conditions=='>2'){
if (($nRowsIn>2)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);

		}

}

	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==18)
	{
		$sql="select NumberOfTerminatedContracts from summaryinformationdebtorMBKI where subjectid=$maxmbki 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($conditions=='<1'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
				$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);

}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);

		}

}
if ($conditions=='2')
{
if (($nRowsIn==2) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);

		}

}
if ($conditions=='>2'){
if (($nRowsIn>2)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);

		}

}

		//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==20)
	{
		$sql="select  InquieryValue from inqueryMBKI where SubjectId=$maxmbki and  id=(select min(id) from inqueryMBKI where SubjectId=$maxmbki)
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($conditions=='1'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
				$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
	
}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
				$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
	
}

}
if ($conditions=='2')
{
if (($nRowsIn==2) ){
		$scoreScor=$scoreScor+$score;
				$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}

}
if ($conditions=='>2'){
if (($nRowsIn>2)){
		$scoreScor=$scoreScor+$score;
				$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
	
}

}

	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==22)
	{
		$sql="select count(id) from SearchInqueryMBKI where SubjectId=$maxmbki
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($conditions=='0'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}

}
if ($conditions=='between 1 and 5'){

if (($nRowsIn==1) or ($nRowsIn==2)or ($nRowsIn==3)or ($nRowsIn==4)or ($nRowsIn==5)){

		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}

}
if ($conditions=='>5')
{
if (($nRowsIn>5) ){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}

}

		//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==25)
	{
		$sql="select count(id) from SearchInqueryMBKI where Dated > (NOW() - INTERVAL 1 MONTH) and SubjectId=$maxmbki
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($conditions=='0'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
		
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1')
{
if (($nRowsIn==1) ){
		$scoreScor=$scoreScor+$score;
		
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}

	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==27)
	{
		$sql="select status_of_residence,property from ClientsForCheckBKI where creditid=$creditid ";
print $sql;
	$nRowsIn=DatabaseHandler::GetAll($sql); 
	print_r($nRowsIn);
if ($nRowsIn[0]['status_of_residence']=='1_Одноосібна власність'){
	if ($conditions=='="1_Одноосібна власність"'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,1,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
}	
}
if (($nRowsIn[0]['status_of_residence']!='1_Одноосібна власність') and ($nRowsIn[0]['property']=='2_Так')){
	if ($conditions=='="2_Так"'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
}	
}
if (($nRowsIn[0]['status_of_residence']!='1_Одноосібна власність') and (($nRowsIn[0]['property']=='1_Ні') )){
	if ($conditions=='="1_Ні"'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
}
if (($nRowsIn[0]['status_of_residence']!='1_Одноосібна власність') and (($nRowsIn[0]['property']!='1_Ні') and ($nRowsIn[0]['property']!='2_Так'))){
	if ($conditions=='Не заповнено'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,1,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
}
}
}
$sql="select d.conditions conditions,d.score score from   bscoringcard d 
where  ascoringcard_id=$id and conditions='NULL'";
print $sql;
 	$resultsdd=DatabaseHandler::GetAll($sql);
	print_r($resultsdd);
for($k = 0; $k < count($resultsdd); ++$k) {
	$conditions=$resultsdd[$k]['conditions'];
	 $score=$resultsdd[$k]['score'];
	 	$sql="select 1 from scoringresult where scoringcardid=$scoringcardid and idparameter=$idparameter and applicationid=$applicationid and idrunscoring=1
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
print $nRowsIn;
if (($nRowsIn==0)){
	$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,1,$scoreScor)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}
}
}
$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,100,0,1,$scoreScor)";
				print "<br />";
	 print $sql;
	 print "<br />";
	//	DatabaseHandler::Execute($sql);
$sql="SELECT minBall FROM `parameterrunscorcard` where scoringcardid=$scoringcardid and status=1 and BKI=1";
$minBall1=DatabaseHandler::GetOne($sql);
if (is_null($minBall1) or ($minBall1=='')) $minBall1=0;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\"  Заг сума скорингу=$scoreScor. Мін сума для переходу в БКІ=$minBall1',$applicationid,'Scoring',3000)";
			DatabaseHandler::Execute($sql);
			print "<br />";
			print $scoreScor;
		print "<br />";	
	 print $minBall1;
	 print "<br />"; 
if ($scoreScor<$minBall1) 
{
	print "<br />";
			print $scoreScor;
		print "<br />";	
	 print $minBall1;
	 print "<br />"; 
	 print $resultMatrixDesicionSec;
	 	 print "<br />"; 
		$decisionAfterScorFirst=2;

	if (	  $resultMatrixDesicionSec==1)
	{
		if ($Zone=='GreyZone') {
$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='Grey Zone'";}
if ($Zone=='WhiteZone') {
$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='White Zone'";}
if ($Zone=='RedZone') {
$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='Red Zone'";}

	}	else {	
	$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='null'";
	}

 	$resultsddddd=DatabaseHandler::GetAll($sql);
for($r = 0; $r < count($resultsddddd); ++$r) {
	 $zoneSc=$resultsddddd[$r]['zone'];
	 $rejectTo=$resultsddddd[$r]['rejectTo'];
	 $approveFrom=$resultsddddd[$r]['approveFrom'];
print "<br />";
	 print $approveFrom;
print "<br />";

print $scoreScor;
print "<br />";

if ($approveFrom>=$scoreScor){
	print "+";
$statusCheck=1;
print "<br />0 -";
print $statusCheck;
print "<br />";
	} else
if ($rejectTo<$scoreScor){
	
	$statusCheck=3;
	
	 }
}
if ($statusCheck==0) {$statusCheck=2;}
} else { $decisionAfterScorFirst=1;

if (	  $resultMatrixDesicionSec==1)
	{
			print "<br />";
			print "Zone";
		print "<br />";	
	 print $Zone;
	 print "<br />"; 
		if (($Zone=='GreyZone')or ($Zone=='БКІ')) {
$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='Grey Zone'";}
if ($Zone=='WhiteZone') {
$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='White Zone'";}
if ($Zone=='RedZone') {
$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='Red Zone'";}

	

 	$resultsddddd=DatabaseHandler::GetAll($sql);
for($r = 0; $r < count($resultsddddd); ++$r) {
	 $zoneSc=$resultsddddd[$r]['zone'];
	 $rejectTo=$resultsddddd[$r]['rejectTo'];
	 $approveFrom=$resultsddddd[$r]['approveFrom'];

if ($approveFrom>=$scoreScor){
	print "<br />2 - ";
print $statusCheck;
print "<br />";
$statusCheck=1;
	} else
if ($rejectTo<=$scoreScor){
	
	$statusCheck=3;
	
	 }
}
}	
}	
}
}
}
if (count($results)==0) $decisionAfterScorFirst=1;
if ($statusCheck>0) $decisionAfterScorFirst=2;
print "<br />1 -";
print $statusCheck;
print "<br />";

if ($statusCheck==0){
$zapyt=0;
$resultZapyt=false;
	$sql="SELECT NameBki,Priority FROM `parameterBKI` where status=1 
union all
SELECT NameBki,4 FROM `parameterBKI` where status=1 and 
exists(select 1 FROM `parameterBKI` where status=1 and NameBKI='УБКІ' and priority=1 and `return`=1) and exists(select 1 FROM `parameterBKI` where status=1 and NameBKI='МБКІ') and  NameBKI='УБКІ'
order by Priority";
	///$sql="SELECT NameBki FROM `parameterBKI` where status=1 order by Priority";
	$resBkis=DatabaseHandler::GetAll($sql);
  $mbkiExists=0;
  $ubkiExists=0;
  $pvbkiExists=0;
 
	foreach ($resBkis as $bki) {
		
if (($bki['NameBki']=='МБКІ')  and (($statusCheck==0) or  ($statusCheck==1))){
	
	  $mbkiExists=2;

$username = $mbkilogin;

$password =$mbkipassword;

$StateCode=$inn;
$sql="select IFNULL(CreditInfoId,0) CreditInfoId,IFNULL(id,0) idmbki from credit_buroMBKI where inn='$StateCode' and (id=$idmbki or $idmbki=0) and CreatedAt>= CURDATE() - INTERVAL {$daybki} DAY order by id desc";
print 'Start';
$res=DatabaseHandler::GetAll($sql);

if(count($res)>0){
	if (isset($res[0]['CreditInfoId']) and $res[0]['CreditInfoId']<>''){
$CreditInfoId=$res[0]['CreditInfoId'];
	} else $CreditInfoId=0;
$idmbki=$res[0]['idmbki'];

} else {$CreditInfoId=0;$idmbki=0;}

if (is_null($CreditInfoId)){$CreditInfoId=0;}

if (is_null($idmbki)){$idmbki=0;}
if ($CreditInfoId==0) 
{
print 'Start2';
$sql="select  CreditInfoId,  ResultCode from Credit_buroMBKIReq where StateCode='$StateCode' order by id desc";

$resMb=DatabaseHandler::GetAll($sql);

if(count($resMb)==0){
$sql="insert into testLog(`test`, `inn`, `type`, `applicationid`)  values('Пошук інформації в МБКІ','$StateCode',1,$applicationid)";


//DatabaseHandler::Execute($sql);
$wsdl = 'https://secure.credithistory.com.ua/service/api/index.php?wsdl';
$client1 = new nusoap_client($wsdl,true);

$headers = 

"<m:CigWsHeader xmlns:m=\"http://ws.creditinfo.com/\"><m:UserName>$username</m:UserName><m:Password>$password</m:Password><m:Version>1_0</m:Version><m:Culture>uk-UA</m:Culture><m:SecurityToken>String</m:SecurityToken><m:UserId>0</m:UserId></m:CigWsHeader>";

//Шлем запрос на поиск

$search = "<Username xsi:type=\"xsd:string\">$username</Username><Password xsi:type=\"xsd:string\">$password</Password><Number xsi:type=\"xsd:string\">$StateCode</Number><NumberType xsi:type=\"xsd:int\">130</NumberType>";


$result_search=$client1->call('Queryresult',$search, 'http://ws.creditinfo.com/');

//Получаем Creditinfoid



$xml= $client1->responseData;
$xmlr = simplexml_load_string($xml );
$doc = new DOMDocument();

$doc->loadXML($xml);
 $doc->formatOutput = true;
 $arrayResult=array();
$arrayResult['Surname']=addslashes($doc->getElementsByTagName('Surname')->item(0)->nodeValue); 
$arrayResult['Name']=addslashes($doc->getElementsByTagName('Name')->item(0)->nodeValue);
$arrayResult['Fathername']=addslashes($doc->getElementsByTagName('Fathername')->item(0)->nodeValue);
$arrayResult['CreditInfoId']=$doc->getElementsByTagName('Creditinfoid')->item(0)->nodeValue;

$arrayResult['IsGarantor']=$doc->getElementsByTagName('IsGarantor')->item(0)->nodeValue;
$arrayResult['LastSearch']=$doc->getElementsByTagName('Lastsearch')->item(0)->nodeValue;
$arrayResult['ResultCode']=$doc->getElementsByTagName('Resultcode')->item(0)->nodeValue;
$arrayResult['Message']=addslashes($doc->getElementsByTagName('Message')->item(0)->nodeValue);
$ResultCode = $doc->getElementsByTagName('Resultcode')->item(0)->nodeValue;
foreach($arrayResult as $k => $v) {
  $$k = $v;
}


if (is_null($CreditInfoId)) {$CreditInfoId=0;}
if (is_null($IsGarantor)) {$IsGarantor=0;}
$Message=addslashes(stripslashes($Message));

$sql="insert into Credit_buroMBKIReq(`Surname`, `Name`, `Fathername`, `CreditInfoId`, `StateCode`, `IsGarantor`, `LastSearch`, `ResultCode`, `Message`)  values('$Surname','$Name','$Fathername',$CreditInfoId,'$StateCode',$IsGarantor,'$LastSearch',$ResultCode,'$Message')";

print $sql;
$results_array=DatabaseHandler::Execute($sql);


} else {
	$ResultCode=$resMb[0]['ResultCode'];
	$CreditInfoId=$resMb[0]['CreditInfoId'];
	
}
}
else $ResultCode=1101;

$ip = $_SERVER['REMOTE_ADDR'];

$sql="select IFNULL(xml_data,'') xml,id from credit_buroMBKI where inn='$StateCode' and CreatedAt>= CURDATE() - INTERVAL 30 DAY";

$xmlresults=DatabaseHandler::GetAll($sql);
if (count($xmlresults)>0){
$idmbki=$xmlresults[0]['id']; 
$xmlresult=$xmlresults[0]['xml']; 
print 'Start3';
} else {
$idmbki=0; 
$xmlresult=''; 
}

if ($xmlresult==''){
$client2 = new nusoap_client('https://secure.credithistory.com.ua/service/service.asmx?WSDL',true,false,false,false,false,0,300);


$resultcode=$ResultCode;

if($resultcode==1102) {$cid = '';}else{$cid = "$CreditInfoId";}

//Если не нашли, не включаем работу дальше

if ($resultcode==1101){$nextstep = 'True';} else {$nextstep = 'False';}
print $resultcode;
print $nextstep;
if($nextstep == 'True') {
$headers = 

"<m:CigWsHeader xmlns:m=\"http://ws.creditinfo.com/\"><m:UserName>$username</m:UserName><m:Password>$password</m:Password><m:Version>1_0</m:Version><m:Culture>uk-UA</m:Culture><m:SecurityToken></m:SecurityToken><m:UserId>0</m:UserId></m:CigWsHeader>";
$client2->setHeaders($headers,'http://ws.creditinfo.com/');

$raw_xml ="<m:GetReport xmlns:m=\"http://ws.creditinfo.com/\"><m:reportId>200017</m:reportId><m:doc><keyValue><ciid>$cid</ciid><creditinfoId>$cid</creditinfoId><remoteIp>$ip</remoteIp><reportVersion>2</reportVersion></keyValue></m:doc></m:GetReport>";

$result=$client2->call('GetReport',$raw_xml, 'http://ws.creditinfo.com/');

$xmlresult = $client2->responseData;
$xml = simplexml_load_string($xmlresult );
$sql="insert into testLog(`test`, `inn`, `type`, `applicationid`)  values('Отримання звіту з МБКІ','$StateCode',1,$applicationid)";


//DatabaseHandler::Execute($sql);

$doc = new DOMDocument();

$doc->loadXML($xml);
$books = $doc->getElementsByTagName("Report");
$successNodeExists = count($books);

if ($successNodeExists) {print "<br />"; print '1'; print "<br />";}
else {print "<br />"; print '2';print "<br />";}
$xmm=$xml->xpath('//Report');
if (isset($xmm[0]->Subject )) {
$xmlresult1=addslashes($xmlresult);	
$sql="insert into credit_buroMBKI(id,xml_data,BKI,CreditInfoId,inn) values(null,'$xmlresult1',1,$CreditInfoId,$StateCode)";

$results_array=DatabaseHandler::Execute($sql);
$sql="SELECT LAST_INSERT_ID()";
$idmbki=DatabaseHandler::GetOne($sql); 
} else {$xmlresult='';$idmbki=0;} 
}
if ($xmlresult<>''){
	
 $xml = simplexml_load_string($xmlresult );
$doc = new DOMDocument();
$doc->loadXML($xmlresult);
$filembki='';
$filembki="mbki_{$inn}_{$nameFolder}";
 $doc->save("{$filembki}.xml");
$mbkiExists=2;
copy("{$filembki}.xml","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/xml_bki/mbki_".$inn."_".date('Ymd').".xml");


$xmm=$xml->xpath('//Report');
if (isset($xmm[0]->Subject )) {
	print 'ok';
foreach ($xmm[0]->Subject as $node) {

    // now you can use $node without going insane about parsing
 $CreditinfoId=($node->CreditinfoId);
 
$SubjectType=($node->SubjectType);
if (isset($node->FIUniqueNumber) and ($node->FIUniqueNumber<>'')){
$FIUniqueNumber=($node->FIUniqueNumber);
} else {$FIUniqueNumber=0;}

$TaxpayerNumber=($node->TaxpayerNumber);
$Passport=($node->Passport);
$DateOfBirth=($node->DateOfBirth);
$Gender=($node->Gender);
$Surname=addslashes($node->Surname);
$Name=addslashes($node->Name);
$FathersName=addslashes($node->FathersName);
$BirthName=addslashes($node->BirthName);
$CityOfBirth=addslashes($node->CityOfBirth);
$Residency=addslashes($node->Residency);
$Nationality=addslashes($node->Nationality);
$Education=addslashes($node->Education);
$EducationallyQualifyingLevel=addslashes($node->EducationallyQualifyingLevel);
$AcademicDegree=addslashes($node->AcademicDegree);
$AcademicStatus=addslashes($node->AcademicStatus);




$MaritialStatus=($node->MaritialStatus);
$Classification=addslashes($node->Classification);

$sql="insert into subjectMBKI values(null,$CreditinfoId,'$SubjectType',
$FIUniqueNumber,
'$TaxpayerNumber',
'$Passport',
'$DateOfBirth',
'$Gender',
'$Surname',
'$Name',
'$FathersName',
'$BirthName',
'$CityOfBirth',
'$Residency',
'$Nationality',
'$Education',
'$EducationallyQualifyingLevel',
'$AcademicDegree',
'$AcademicStatus',
'$MaritialStatus',
'$Classification',
$idmbki,
$applicationid
)";
print $sql;
DatabaseHandler::Execute($sql);
}
$sql="SELECT LAST_INSERT_ID()";
$subjectid=DatabaseHandler::GetOne($sql); 

foreach ($xmm[0]->Addresses->Address as $node) {


$AddressType=addslashes($node->AddressType );
$Street=addslashes($node->Street);
$City=addslashes($node->City);
$Zipcode=addslashes($node->Zipcode);
$Region=addslashes($node->Region);
$Country=addslashes($node->Country);
$District=addslashes($node->District);
$AdditionalInformation=addslashes($node->AdditionalInformation);
$sql="insert into addressMBKI values(null,$CreditinfoId,'$AddressType',
'$Street',
'$City',
'$Zipcode',
'$Region',
'$Country',
'$District',
'$AdditionalInformation',
$subjectid
)";
print $sql;
DatabaseHandler::Execute($sql); 
}
foreach ($xmm[0]->Contacts->Contact as $node) {

$ExportCode=($node->ExportCode );
$ImportCode=($node->ImportCode);
$Name=addslashes($node->Name);
$Value=addslashes($node->Value);

 $sql="insert into contactMBKI values(null,$CreditinfoId,'$ExportCode',
$ImportCode,
'$Name',
'$Value',
$subjectid

)";
print $sql;
DatabaseHandler::Execute($sql); 
}
foreach ($xmm[0]->Identifications->Identification as $node) {



	
$IdType= ($node->IdType  );
$RegistrationDate=($node->RegistrationDate );
$DocumentNumber=addslashes($node->DocumentNumber);
if (isset($node->IssuedBy)){
$IssuedBy=addslashes($node->IssuedBy);
} else $IssuedBy='';
if (isset($node->IssueDate)){
$IssueDate=($node->IssueDate);
} else $IssueDate=NULL;
    // move the pointer to the next product
 $sql="insert into identificationMBKI values(null,$CreditinfoId,'$IdType',
'$RegistrationDate',
'$DocumentNumber',
'$IssuedBy',
'$IssueDate',
$subjectid
)";
print $sql;
DatabaseHandler::Execute($sql); 
}


foreach ($xmm[0]->Relations->Relation as $node) {

	$State=addslashes($node->State);
$JobTitle=addslashes($node->JobTitle );
$CompanyName=addslashes($node->CompanyName );
$SubjectsPosition=addslashes($node->SubjectsPosition);
$RegistrationNumber=addslashes($node->RegistrationNumber);
$StartDate=($node->StartDate);
$Address=addslashes($node->Address);
$ProviderCode=addslashes($node->ProviderCode);


    // move the pointer to the next product
  $sql="insert into relationMBKI values(null,$CreditinfoId,'$State',
'$JobTitle',
'$CompanyName',
'$SubjectsPosition',
'$RegistrationNumber',
'$StartDate',
'$Address',
'$ProviderCode',
$subjectid
)";
 print $sql;
DatabaseHandler::Execute($sql); 



}
foreach ($xmm[0]->Contracts->Contract as $node) {
	
 

	
$ExportCode=($node->ExportCode);
$ImportCode=($node->ImportCode);	
	$ContractType=($node->ContractType);
	$ContractPosition=($node->ContractPosition);

	$ContractRole=($node->ContractRole);
	$ContractPhase=($node->ContractPhase );
		$CodeOfContract=($node->CodeOfContract);
		$ProtestOfSubject=($node->ProtestOfSubject);
		$PurposeOfCredit=($node->PurposeOfCredit );	
		$CurrencyCode=($node->CurrencyCode );
		
$NegativeStatusExportCode=($node->NegativeStatus->ExportCode);
		$NegativeStatusImportCode=addslashes($node->NegativeStatus->ImportCode);
		$NegativeStatusValue=addslashes($node->NegativeStatus->Value);
			$SubjectRole=($node->SubjectRole );
		$ContractStatusExportCode=($node->ContractStatus->ExportCode);
		$ContractStatusImportCode=($node->ContractStatus->ImportCode);
		$ContractStatusValue=addslashes($node->ContractStatus->Value);
	$DateOfApplication=($node->DateOfApplication);
		$CreditStartDate=($node->CreditStartDate);
		$ContractEndDate=($node->ContractEndDate);
		$PenalityDate=($node->PenalityDate);
		$TotalAmountAmountType=($node->TotalAmount->AmountType);
		$TotalAmountCurrency=($node->TotalAmount->Currency);
		$TotalAmountValue=isset($node->TotalAmount->Value)?$node->TotalAmount->Value:0;
		$FactualRepaymentDate=isset($node->FactualRepaymentDate)?$node->FactualRepaymentDate:'';
		$NumberOfOutstandingInstalments=($node->NumberOfOutstandingInstalments);
		$PereodicityOfPayments=($node->PereodicityOfPayments);
		$OutstandingAmountCurrency=($node->OutstandingAmount->Currency);
		$OutstandingAmountValue=($node->OutstandingAmount->Value);
		$MethodOfPayments =isset($node->MethodOfPayments )?$node->MethodOfPayments :'';
		$NumberOfInstalments=($node->NumberOfInstalments);
		$NumberOfOverdueInstalments=isset($node->NumberOfOverdueInstalments)?$node->NumberOfOverdueInstalments:0;
		
	$NumberOfInstalmentsNotPaidAccordingToInterestRate=isset($node->NumberOfInstalmentsNotPaidAccordingToInterestRate)?$node->NumberOfInstalmentsNotPaidAccordingToInterestRate:0;
		$MonthlyInstalmentAmountCurrency=($node->MonthlyInstalmentAmount->Currency);
		$MonthlyInstalmentAmountValue=isset($node->MonthlyInstalmentAmount->Value)?$node->MonthlyInstalmentAmount->Value:0;
		$OverdueAmountCurrency=($node->OverdueAmount->Currency);
		$OverdueAmountValue=isset($node->OverdueAmount->Value)?$node->OverdueAmount->Value:0;
		$OverdueAmountValue=str_replace(',','.',$OverdueAmountValue);
			$OverdueAmountPaymentCount=isset($node->OverdueAmount->PaymentCount)?$node->OverdueAmount->PaymentCount:0;
					$DueInterestAmountCurrency=($node->DueInterestAmount->Currency);	
								$DueInterestAmountValue=isset($node->DueInterestAmount->Value)?$node->DueInterestAmount->Value:0;		
		
			$Creditor=($node->Creditor);
			$CreditorType=isset($node->CreditorType)?$node->CreditorType:'';
				$InteresRate=isset($node->InteresRate)?$node->InteresRate:'0';
				if ($InteresRate=='') $InteresRate='0';
				
						if (is_null($InteresRate)) $InteresRate='0';
						
						$LastUpdateContract=($node->LastUpdateContract);
					$AccountingDate=($node->AccountingDate);
					$DateOfSignature=($node->DateOfSignature);
							$Collaterals=($node->Collaterals);
						$ResidualAmountCurrency=isset($node->ResidualAmount->Currency)?$node->ResidualAmount->Currency:'';
			$ResidualAmountValue=isset($node->ResidualAmount->Value)?$node->ResidualAmount->Value:0;
		$ResidualAmountValue=str_replace(',','.',	$ResidualAmountValue);
			
	
				$ExportCode1=$node->Roles->Role->ExportCode;

$ImportCode1=($node->Roles->Role->ImportCode);
$SubjectRole1=($node->Roles->Role->SubjectRole);
$LastUpdateSubject1=($node->Roles->Role->LastUpdateSubject);
$IdentificationType1=($node->Roles->Role->Identification->IdentificationType);
$IdentificationValue1=($node->Roles->Role->Identification->IdentificationValue);
	$sql="insert into contractsMBKI values(null,$CreditinfoId,
'$ExportCode',
$ImportCode,
'$ContractType',
$ContractPosition,
'$ContractRole',
'$ContractPhase',
'$CodeOfContract',
'$PurposeOfCredit',
'$CurrencyCode',
'$NegativeStatusExportCode',
'$NegativeStatusImportCode',
'$NegativeStatusValue',
'$SubjectRole',
'$ContractStatusExportCode',
'$ContractStatusImportCode',
'$ContractStatusValue',
'$DateOfApplication',
'$CreditStartDate',
'$ContractEndDate',
'$PenalityDate',
'$TotalAmountAmountType',
'$TotalAmountCurrency',
$TotalAmountValue,
'$FactualRepaymentDate',
'$NumberOfOutstandingInstalments',
'$PereodicityOfPayments',
'$OutstandingAmountCurrency',
'$OutstandingAmountValue',
'$MethodOfPayments',
'$NumberOfInstalments',
$NumberOfOverdueInstalments,
$NumberOfInstalmentsNotPaidAccordingToInterestRate,
'$MonthlyInstalmentAmountCurrency',
$MonthlyInstalmentAmountValue,
'$OverdueAmountCurrency',
$OverdueAmountValue,
$OverdueAmountPaymentCount,
'$DueInterestAmountCurrency',
$DueInterestAmountValue,
'$Creditor',
'$CreditorType',
'$InteresRate',
'$LastUpdateContract',
'$AccountingDate',
'$DateOfSignature',
'$Collaterals',
$subjectid,
'$ResidualAmountCurrency',
$ResidualAmountValue



)";

print $sql;
DatabaseHandler::Execute($sql);
$sql="SELECT LAST_INSERT_ID()";
$lastinsertId=DatabaseHandler::GetOne($sql); 
 $sql="insert into contractsrolesMBKI values(null,$CreditinfoId,$lastinsertId,
'$ExportCode1',
$ImportCode1,
'$SubjectRole1',
'$LastUpdateSubject1',
'$IdentificationType1',
'$IdentificationValue1',
$subjectid

)";

DatabaseHandler::Execute($sql); 
 foreach($node->HistoricalCalendar as $cal){  
if ($cal['months']=='-12') {

foreach($cal->Months as $item)
{	

 

	$description=($item->Description);
	$Month1=($item->Month1->Month);
	$Month1Value=($item->Month1->Value);
	
		$Month2=($item->Month2->Month);
$Month2Value=($item->Month2->Value);

		$Month3=($item->Month3->Month);
	$Month3Value=($item->Month3->Value);

		$Month4=($item->Month4->Month);
	$Month4Value=($item->Month4->Value);

		$Month5=($item->Month5->Month);
	$Month5Value=($item->Month5->Value);

		$Month6=($item->Month6->Month);
	$Month6Value=($item->Month6->Value);

		$Month7=($item->Month7->Month);
	$Month7Value=($item->Month7->Value);

		$Month8=($item->Month8->Month);
	$Month8Value=($item->Month8->Value);

		$Month9=($item->Month9->Month);
	$Month9Value=($item->Month9->Value);

		$Month10=($item->Month10->Month);
	$Month10Value=($item->Month10->Value);

		$Month11=($item->Month11->Month);
	$Month11Value=($item->Month11->Value);

		$Month12=($item->Month12->Month);
	$Month12Value=($item->Month12->Value);
									
		
 $sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
0,
'$Month2',
0,
'$Month3',
0,
'$Month4',
0,
'$Month5',
0,
'$Month6',
0,
'$Month7',
0,
'$Month8',
0,
'$Month9',
0,
'$Month10',
0,
'$Month11',
0,
'$Month12',
0,
$subjectid
,'-12'
)";					
print "<br />";			
print $sql;
print "<br />";	
	DatabaseHandler::Execute($sql); 	
    // move the pointer to the next product
 
}
foreach($cal->HCCreditCardUsedInMonth  as $item)
{
	

	$description=($item->Description);
	$Month1=($item->Month1->Value);

	
		$Month2=($item->Month2->Value);


		$Month3=($item->Month3->Value);


		$Month4=($item->Month4->Value);


		$Month5=($item->Month5->Value);


		$Month6=($item->Month6->Value);


		$Month7=($item->Month7->Value);


		$Month8=($item->Month8->Value);


		$Month9=($item->Month9->Value);


		$Month10=($item->Month10->Value);


		$Month11=($item->Month11->Value);


		$Month12=($item->Month12->Value);

									
		
 $sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
0,
'$Month2',
0,
'$Month3',
0,
'$Month4',
0,
'$Month5',
0,
'$Month6',
0,
'$Month7',
0,
'$Month8',
0,
'$Month9',
0,
'$Month10',
0,
'$Month11',
0,
'$Month12',
0,
$subjectid
,'-12'
)";					
print "<br />";			
print $sql;
print "<br />";	
	DatabaseHandler::Execute($sql); 	
}
foreach($cal->HCCreditUsedInMonth  as $item)
{
	

	$description=($item->Description);
	$Month1=($item->Month1->Value);

	
		$Month2=($item->Month2->Value);


		$Month3=($item->Month3->Value);


		$Month4=($item->Month4->Value);


		$Month5=($item->Month5->Value);


		$Month6=($item->Month6->Value);


		$Month7=($item->Month7->Value);


		$Month8=($item->Month8->Value);


		$Month9=($item->Month9->Value);


		$Month10=($item->Month10->Value);


		$Month11=($item->Month11->Value);


		$Month12=($item->Month12->Value);

									
		
 $sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
0,
'$Month2',
0,
'$Month3',
0,
'$Month4',
0,
'$Month5',
0,
'$Month6',
0,
'$Month7',
0,
'$Month8',
0,
'$Month9',
0,
'$Month10',
0,
'$Month11',
0,
'$Month12',
0,
$subjectid
,'-12'
)";					
print "<br />";			
print $sql;
print "<br />";	
	DatabaseHandler::Execute($sql); 	
}

foreach($cal->HCTotalNumberOfOverdueInstalments as $item)
{	

 print "<br />";
 print_r($cal->HCTotalNumberOfOverdueInstalments) ;
 print "<br />";
	$description=($item->Description);

	$Month1=($item->Month1->Month);
	if (isset($item->Month1->Value)) 	$Month1Value=($item->Month1->Value);
	else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;

	$Month1Value=str_replace(',','.',$Month1Value);
		$Month2=($item->Month2->Month);
		if (isset($item->Month2->Value)) $Month2Value=($item->Month2->Value);
		else $Month2Value=0;
if ($Month2Value=='-')  $Month2Value=0;

$Month2Value=str_replace(',','.',$Month2Value);
 		$Month3=($item->Month3->Month);
			if (isset($item->Month3->Value)) 	$Month3Value=($item->Month3->Value);
else $Month3Value=0;
if ($Month3Value=='-')  $Month3Value=0;
$Month3Value=str_replace(',','.',$Month3Value);
 		$Month4=($item->Month4->Month);
		if (isset($item->Month4->Value)) 	$Month4Value=($item->Month4->Value);
		else $Month4Value=0;
if ($Month4Value=='-')  $Month4Value=0;
$Month4Value=str_replace(',','.',$Month4Value);
 		$Month5=($item->Month5->Month);
		if (isset($item->Month5->Value)) 	$Month5Value=($item->Month5->Value);
		else $Month5Value=0;
if ($Month5Value=='-')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);
 		$Month6=($item->Month6->Month);
		if (isset($item->Month6->Value)) 	$Month6Value=($item->Month6->Value);
		else $Month6Value=0;
if ($Month6Value=='-')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);
		$Month7=($item->Month7->Month);
		if (isset($item->Month7->Value))  	$Month7Value=($item->Month7->Value);
		else $Month7Value=0;
if ($Month7Value=='-')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);
		$Month8=($item->Month8->Month);
			if (isset($item->Month8->Value)) 	$Month8Value=($item->Month8->Value);
			else $Month8Value=0;
if ($Month8Value=='-')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);

		$Month9=($item->Month9->Month);
		if (isset($item->Month9->Value)) 	$Month9Value=($item->Month9->Value);
		else $Month9Value=0;
if ($Month9Value=='-')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);
		$Month10=($item->Month10->Month);
		if (isset($item->Month10->Value))  $Month10Value=($item->Month10->Value);
		else $Month10Value=0;
if ($Month10Value=='-')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);

		$Month11=($item->Month11->Month);
		if (isset($item->Month11->Value)) 	$Month11Value=($item->Month11->Value);
		else $Month11Value=0;
if ($Month11Value=='-')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);
		$Month12=($item->Month12->Month);
		if (isset($item->Month12->Value))  $Month12Value=($item->Month12->Value);
		else $Month12Value=0;
if ($Month12Value=='-')  $Month12Value=0;
$Month12Value=str_replace(',','.',$Month12Value);

		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month3',
$Month3Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
,'-12'
)";		
print "<br />";			
print $sql;
print "<br />";	
	DatabaseHandler::Execute($sql); 			


    // move the pointer to the next product
 
}
// print '\n';
// print "<br />";

foreach($cal->HCResidualAmount as $item)
{	

  print "<br />";
 print_r($cal->HCResidualAmount) ;
 print "<br />";

	$description=($item->Description);
	if (isset($item->Month1->Month)){
	$Month1=($item->Month1->Month);	
	}
else $Month1='';
if (isset($item->Month1->Value)){
$Month1Value=($item->Month1->Value);
} else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	$Month1Value=str_replace(',','.',$Month1Value);
if (isset($item->Month2->Month)){
	$Month2=($item->Month2->Month);	
	}
else $Month2='';
if (isset($item->Month2->Value)){
$Month2Value=($item->Month2->Value);
} else $Month2Value=0;

	if ($Month2Value=='-')  $Month2Value=0;
	$Month2Value=str_replace(',','.',$Month2Value);
	if (isset($item->Month3->Month)){
	$Month3=($item->Month3->Month);	
	}
else $Month3='';
if (isset($item->Month3->Value)){
$Month3Value=($item->Month3->Value);
} else $Month3Value=0;
if ($Month3Value=='-')  $Month3Value=0;
$Month3Value=str_replace(',','.',$Month3Value);
		$Month4=($item->Month4->Month);
		if (isset($item->Month4->Value)) 	$Month4Value=($item->Month4->Value);
		else $Month4Value=0;
if ($Month4Value=='-')  $Month4Value=0;
$Month4Value=str_replace(',','.',$Month4Value);
	$Month4Value=str_replace(' ','',$Month4Value);
		$Month5=($item->Month5->Month);
			if (isset($item->Month5->Value)) 	$Month5Value=($item->Month5->Value);
			else $Month5Value=0;
if ($Month5Value=='-')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);
	$Month5Value=str_replace(' ','',$Month5Value);
		$Month6=($item->Month6->Month);
		if (isset($item->Month6->Value)) 
	$Month6Value=($item->Month6->Value);
else $Month6Value=0;
if ($Month6Value=='-')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);
	$Month6Value=str_replace(' ','',$Month6Value);
		$Month7=($item->Month7->Month);
			if (isset($item->Month7->Value)) 
	$Month7Value=($item->Month7->Value);
else 	$Month7Value=0;
if ($Month7Value=='-')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);
// print $Month7Value;
	$Month7Value=str_replace(' ','',$Month7Value);
// print $Month7Value;
	$Month8=($item->Month8->Month);
		if (isset($item->Month8->Value)) 	$Month8Value=($item->Month8->Value);
		else $Month8Value=0;
if ($Month8Value=='-')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);
	$Month8Value=str_replace(' ','',$Month8Value);
	$Month9=($item->Month9->Month);
	if (isset($item->Month9->Value)) 	$Month9Value=($item->Month9->Value);
else $Month9Value=0;
if ($Month9Value=='-')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);
			$Month9Value=str_replace(' ','',$Month9Value);
			$Month10=($item->Month10->Month);
			if (isset($item->Month10->Value)) 	$Month10Value=($item->Month10->Value);
			else $Month10Value=0;
if ($Month10Value=='-')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);
	$Month10Value=str_replace(' ','',$Month10Value);
	$Month11=($item->Month11->Month);
	if (isset($item->Month11->Value)) 	$Month11Value=($item->Month11->Value);
	else $Month11Value=0;
if ($Month11Value=='-')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);
			$Month11Value=str_replace(' ','',$Month11Value);
		$Month12=($item->Month12->Month);
		if (isset($item->Month12->Value)) 	$Month12Value=($item->Month12->Value);
		else $Month12Value=0;
if ($Month12Value=='-')  $Month12Value=0;
$Month12Value=str_replace(',','.',$Month12Value);
			$Month1Value=preg_replace('/[^0-9]/', '', $Month1Value);
	$Month2Value=preg_replace('/[^0-9]/', '', $Month2Value);

	$Month3Value=preg_replace('/[^0-9]/', '', $Month3Value);
		$Month4Value=preg_replace('/[^0-9]/', '', $Month4Value);
			$Month5Value=preg_replace('/[^0-9]/', '', $Month5Value);
				$Month6Value=preg_replace('/[^0-9]/', '', $Month6Value);
					$Month7Value=preg_replace('/[^0-9]/', '', $Month7Value);
						$Month8Value=preg_replace('/[^0-9]/', '', $Month8Value);
							$Month9Value=preg_replace('/[^0-9]/', '', $Month9Value);
								$Month10Value=preg_replace('/[^0-9]/', '', $Month10Value);
									$Month11Value=preg_replace('/[^0-9]/', '', $Month11Value);
										$Month12Value=preg_replace('/[^0-9]/', '', $Month12Value);
		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month3',
$Month3Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
,'-12'
)";					
print "<br />";			
print $sql;
print "<br />";	
	DatabaseHandler::Execute($sql); 	
									
			


    // move the pointer to the next product
 
}

foreach($cal->HCUsedAmount as $item)
{	

  print "<br />";
 print_r($cal->HCResidualAmount) ;
 print "<br />";

	$description=($item->Description);
	if (isset($item->Month1->Month)){
	$Month1=($item->Month1->Month);	
	}
else $Month1='';
if (isset($item->Month1->Value)){
$Month1Value=($item->Month1->Value);
} else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	$Month1Value=str_replace(',','.',$Month1Value);
if (isset($item->Month2->Month)){
	$Month2=($item->Month2->Month);	
	}
else $Month2='';
if (isset($item->Month2->Value)){
$Month2Value=($item->Month2->Value);
} else $Month2Value=0;
	if ($Month2Value=='-')  $Month2Value=0;
	$Month2Value=str_replace(',','.',$Month2Value);
if (isset($item->Month3->Month)){
	$Month3=($item->Month3->Month);	
	}
else $Month3='';
if (isset($item->Month3->Value)){
$Month3Value=($item->Month3->Value);
} else $Month3Value=0;
if ($Month3Value=='-')  $Month3Value=0;
$Month3Value=str_replace(',','.',$Month3Value);
	
		$Month4=($item->Month4->Month);
		if (isset($item->Month4->Value)) 	$Month4Value=($item->Month4->Value);
		else $Month4Value=0;
if ($Month4Value=='-')  $Month4Value=0;
$Month4Value=str_replace(',','.',$Month4Value);
	$Month4Value=str_replace(' ','',$Month4Value);
		$Month5=($item->Month5->Month);
			if (isset($item->Month5->Value)) 	$Month5Value=($item->Month5->Value);
			else $Month5Value=0;
if ($Month5Value=='-')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);
	$Month5Value=str_replace(' ','',$Month5Value);
		$Month6=($item->Month6->Month);
		if (isset($item->Month6->Value)) 
	$Month6Value=($item->Month6->Value);
else $Month6Value=0;
if ($Month6Value=='-')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);
	$Month6Value=str_replace(' ','',$Month6Value);
		$Month7=($item->Month7->Month);
			if (isset($item->Month7->Value)) 
	$Month7Value=($item->Month7->Value);
else 	$Month7Value=0;
if ($Month7Value=='-')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);
// print $Month7Value;
	$Month7Value=str_replace(' ','',$Month7Value);
// print $Month7Value;
	$Month8=($item->Month8->Month);
		if (isset($item->Month8->Value)) 	$Month8Value=($item->Month8->Value);
		else $Month8Value=0;
if ($Month8Value=='-')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);
	$Month8Value=str_replace(' ','',$Month8Value);	
	$Month9=($item->Month9->Month);
	if (isset($item->Month9->Value)) 	$Month9Value=($item->Month9->Value);
else $Month9Value=0;
if ($Month9Value=='-')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);
			$Month9Value=str_replace(' ','',$Month9Value);
		$Month10=($item->Month10->Month);
			if (isset($item->Month10->Value)) 	$Month10Value=($item->Month10->Value);
			else $Month10Value=0;
if ($Month10Value=='-')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);
	$Month10Value=str_replace(' ','',$Month10Value);	
	$Month11=($item->Month11->Month);
	if (isset($item->Month11->Value)) 	$Month11Value=($item->Month11->Value);
	else $Month11Value=0;
if ($Month11Value=='-')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);
			$Month11Value=str_replace(' ','',$Month11Value);
		$Month12=($item->Month12->Month);
		if (isset($item->Month12->Value)) 	$Month12Value=($item->Month12->Value);
		else $Month12Value=0;
if ($Month12Value=='-')  $Month12Value=0;
$Month12Value=str_replace(',','.',$Month12Value);
			
			$Month1Value=preg_replace('/[^0-9]/', '', $Month1Value);
	$Month2Value=preg_replace('/[^0-9]/', '', $Month2Value);

	$Month3Value=preg_replace('/[^0-9]/', '', $Month3Value);
		$Month4Value=preg_replace('/[^0-9]/', '', $Month4Value);
			$Month5Value=preg_replace('/[^0-9]/', '', $Month5Value);
				$Month6Value=preg_replace('/[^0-9]/', '', $Month6Value);
					$Month7Value=preg_replace('/[^0-9]/', '', $Month7Value);
						$Month8Value=preg_replace('/[^0-9]/', '', $Month8Value);
							$Month9Value=preg_replace('/[^0-9]/', '', $Month9Value);
								$Month10Value=preg_replace('/[^0-9]/', '', $Month10Value);
									$Month11Value=preg_replace('/[^0-9]/', '', $Month11Value);
										$Month12Value=preg_replace('/[^0-9]/', '', $Month12Value);
		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month3',
$Month3Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
,'-12'
)";					
print "<br />";			
print $sql;
print "<br />";	
	DatabaseHandler::Execute($sql); 	
									
			


    // move the pointer to the next product
 
}

foreach($cal->HCTotalOverdueAmount as $item)
{	

  print "<br />";
 print_r($cal->HCTotalOverdueAmount) ;
 print "<br />";

	$description=($item->Description);
	if (isset($item->Month1->Month)){
	$Month1=($item->Month1->Month);	
	}
else $Month1='';
if (isset($item->Month1->Value)){
$Month1Value=($item->Month1->Value);
} else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	$Month1Value=str_replace(',','.',$Month1Value);
if (isset($item->Month2->Month)){
	$Month2=($item->Month2->Month);	
	}
else $Month2='';
if (isset($item->Month2->Value)){
$Month2Value=($item->Month2->Value);
} else $Month2Value=0;
	if ($Month2Value=='-')  $Month2Value=0;
	$Month2Value=str_replace(',','.',$Month2Value);
if (isset($item->Month3->Month)){
	$Month3=($item->Month3->Month);	
	}
else $Month3='';
if (isset($item->Month3->Value)){
$Month3Value=($item->Month3->Value);
} else $Month3Value=0;
if ($Month3Value=='-')  $Month3Value=0;
$Month3Value=str_replace(',','.',$Month3Value);
	
		$Month4=($item->Month4->Month);
		if (isset($item->Month4->Value)) 	$Month4Value=($item->Month4->Value);
		else $Month4Value=0;
if ($Month4Value=='-')  $Month4Value=0;
$Month4Value=str_replace(',','.',$Month4Value);
	$Month4Value=str_replace(' ','',$Month4Value);
		$Month5=($item->Month5->Month);
			if (isset($item->Month5->Value)) 	$Month5Value=($item->Month5->Value);
			else $Month5Value=0;
if ($Month5Value=='-')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);
	$Month5Value=str_replace(' ','',$Month5Value);
		$Month6=($item->Month6->Month);
		if (isset($item->Month6->Value)) 
	$Month6Value=($item->Month6->Value);
else $Month6Value=0;
if ($Month6Value=='-')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);
	$Month6Value=str_replace(' ','',$Month6Value);
		$Month7=($item->Month7->Month);
			if (isset($item->Month7->Value)) 
	$Month7Value=($item->Month7->Value);
else 	$Month7Value=0;
if ($Month7Value=='-')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);
// print $Month7Value;
	$Month7Value=str_replace(' ','',$Month7Value);
// print $Month7Value;
	$Month8=($item->Month8->Month);
		if (isset($item->Month8->Value)) 	$Month8Value=($item->Month8->Value);
		else $Month8Value=0;
if ($Month8Value=='-')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);
	$Month8Value=str_replace(' ','',$Month8Value);	
	$Month9=($item->Month9->Month);
	if (isset($item->Month9->Value)) 	$Month9Value=($item->Month9->Value);
else $Month9Value=0;
if ($Month9Value=='-')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);
			$Month9Value=str_replace(' ','',$Month9Value);
		$Month10=($item->Month10->Month);
			if (isset($item->Month10->Value)) 	$Month10Value=($item->Month10->Value);
			else $Month10Value=0;
if ($Month10Value=='-')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);
	$Month10Value=str_replace(' ','',$Month10Value);	
	$Month11=($item->Month11->Month);
	if (isset($item->Month11->Value)) 	$Month11Value=($item->Month11->Value);
	else $Month11Value=0;
if ($Month11Value=='-')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);
			$Month11Value=str_replace(' ','',$Month11Value);
		$Month12=($item->Month12->Month);
		if (isset($item->Month12->Value)) 	$Month12Value=($item->Month12->Value);
		else $Month12Value=0;
if ($Month12Value=='-')  $Month12Value=0;
$Month12Value=str_replace(',','.',$Month12Value);
			
			$Month1Value=preg_replace('/[^0-9]/', '', $Month1Value);
	$Month2Value=preg_replace('/[^0-9]/', '', $Month2Value);

	$Month3Value=preg_replace('/[^0-9]/', '', $Month3Value);
		$Month4Value=preg_replace('/[^0-9]/', '', $Month4Value);
			$Month5Value=preg_replace('/[^0-9]/', '', $Month5Value);
				$Month6Value=preg_replace('/[^0-9]/', '', $Month6Value);
					$Month7Value=preg_replace('/[^0-9]/', '', $Month7Value);
						$Month8Value=preg_replace('/[^0-9]/', '', $Month8Value);
							$Month9Value=preg_replace('/[^0-9]/', '', $Month9Value);
								$Month10Value=preg_replace('/[^0-9]/', '', $Month10Value);
									$Month11Value=preg_replace('/[^0-9]/', '', $Month11Value);
										$Month12Value=preg_replace('/[^0-9]/', '', $Month12Value);
		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month3',
$Month3Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
,'-12'
)";					
print "<br />";			
print $sql;
print "<br />";	
	DatabaseHandler::Execute($sql); 	
									
			


    // move the pointer to the next product
 
}

foreach($cal->HCOverdraft as $item)
{	

  print "<br />";
 print_r($cal->HCTotalOverdueAmount) ;
 print "<br />";

	$description=($item->Description);
	if (isset($item->Month1->Month)){
	$Month1=($item->Month1->Month);	
	}
else $Month1='';
if (isset($item->Month1->Value)){
$Month1Value=($item->Month1->Value);
} else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	$Month1Value=str_replace(',','.',$Month1Value);
if (isset($item->Month2->Month)){
	$Month2=($item->Month2->Month);	
	}
else $Month2='';
if (isset($item->Month2->Value)){
$Month2Value=($item->Month2->Value);
} else $Month2Value=0;
	if ($Month2Value=='-')  $Month2Value=0;
	$Month2Value=str_replace(',','.',$Month2Value);
if (isset($item->Month3->Month)){
	$Month3=($item->Month3->Month);	
	}
else $Month3='';
if (isset($item->Month3->Value)){
$Month3Value=($item->Month3->Value);
} else $Month3Value=0;
if ($Month3Value=='-')  $Month3Value=0;
$Month3Value=str_replace(',','.',$Month3Value);
	
		$Month4=($item->Month4->Month);
		if (isset($item->Month4->Value)) 	$Month4Value=($item->Month4->Value);
		else $Month4Value=0;
if ($Month4Value=='-')  $Month4Value=0;
$Month4Value=str_replace(',','.',$Month4Value);
	$Month4Value=str_replace(' ','',$Month4Value);
		$Month5=($item->Month5->Month);
			if (isset($item->Month5->Value)) 	$Month5Value=($item->Month5->Value);
			else $Month5Value=0;
if ($Month5Value=='-')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);
	$Month5Value=str_replace(' ','',$Month5Value);
		$Month6=($item->Month6->Month);
		if (isset($item->Month6->Value)) 
	$Month6Value=($item->Month6->Value);
else $Month6Value=0;
if ($Month6Value=='-')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);
	$Month6Value=str_replace(' ','',$Month6Value);
		$Month7=($item->Month7->Month);
			if (isset($item->Month7->Value)) 
	$Month7Value=($item->Month7->Value);
else 	$Month7Value=0;
if ($Month7Value=='-')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);
// print $Month7Value;
	$Month7Value=str_replace(' ','',$Month7Value);
// print $Month7Value;
	$Month8=($item->Month8->Month);
		if (isset($item->Month8->Value)) 	$Month8Value=($item->Month8->Value);
		else $Month8Value=0;
if ($Month8Value=='-')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);
	$Month8Value=str_replace(' ','',$Month8Value);	
	$Month9=($item->Month9->Month);
	if (isset($item->Month9->Value)) 	$Month9Value=($item->Month9->Value);
else $Month9Value=0;
if ($Month9Value=='-')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);
			$Month9Value=str_replace(' ','',$Month9Value);
		$Month10=($item->Month10->Month);
			if (isset($item->Month10->Value)) 	$Month10Value=($item->Month10->Value);
			else $Month10Value=0;
if ($Month10Value=='-')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);
	$Month10Value=str_replace(' ','',$Month10Value);	
	$Month11=($item->Month11->Month);
	if (isset($item->Month11->Value)) 	$Month11Value=($item->Month11->Value);
	else $Month11Value=0;
if ($Month11Value=='-')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);
			$Month11Value=str_replace(' ','',$Month11Value);
		$Month12=($item->Month12->Month);
		if (isset($item->Month12->Value)) 	$Month12Value=($item->Month12->Value);
		else $Month12Value=0;
if ($Month12Value=='-')  $Month12Value=0;
$Month12Value=str_replace(',','.',$Month12Value);
			
			$Month1Value=preg_replace('/[^0-9]/', '', $Month1Value);
	$Month2Value=preg_replace('/[^0-9]/', '', $Month2Value);

	$Month3Value=preg_replace('/[^0-9]/', '', $Month3Value);
		$Month4Value=preg_replace('/[^0-9]/', '', $Month4Value);
			$Month5Value=preg_replace('/[^0-9]/', '', $Month5Value);
				$Month6Value=preg_replace('/[^0-9]/', '', $Month6Value);
					$Month7Value=preg_replace('/[^0-9]/', '', $Month7Value);
						$Month8Value=preg_replace('/[^0-9]/', '', $Month8Value);
							$Month9Value=preg_replace('/[^0-9]/', '', $Month9Value);
								$Month10Value=preg_replace('/[^0-9]/', '', $Month10Value);
									$Month11Value=preg_replace('/[^0-9]/', '', $Month11Value);
										$Month12Value=preg_replace('/[^0-9]/', '', $Month12Value);
		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month3',
$Month3Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
,'-12'
)";					
print "<br />";			
print $sql;
print "<br />";	
	DatabaseHandler::Execute($sql); 	
									
			


    // move the pointer to the next product
 
}

}
if ($cal['months']=='-24') {

foreach($cal->Months as $item)
{	

 

	$description=($item->Description);
	$Month1=($item->Month1->Month);
	$Month1Value=($item->Month1->Value);
	
		$Month2=($item->Month2->Month);
$Month2Value=($item->Month2->Value);

		$Month3=($item->Month3->Month);
	$Month3Value=($item->Month3->Value);

		$Month4=($item->Month4->Month);
	$Month4Value=($item->Month4->Value);

		$Month5=($item->Month5->Month);
	$Month5Value=($item->Month5->Value);

		$Month6=($item->Month6->Month);
	$Month6Value=($item->Month6->Value);

		$Month7=($item->Month7->Month);
	$Month7Value=($item->Month7->Value);

		$Month8=($item->Month8->Month);
	$Month8Value=($item->Month8->Value);

		$Month9=($item->Month9->Month);
	$Month9Value=($item->Month9->Value);

		$Month10=($item->Month10->Month);
	$Month10Value=($item->Month10->Value);

		$Month11=($item->Month11->Month);
	$Month11Value=($item->Month11->Value);

		$Month12=($item->Month12->Month);
	$Month12Value=($item->Month12->Value);
									
		
 $sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
0,
'$Month2',
0,
'$Month3',
0,
'$Month4',
0,
'$Month5',
0,
'$Month6',
0,
'$Month7',
0,
'$Month8',
0,
'$Month9',
0,
'$Month10',
0,
'$Month11',
0,
'$Month12',
0,
$subjectid
,'-24'
)";					

	DatabaseHandler::Execute($sql); 	
    // move the pointer to the next product
 
}
foreach($cal->HCTotalNumberOfOverdueInstalments as $item)
{	

 

	$description=($item->Description);

	$Month1=($item->Month1->Month);
	if (isset($item->Month1->Value)) 	$Month1Value=($item->Month1->Value);
	else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	$Month1Value=str_replace(',','.',$Month1Value);
	
		$Month2=($item->Month2->Month);
		if (isset($item->Month2->Value)) $Month2Value=($item->Month2->Value);
		else $Month2Value=0;
if ($Month2Value=='-')  $Month2Value=0;
$Month2Value=str_replace(',','.',$Month2Value);

		$Month3=($item->Month3->Month);
			if (isset($item->Month3->Value)) 	$Month3Value=($item->Month3->Value);
else $Month3Value=0;
if ($Month3Value=='-')  $Month3Value=0;
$Month3Value=str_replace(',','.',$Month3Value);

		$Month4=($item->Month4->Month);
		if (isset($item->Month4->Value)) 	$Month4Value=($item->Month4->Value);
		else $Month4Value=0;
if ($Month4Value=='-')  $Month4Value=0;
$Month4Value=str_replace(',','.',$Month4Value);

		$Month5=($item->Month5->Month);
		if (isset($item->Month5->Value)) 	$Month5Value=($item->Month5->Value);
		else $Month5Value=0;
if ($Month5Value=='-')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);

		$Month6=($item->Month6->Month);
		if (isset($item->Month6->Value)) 	$Month6Value=($item->Month6->Value);
		else $Month6Value=0;
if ($Month6Value=='-')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);

		$Month7=($item->Month7->Month);
		if (isset($item->Month7->Value))  	$Month7Value=($item->Month7->Value);
		else $Month7Value=0;
if ($Month7Value=='-')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);

		$Month8=($item->Month8->Month);
			if (isset($item->Month8->Value)) 	$Month8Value=($item->Month8->Value);
			else $Month8Value=0;
if ($Month8Value=='-')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);


		$Month9=($item->Month9->Month);
		if (isset($item->Month9->Value)) 	$Month9Value=($item->Month9->Value);
		else $Month9Value=0;
if ($Month9Value=='-')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);

		$Month10=($item->Month10->Month);
		if (isset($item->Month10->Value))  $Month10Value=($item->Month10->Value);
		else $Month10Value=0;
if ($Month10Value=='-')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);


		$Month11=($item->Month11->Month);
		if (isset($item->Month11->Value)) 	$Month11Value=($item->Month11->Value);
		else $Month11Value=0;
if ($Month11Value=='-')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);

		$Month12=($item->Month12->Month);
		if (isset($item->Month12->Value))  $Month12Value=($item->Month12->Value);
		else $Month12Value=0;
if ($Month12Value=='-')  $Month12Value=0;
$Month12Value=str_replace(',','.',$Month12Value);


		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month3',
$Month3Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
,'-24'
)";					
print $sql;
	DatabaseHandler::Execute($sql); 			


    // move the pointer to the next product
 
}
foreach($cal->HCResidualAmount as $item)
{	

 

	$description=($item->Description);
	if (isset($item->Month1->Month)){
	$Month1=($item->Month1->Month);	
	}
else $Month1='';
if (isset($item->Month1->Value)){
$Month1Value=($item->Month1->Value);
} else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	$Month1Value=str_replace(',','.',$Month1Value);
if (isset($item->Month2->Month)){
	$Month2=($item->Month2->Month);	
	}
else $Month2='';
if (isset($item->Month2->Value)){
$Month2Value=($item->Month2->Value);
} else $Month2Value=0;
	if ($Month2Value=='-')  $Month2Value=0;
	$Month2Value=str_replace(',','.',$Month2Value);
if (isset($item->Month3->Month)){
	$Month3=($item->Month3->Month);	
	}
else $Month3='';
if (isset($item->Month3->Value)){
$Month3Value=($item->Month3->Value);
} else $Month3Value=0;
if ($Month3Value=='-')  $Month3Value=0;
$Month3Value=str_replace(',','.',$Month3Value);
	
		$Month4=($item->Month4->Month);
		if (isset($item->Month4->Value)) 	$Month4Value=($item->Month4->Value);
		else $Month4Value=0;
if ($Month4Value=='-')  $Month4Value=0;
$Month4Value=str_replace(',','.',$Month4Value);
	$Month4Value=str_replace(' ','',$Month4Value);
		$Month5=($item->Month5->Month);
			if (isset($item->Month4->Value)) 	$Month5Value=($item->Month5->Value);
			else $Month5Value=0;
if ($Month5Value=='-')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);
	$Month5Value=str_replace(' ','',$Month5Value);
		$Month6=($item->Month6->Month);
		if (isset($item->Month6->Value)) 
	$Month6Value=($item->Month6->Value);
else $Month6Value=0;
if ($Month6Value=='-')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);
	$Month6Value=str_replace(' ','',$Month6Value);
		$Month7=($item->Month7->Month);
			if (isset($item->Month7->Value)) 
	$Month7Value=($item->Month7->Value);
else 	$Month7Value=0;
if ($Month7Value=='-')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);
// print $Month7Value;
	$Month7Value=str_replace(' ','',$Month7Value);
// print $Month7Value;
	$Month8=($item->Month8->Month);
		if (isset($item->Month8->Value)) 	$Month8Value=($item->Month8->Value);
		else $Month8Value=0;
if ($Month8Value=='-')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);
	$Month8Value=str_replace(' ','',$Month8Value);	
	$Month9=($item->Month9->Month);
	if (isset($item->Month9->Value)) 	$Month9Value=($item->Month9->Value);
else $Month9Value=0;
if ($Month9Value=='-')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);
			$Month9Value=str_replace(' ','',$Month9Value);
		$Month10=($item->Month10->Month);
			if (isset($item->Month10->Value)) 	$Month10Value=($item->Month10->Value);
			else $Month10Value=0;
if ($Month10Value=='-')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);
	$Month10Value=str_replace(' ','',$Month10Value);	
	$Month11=($item->Month11->Month);
	if (isset($item->Month11->Value)) 	$Month11Value=($item->Month11->Value);
	else $Month11Value=0;
if ($Month11Value=='-')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);
			$Month11Value=str_replace(' ','',$Month11Value);
		$Month12=($item->Month12->Month);
		if (isset($item->Month12->Value)) 	$Month12Value=($item->Month12->Value);
		else $Month12Value=0;
if ($Month12Value=='-')  $Month12Value=0;
$Month12Value=str_replace(',','.',$Month12Value);
			
			$Month1Value=preg_replace('/[^0-9]/', '', $Month1Value);
	$Month2Value=preg_replace('/[^0-9]/', '', $Month2Value);

	$Month3Value=preg_replace('/[^0-9]/', '', $Month3Value);
		$Month4Value=preg_replace('/[^0-9]/', '', $Month4Value);
			$Month5Value=preg_replace('/[^0-9]/', '', $Month5Value);
				$Month6Value=preg_replace('/[^0-9]/', '', $Month6Value);
					$Month7Value=preg_replace('/[^0-9]/', '', $Month7Value);
						$Month8Value=preg_replace('/[^0-9]/', '', $Month8Value);
							$Month9Value=preg_replace('/[^0-9]/', '', $Month9Value);
								$Month10Value=preg_replace('/[^0-9]/', '', $Month10Value);
									$Month11Value=preg_replace('/[^0-9]/', '', $Month11Value);
										$Month12Value=preg_replace('/[^0-9]/', '', $Month12Value);
		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month3',
$Month3Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
,'-24'
)";					
print $sql;
	DatabaseHandler::Execute($sql); 	
									
			


    // move the pointer to the next product
 
}

// print '\n';
// print "<br />";
foreach($cal->HCTotalOverdueAmount as $item)
{	

 

	$description=($item->Description);
	if (isset($item->Month1->Month)){
	$Month1=($item->Month1->Month);	
	}
else $Month1='';
if (isset($item->Month1->Value)){
$Month1Value=($item->Month1->Value);
} else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	$Month1Value=str_replace(',','.',$Month1Value);
if (isset($item->Month2->Month)){
	$Month2=($item->Month2->Month);	
	}
else $Month2='';
if (isset($item->Month2->Value)){
$Month2Value=($item->Month2->Value);
} else $Month2Value=0;
	if ($Month2Value=='-')  $Month2Value=0;
	$Month2Value=str_replace(',','.',$Month2Value);
if (isset($item->Month3->Month)){
	$Month3=($item->Month3->Month);	
	}
else $Month3='';
if (isset($item->Month3->Value)){
$Month3Value=($item->Month3->Value);
} else $Month3Value=0;
if ($Month3Value=='-')  $Month3Value=0;
$Month3Value=str_replace(',','.',$Month3Value);
	
		$Month4=($item->Month4->Month);
		if (isset($item->Month4->Value)) 	$Month4Value=($item->Month4->Value);
		else $Month4Value=0;
if ($Month4Value=='-')  $Month4Value=0;
$Month4Value=str_replace(',','.',$Month4Value);
	$Month4Value=str_replace(' ','',$Month4Value);
		$Month5=($item->Month5->Month);
			if (isset($item->Month4->Value)) 	$Month5Value=($item->Month5->Value);
			else $Month5Value=0;
if ($Month5Value=='-')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);
	$Month5Value=str_replace(' ','',$Month5Value);
		$Month6=($item->Month6->Month);
		if (isset($item->Month6->Value)) 
	$Month6Value=($item->Month6->Value);
else $Month6Value=0;
if ($Month6Value=='-')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);
	$Month6Value=str_replace(' ','',$Month6Value);
		$Month7=($item->Month7->Month);
			if (isset($item->Month7->Value)) 
	$Month7Value=($item->Month7->Value);
else 	$Month7Value=0;
if ($Month7Value=='-')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);
// print $Month7Value;
	$Month7Value=str_replace(' ','',$Month7Value);
// print $Month7Value;
	$Month8=($item->Month8->Month);
		if (isset($item->Month8->Value)) 	$Month8Value=($item->Month8->Value);
		else $Month8Value=0;
if ($Month8Value=='-')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);
	$Month8Value=str_replace(' ','',$Month8Value);	
	$Month9=($item->Month9->Month);
	if (isset($item->Month9->Value)) 	$Month9Value=($item->Month9->Value);
else $Month9Value=0;
if ($Month9Value=='-')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);
			$Month9Value=str_replace(' ','',$Month9Value);
		$Month10=($item->Month10->Month);
			if (isset($item->Month10->Value)) 	$Month10Value=($item->Month10->Value);
			else $Month10Value=0;
if ($Month10Value=='-')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);
	$Month10Value=str_replace(' ','',$Month10Value);	
	$Month11=($item->Month11->Month);
	if (isset($item->Month11->Value)) 	$Month11Value=($item->Month11->Value);
	else $Month11Value=0;
if ($Month11Value=='-')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);
			$Month11Value=str_replace(' ','',$Month11Value);
		$Month12=($item->Month12->Month);
		if (isset($item->Month12->Value)) 	$Month12Value=($item->Month12->Value);
		else $Month12Value=0;
if ($Month12Value=='-')  $Month12Value=0;
$Month12Value=str_replace(',','.',$Month12Value);
			
			$Month1Value=preg_replace('/[^0-9]/', '', $Month1Value);
	$Month2Value=preg_replace('/[^0-9]/', '', $Month2Value);

	$Month3Value=preg_replace('/[^0-9]/', '', $Month3Value);
		$Month4Value=preg_replace('/[^0-9]/', '', $Month4Value);
			$Month5Value=preg_replace('/[^0-9]/', '', $Month5Value);
				$Month6Value=preg_replace('/[^0-9]/', '', $Month6Value);
					$Month7Value=preg_replace('/[^0-9]/', '', $Month7Value);
						$Month8Value=preg_replace('/[^0-9]/', '', $Month8Value);
							$Month9Value=preg_replace('/[^0-9]/', '', $Month9Value);
								$Month10Value=preg_replace('/[^0-9]/', '', $Month10Value);
									$Month11Value=preg_replace('/[^0-9]/', '', $Month11Value);
										$Month12Value=preg_replace('/[^0-9]/', '', $Month12Value);
		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month3',
$Month3Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
,'-24'
)";					
print $sql;
	DatabaseHandler::Execute($sql); 	
									
			


    // move the pointer to the next product
 
}
foreach($cal->HCCreditCardUsedInMonth  as $item)
{
	

	$description=($item->Description);
	$Month1=($item->Month1->Value);

	
		$Month2=($item->Month2->Value);


		$Month3=($item->Month3->Value);


		$Month4=($item->Month4->Value);


		$Month5=($item->Month5->Value);


		$Month6=($item->Month6->Value);


		$Month7=($item->Month7->Value);


		$Month8=($item->Month8->Value);


		$Month9=($item->Month9->Value);


		$Month10=($item->Month10->Value);


		$Month11=($item->Month11->Value);


		$Month12=($item->Month12->Value);

									
		
 $sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
0,
'$Month2',
0,
'$Month3',
0,
'$Month4',
0,
'$Month5',
0,
'$Month6',
0,
'$Month7',
0,
'$Month8',
0,
'$Month9',
0,
'$Month10',
0,
'$Month11',
0,
'$Month12',
0,
$subjectid
,'-24'
)";					

	DatabaseHandler::Execute($sql); 	
}
foreach($cal->HCUsedAmount as $item)
{	

 print "<br />";
 print_r($cal->HCUsedAmount) ;
 print "<br />";
	$description=($item->Description);

	$Month1=($item->Month1->Month);
	if (isset($item->Month1->Value)) 	$Month1Value=($item->Month1->Value);
	else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	if ($Month1Value=='Відсутність трансакції')  $Month1Value=0;
	$Month1Value=str_replace(',','.',$Month1Value);
		$Month1Value=preg_replace('/[^0-9\.]/', '',$Month1Value);
		$Month2=($item->Month2->Month);
		if (isset($item->Month2->Value)) $Month2Value=($item->Month2->Value);
		else $Month2Value=0;
if ($Month2Value=='-')  $Month2Value=0;
if ($Month2Value=='Відсутність трансакції')  $Month2Value=0;
$Month2Value=str_replace(',','.',$Month2Value);
	$Month2Value=preg_replace('/[^0-9\.]/', '',$Month2Value);
		$Month3=($item->Month3->Month);
			if (isset($item->Month3->Value)) 	$Month3Value=($item->Month3->Value);
else $Month3Value=0;
if ($Month3Value=='-')  $Month3Value=0;
if ($Month3Value=='Відсутність трансакції')  $Month3Value=0;
$Month3Value=str_replace(',','.',$Month3Value);
	$Month3Value=preg_replace('/[^0-9\.]/', '',$Month3Value);
		$Month4=($item->Month4->Month);
		if (isset($item->Month4->Value)) 	$Month4Value=($item->Month4->Value);
		else $Month4Value=0;
if ($Month4Value=='-')  $Month4Value=0;
if ($Month4Value=='Відсутність трансакції')  $Month4Value=0;
$Month4Value=str_replace(',','.',$Month4Value);
	$Month4Value=preg_replace('/[^0-9\.]/', '',$Month4Value);
		$Month5=($item->Month5->Month);
		if (isset($item->Month5->Value)) 	$Month5Value=($item->Month5->Value);
		else $Month5Value=0;
if ($Month5Value=='-')  $Month5Value=0;
if ($Month5Value=='Відсутність трансакції')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);
	$Month5Value=preg_replace('/[^0-9\.]/', '',$Month5Value);
		$Month6=($item->Month6->Month);
		if (isset($item->Month6->Value)) 	$Month6Value=($item->Month6->Value);
		else $Month6Value=0;
if ($Month6Value=='-')  $Month6Value=0;
if ($Month6Value=='Відсутність трансакції')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);
	$Month6Value=preg_replace('/[^0-9\.]/', '',$Month6Value);

		$Month7=($item->Month7->Month);
		if (isset($item->Month7->Value))  	$Month7Value=($item->Month7->Value);
		else $Month7Value=0;
if ($Month7Value=='-')  $Month7Value=0;
if ($Month7Value=='Відсутність трансакції')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);
	$Month7Value=preg_replace('/[^0-9\.]/', '',$Month7Value);
		$Month8=($item->Month8->Month);
			if (isset($item->Month8->Value)) 	$Month8Value=($item->Month8->Value);
			else $Month8Value=0;
if ($Month8Value=='-')  $Month8Value=0;
if ($Month8Value=='Відсутність трансакції')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);
	$Month8Value=preg_replace('/[^0-9\.]/', '',$Month8Value);

		$Month9=($item->Month9->Month);
		if (isset($item->Month9->Value)) 	$Month9Value=($item->Month9->Value);
		else $Month9Value=0;
if ($Month9Value=='-')  $Month9Value=0;
if ($Month9Value=='Відсутність трансакції')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);
	$Month9Value=preg_replace('/[^0-9\.]/', '',$Month9Value);
		$Month10=($item->Month10->Month);
		if (isset($item->Month10->Value))  $Month10Value=($item->Month10->Value);
		else $Month10Value=0;
if ($Month10Value=='-')  $Month10Value=0;
if ($Month10Value=='Відсутність трансакції')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);
	$Month10Value=preg_replace('/[^0-9\.]/', '',$Month10Value);

		$Month11=($item->Month11->Month);
		if (isset($item->Month11->Value)) 	$Month11Value=($item->Month11->Value);
		else $Month11Value=0;
if ($Month11Value=='-')  $Month11Value=0;
if ($Month11Value=='Відсутність трансакції')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);

	$Month11Value=preg_replace('/[^0-9\.]/', '',$Month11Value);
		$Month12=($item->Month12->Month);
		if (isset($item->Month12->Value))  $Month12Value=($item->Month12->Value);
		else $Month12Value=0;
if ($Month12Value=='-')  $Month12Value=0;
if ($Month12Value=='Відсутність трансакції')  $Month12Value=0;

$Month12Value=str_replace(',','.',$Month12Value);

	$Month12Value=preg_replace('/[^0-9\.]/', '',$Month12Value);
		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month3',
$Month3Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
,'-12'
)";		
print "<br />ds";			
print $sql;
print "<br />";	
	DatabaseHandler::Execute($sql); 			


    // move the pointer to the next product
 
}
foreach($cal->HCOferdraft as $item)
{	

 print "<br />";
 print_r($cal->HCOferdraft) ;
 print "<br />";
	$description=($item->Description);

	$Month1=($item->Month1->Month);
	if (isset($item->Month1->Value)) 	$Month1Value=($item->Month1->Value);
	else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	if ($Month1Value=='Відсутність трансакції')  $Month1Value=0;

	$Month1Value=str_replace(',','.',$Month1Value);
	$Month1Value=preg_replace('/[^0-9\.]/', '',$Month1Value);
		$Month2=($item->Month2->Month);
		if (isset($item->Month2->Value)) $Month2Value=($item->Month2->Value);
		else $Month2Value=0;
if ($Month2Value=='-')  $Month2Value=0;
	if ($Month2Value=='Відсутність трансакції')  $Month2Value=0;
$Month2Value=str_replace(',','.',$Month2Value);
$Month2Value=preg_replace('/[^0-9\.]/', '',$Month2Value);
		$Month3=($item->Month3->Month);
			if (isset($item->Month3->Value)) 	$Month3Value=($item->Month3->Value);
else $Month3Value=0;
if ($Month3Value=='-')  $Month3Value=0;
	if ($Month3Value=='Відсутність трансакції')  $Month3Value=0;
$Month3Value=str_replace(',','.',$Month3Value);
$Month3Value=preg_replace('/[^0-9\.]/', '',$Month3Value);
		$Month4=($item->Month4->Month);
		if (isset($item->Month4->Value)) 	$Month4Value=($item->Month4->Value);
		else $Month4Value=0;
if ($Month4Value=='-')  $Month4Value=0;
	if ($Month4Value=='Відсутність трансакції')  $Month4Value=0;

$Month4Value=str_replace(',','.',$Month4Value);
$Month4Value=preg_replace('/[^0-9\.]/', '',$Month4Value);
		$Month5=($item->Month5->Month);
		if (isset($item->Month5->Value)) 	$Month5Value=($item->Month5->Value);
		else $Month5Value=0;
if ($Month5Value=='-')  $Month5Value=0;
	if ($Month5Value=='Відсутність трансакції')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);
$Month5Value=preg_replace('/[^0-9\.]/', '',$Month5Value);
		$Month6=($item->Month6->Month);
		if (isset($item->Month6->Value)) 	$Month6Value=($item->Month6->Value);
		else $Month6Value=0;
if ($Month6Value=='-')  $Month6Value=0;
	if ($Month6Value=='Відсутність трансакції')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);
	$Month6Value=preg_replace('/[^0-9\.]/', '',$Month6Value);
		$Month7=($item->Month7->Month);
		if (isset($item->Month7->Value))  	$Month7Value=($item->Month7->Value);
		else $Month7Value=0;
if ($Month7Value=='-')  $Month7Value=0;
	if ($Month7Value=='Відсутність трансакції')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);
	$Month7Value=preg_replace('/[^0-9\.]/', '',$Month7Value);
		$Month8=($item->Month8->Month);
			if (isset($item->Month8->Value)) 	$Month8Value=($item->Month8->Value);
			else $Month8Value=0;
if ($Month8Value=='-')  $Month8Value=0;
	if ($Month8Value=='Відсутність трансакції')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);

	$Month8Value=preg_replace('/[^0-9\.]/', '',$Month8Value);
		$Month9=($item->Month9->Month);
		if (isset($item->Month9->Value)) 	$Month9Value=($item->Month9->Value);
		else $Month9Value=0;
if ($Month9Value=='-')  $Month9Value=0;
	if ($Month9Value=='Відсутність трансакції')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);
	$Month9Value=preg_replace('/[^0-9\.]/', '',$Month9Value);
		$Month10=($item->Month10->Month);
		if (isset($item->Month10->Value))  $Month10Value=($item->Month10->Value);
		else $Month10Value=0;
if ($Month10Value=='-')  $Month10Value=0;
if ($Month10Value=='Відсутність трансакції')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);
	$Month10Value=preg_replace('/[^0-9\.]/', '',$Month10Value);

		$Month11=($item->Month11->Month);
		if (isset($item->Month11->Value)) 	$Month11Value=($item->Month11->Value);
		else $Month11Value=0;
if ($Month11Value=='-')  $Month11Value=0;
if ($Month11Value=='Відсутність трансакції')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);
	$Month11Value=preg_replace('/[^0-9\.]/', '',$Month11Value);
		$Month12=($item->Month12->Month);
		if (isset($item->Month12->Value))  $Month12Value=($item->Month12->Value);
		else $Month12Value=0;
if ($Month12Value=='-')  $Month12Value=0;
if ($Month12Value=='Відсутність трансакції')  $Month12Value=0;
$Month12Value=str_replace(',','.',$Month12Value);
	$Month12Value=preg_replace('/[^0-9\.]/', '',$Month12Value);

		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month3',
$Month3Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
,'-12'
)";		
print "<br />";			
print $sql;
print "<br />";	
	DatabaseHandler::Execute($sql); 			


    // move the pointer to the next product
 
}
foreach($cal->HCCreditUsedInMonth as $item)
{	

 print "<br />";
 print_r($cal->HCCreditUsedInMonth) ;
 print "<br />";
	$description=($item->Description);

	$Month1=($item->Month1->Month);
	if (isset($item->Month1->Value)) 	$Month1Value=($item->Month1->Value);
	else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	if ($Month1Value=='Відсутність трансакції')  $Month1Value=0;
	$Month1Value=str_replace(',','.',$Month1Value);
		$Month1Value=preg_replace('/[^0-9\.]/', '',$Month1Value);
		$Month2=($item->Month2->Month);
		if (isset($item->Month2->Value)) $Month2Value=($item->Month2->Value);
		else $Month2Value=0;
if ($Month2Value=='-')  $Month2Value=0;
	if ($Month2Value=='Відсутність трансакції')  $Month2Value=0;
$Month2Value=str_replace(',','.',$Month2Value);
$Month2Value=preg_replace('/[^0-9\.]/', '',$Month2Value);
		$Month3=($item->Month3->Month);
			if (isset($item->Month3->Value)) 	$Month3Value=($item->Month3->Value);
else $Month3Value=0;
if ($Month3Value=='-')  $Month3Value=0;
	if ($Month3Value=='Відсутність трансакції')  $Month3Value=0;
$Month3Value=str_replace(',','.',$Month3Value);
$Month3Value=preg_replace('/[^0-9\.]/', '',$Month3Value);
		$Month4=($item->Month4->Month);
		if (isset($item->Month4->Value)) 	$Month4Value=($item->Month4->Value);
		else $Month4Value=0;
if ($Month4Value=='-')  $Month4Value=0;
	if ($Month4Value=='Відсутність трансакції')  $Month4Value=0;
$Month4Value=str_replace(',','.',$Month4Value);
$Month4Value=preg_replace('/[^0-9\.]/', '',$Month4Value);
		$Month5=($item->Month5->Month);
		if (isset($item->Month5->Value)) 	$Month5Value=($item->Month5->Value);
		else $Month5Value=0;
if ($Month5Value=='-')  $Month5Value=0;
	if ($Month5Value=='Відсутність трансакції')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);
$Month5Value=preg_replace('/[^0-9\.]/', '',$Month5Value);
		$Month6=($item->Month6->Month);
		if (isset($item->Month6->Value)) 	$Month6Value=($item->Month6->Value);
		else $Month6Value=0;
if ($Month6Value=='-')  $Month6Value=0;
	if ($Month6Value=='Відсутність трансакції')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);
$Month6Value=preg_replace('/[^0-9\.]/', '',$Month6Value);

		$Month7=($item->Month7->Month);
		if (isset($item->Month7->Value))  	$Month7Value=($item->Month7->Value);
		else $Month7Value=0;
if ($Month7Value=='-')  $Month7Value=0;
	if ($Month7Value=='Відсутність трансакції')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);
$Month7Value=preg_replace('/[^0-9\.]/', '',$Month7Value);

		$Month8=($item->Month8->Month);
			if (isset($item->Month8->Value)) 	$Month8Value=($item->Month8->Value);
			else $Month8Value=0;
if ($Month8Value=='-')  $Month8Value=0;
	if ($Month8Value=='Відсутність трансакції')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);
$Month8Value=preg_replace('/[^0-9\.]/', '',$Month8Value);


		$Month9=($item->Month9->Month);
		if (isset($item->Month9->Value)) 	$Month9Value=($item->Month9->Value);
		else $Month9Value=0;
if ($Month9Value=='-')  $Month9Value=0;
	if ($Month9Value=='Відсутність трансакції')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);
$Month9Value=preg_replace('/[^0-9\.]/', '',$Month9Value);

		$Month10=($item->Month10->Month);
		if (isset($item->Month10->Value))  $Month10Value=($item->Month10->Value);
		else $Month10Value=0;
if ($Month10Value=='-')  $Month10Value=0;
	if ($Month10Value=='Відсутність трансакції')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);
$Month10Value=preg_replace('/[^0-9\.]/', '',$Month10Value);


		$Month11=($item->Month11->Month);
		if (isset($item->Month11->Value)) 	$Month11Value=($item->Month11->Value);
		else $Month11Value=0;
if ($Month11Value=='-')  $Month11Value=0;
	if ($Month11Value=='Відсутність трансакції')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);
$Month11Value=preg_replace('/[^0-9\.]/', '',$Month11Value);
		$Month12=($item->Month12->Month);
		if (isset($item->Month12->Value))  $Month12Value=($item->Month12->Value);
		else $Month12Value=0;
if ($Month12Value=='-')  $Month12Value=0;
	if ($Month12Value=='Відсутність трансакції')  $Month12Value=0;
$Month12Value=str_replace(',','.',$Month12Value);
$Month12Value=preg_replace('/[^0-9\.]/', '',$Month12Value);

		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month3',
$Month3Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
,'-12'
)";		
print "<br />";			
print $sql;
print "<br />";	
	DatabaseHandler::Execute($sql); 			


    // move the pointer to the next product
 
}


}
/*
if ($cal['months']=='-24') {


foreach($cal->Months as $item)
{	

 

	$description=($item->Description);
	$Month1=($item->Month1->Month);
	$Month1Value=($item->Month1->Value);
	
		$Month2=($item->Month2->Month);
$Month2Value=($item->Month2->Value);

		$Month5=($item->Month5->Month);
	$Month5Value=($item->Month5->Value);

		$Month4=($item->Month4->Month);
	$Month4Value=($item->Month4->Value);

		$Month5=($item->Month5->Month);
	$Month5Value=($item->Month5->Value);

		$Month6=($item->Month6->Month);
	$Month6Value=($item->Month6->Value);

		$Month7=($item->Month7->Month);
	$Month7Value=($item->Month7->Value);

		$Month8=($item->Month8->Month);
	$Month8Value=($item->Month8->Value);

		$Month9=($item->Month9->Month);
	$Month9Value=($item->Month9->Value);

		$Month10=($item->Month10->Month);
	$Month10Value=($item->Month10->Value);

		$Month11=($item->Month11->Month);
	$Month11Value=($item->Month11->Value);

		$Month12=($item->Month12->Month);
	$Month12Value=($item->Month12->Value);
									
		
 $sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
0,
'$Month2',
0,
'$Month5',
0,
'$Month4',
0,
'$Month5',
0,
'$Month6',
0,
'$Month7',
0,
'$Month8',
0,
'$Month9',
0,
'$Month10',
0,
'$Month11',
0,
'$Month12',
0,
$subjectid
)";					
// print $sql;
	DatabaseHandler::Execute($sql); 	
    // move the pointer to the next product
 
}
foreach($cal->HCTotalNumberOfOverdueInstalments as $item)
{	

 

	$description=($item->Description);
	if (isset($item->Month1->Month)){
	$Month1=($item->Month1->Month);	
	}
else $Month1='';
if (isset($item->Month1->Value)){
$Month1Value=($item->Month1->Value);
} else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	$Month1Value=str_replace(',','.',$Month1Value);
if (isset($item->Month2->Month)){
	$Month2=($item->Month2->Month);	
	}
else $Month2='';
if (isset($item->Month2->Value)){
$Month2Value=($item->Month2->Value);
} else $Month2Value=0;
	if ($Month2Value=='-')  $Month2Value=0;
	$Month2Value=str_replace(',','.',$Month2Value);
if (isset($item->Month5->Month)){
	$Month5=($item->Month5->Month);	
	}
else $Month5='';
if (isset($item->Month5->Value)){
$Month5Value=($item->Month5->Value);
} else $Month5Value=0;
	if ($Month5Value=='-')  $Month5Value=0;
	$Month5Value=str_replace(',','.',$Month5Value);
if (isset($item->Month4>Month)){
	$Month4=($item->Month4->Month);	
	}
else $Month4='';
if (isset($item->Month4->Value)){
$Month4Value=($item->Month4->Value);
} else $Month4Value=0;
	if ($Month4Value=='-')  $Month4Value=0;
	$Month4Value=str_replace(',','.',$Month4Value);
	if (isset($item->Month5->Month)){
	$Month5=($item->Month5->Month);	
	}
else $Month5='';
if (isset($item->Month5->Value)){
$Month5Value=($item->Month5->Value);
} else $Month5Value=0;
	if ($Month5Value=='-')  $Month5Value=0;
	$Month5Value=str_replace(',','.',$Month5Value);
	if (isset($item->Month6->Month)){
	$Month6=($item->Month6->Month);	
	}
else $Month6='';
if (isset($item->Month6->Value)){
$Month6Value=($item->Month6->Value);
} else $Month6Value=0;
	if ($Month6Value=='-')  $Month6Value=0;
	$Month6Value=str_replace(',','.',$Month6Value);
	if (isset($item->Month7->Month)){
	$Month7=($item->Month7->Month);	
	}
else $Month7='';
if (isset($item->Month7->Value)){
$Month7Value=($item->Month7->Value);
} else $Month7Value=0;
	if ($Month7Value=='-')  $Month7Value=0;
	$Month7Value=str_replace(',','.',$Month7Value);
	if (isset($item->Month8->Month)){
	$Month8=($item->Month8->Month);	
	}
else $Month8='';
if (isset($item->Month8->Value)){
$Month8Value=($item->Month8->Value);
} else $Month8Value=0;
	if ($Month8Value=='-')  $Month8Value=0;
	$Month8Value=str_replace(',','.',$Month8Value);
	if (isset($item->Month9->Month)){
	$Month9=($item->Month9->Month);	
	}
else $Month9='';
if (isset($item->Month9->Value)){
$Month9Value=($item->Month9->Value);
} else $Month9Value=0;
	if ($Month9Value=='-')  $Month9Value=0;
	$Month9Value=str_replace(',','.',$Month9Value);
	if (isset($item->Month10->Month)){
	$Month10=($item->Month10->Month);	
	}
else $Month10='';
if (isset($item->Month10->Value)){
$Month10Value=($item->Month10->Value);
} else $Month10Value=0;
	if ($Month10Value=='-')  $Month10Value=0;
	$Month10Value=str_replace(',','.',$Month10Value);
	if (isset($item->Month11->Month)){
	$Month11=($item->Month11->Month);	
	}
else $Month11='';
if (isset($item->Month11->Value)){
$Month11Value=($item->Month11->Value);
} else $Month11Value=0;
	if ($Month11Value=='-')  $Month11Value=0;
	$Month11Value=str_replace(',','.',$Month11Value);
	if (isset($item->Month12->Month)){
	$Month12=($item->Month12->Month);	
	}
else $Month12='';
if (isset($item->Month12->Value)){
$Month12Value=($item->Month12->Value);
} else $Month12Value=0;
	if ($Month12Value=='-')  $Month12Value=0;
	$Month12Value=str_replace(',','.',$Month12Value);
		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month5',
$Month5Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
)";					
// print $sql;
	DatabaseHandler::Execute($sql); 			


    // move the pointer to the next product
 
}

foreach($cal->HCTotalOverdueAmount as $item)
{	

 

	$description=($item->Description);
	if (isset($item->Month1->Month)){
	$Month1=($item->Month1->Month);	
	}
else $Month1='';
if (isset($item->Month1->Value)){
$Month1Value=($item->Month1->Value);
} else $Month1Value=0;
	if ($Month1Value=='-')  $Month1Value=0;
	$Month1Value=str_replace(',','.',$Month1Value);
if (isset($item->Month2->Month)){
	$Month2=($item->Month2->Month);	
	}
else $Month2='';
if (isset($item->Month2->Value)){
$Month2Value=($item->Month2->Value);
} else $Month2Value=0;
	if ($Month2Value=='-')  $Month2Value=0;
	$Month2Value=str_replace(',','.',$Month2Value);
if (isset($item->Month5->Month)){
	$Month5=($item->Month5->Month);	
	}
else $Month5='';
if (isset($item->Month5->Value)){
$Month5Value=($item->Month5->Value);
} else $Month5Value=0;

	$Month1Value=str_replace(' ','',$Month1Value);
	
		
	$Month2Value=str_replace(' ','',$Month2Value);
	
	$Month5Value=str_replace(' ','',$Month5Value);
		$Month4=($item->Month4->Month);
	$Month4Value=($item->Month4->Value);
if ($Month4Value=='-')  $Month4Value=0;
$Month4Value=str_replace(',','.',$Month4Value);
	$Month4Value=str_replace(' ','',$Month14Value);
		$Month5=($item->Month5->Month);
	$Month5Value=($item->Month5->Value);
if ($Month5Value=='-')  $Month5Value=0;
$Month5Value=str_replace(',','.',$Month5Value);
	$Month5Value=str_replace(' ','',$Month5Value);
		$Month6=($item->Month6->Month);
	$Month6Value=($item->Month6->Value);
if ($Month6Value=='-')  $Month6Value=0;
$Month6Value=str_replace(',','.',$Month6Value);
	$Month6Value=str_replace(' ','',$Month6Value);
		$Month7=($item->Month7->Month);
	$Month7Value=($item->Month7->Value);
if ($Month7Value=='-')  $Month7Value=0;
$Month7Value=str_replace(',','.',$Month7Value);
	$Month7Value=str_replace(' ','',$Month7Value);
		$Month8=($item->Month8->Month);
	$Month8Value=($item->Month8->Value);
if ($Month8Value=='-')  $Month8Value=0;
$Month8Value=str_replace(',','.',$Month8Value);
	$Month8Value=str_replace(' ','',$Month8Value);	
	$Month9=($item->Month9->Month);
	$Month9Value=($item->Month9->Value);
if ($Month9Value=='-')  $Month9Value=0;
$Month9Value=str_replace(',','.',$Month9Value);
			$Month9Value=str_replace(' ','',$Month9Value);
		$Month10=($item->Month10->Month);
	$Month10Value=($item->Month10->Value);
if ($Month10Value=='-')  $Month10Value=0;
$Month10Value=str_replace(',','.',$Month10Value);
	$Month10Value=str_replace(' ','',$Month10Value);	
	$Month11=($item->Month11->Month);
	$Month11Value=($item->Month11->Value);
if ($Month11Value=='-')  $Month11Value=0;
$Month11Value=str_replace(',','.',$Month11Value);
			$Month11Value=str_replace(' ','',$Month11Value);
		$Month12=($item->Month12->Month);
	$Month12Value=($item->Month12->Value);
if ($Month12Value=='-')  $Month12Value=0;
$Month12Value=str_replace(',','.',$Month12Value);
			$Month12Value=str_replace(' ','',$Month12Value);
			$Month1Value=preg_replace('/[^0-9]/', '', $Month1Value);
	$Month2Value=preg_replace('/[^0-9]/', '', $Month2Value);

	$Month5Value=preg_replace('/[^0-9]/', '', $Month5Value);
		$Month4Value=preg_replace('/[^0-9]/', '', $Month4Value);
			$Month5Value=preg_replace('/[^0-9]/', '', $Month5Value);
				$Month6Value=preg_replace('/[^0-9]/', '', $Month6Value);
					$Month7Value=preg_replace('/[^0-9]/', '', $Month7Value);
						$Month8Value=preg_replace('/[^0-9]/', '', $Month8Value);
							$Month9Value=preg_replace('/[^0-9]/', '', $Month9Value);
								$Month10Value=preg_replace('/[^0-9]/', '', $Month10Value);
									$Month11Value=preg_replace('/[^0-9]/', '', $Month11Value);
										$Month12Value=preg_replace('/[^0-9]/', '', $Month12Value);
		$sql="insert into historicalcalendarsMBKI values(null,$CreditinfoId,$lastinsertId,
'$description',
'$Month1',
$Month1Value,
'$Month2',
$Month2Value,
'$Month5',
$Month5Value,
'$Month4',
$Month4Value,
'$Month5',
$Month5Value,
'$Month6',
$Month6Value,
'$Month7',
$Month7Value,
'$Month8',
$Month8Value,
'$Month9',
$Month9Value,
'$Month10',
$Month10Value,
'$Month11',
$Month11Value,
'$Month12',
$Month12Value,
$subjectid
)";					
// print $sql;
	DatabaseHandler::Execute($sql); 	
									
			


    // move the pointer to the next product
 
}
}*/
}
}

foreach ($xmm[0]->SearchInquiries->InquiryList as $node) {
 foreach($node->SearchInquiry as $cal){  
 $date=$cal->Date;
 $Subscriber=$cal->Subscriber;
 $SubscriberType=$cal->SubscriberType;
 $sql="insert into SearchInqueryMBKI values(null,'$date','$Subscriber','$SubscriberType',$CreditinfoId,$subjectid)";
 print $sql;
DatabaseHandler::Execute($sql);
 }
}	
foreach ($xmm[0]->Inquiers as $node) {
 foreach($node->Inquiery as $cal){  
 $year=$cal->Year;
 $quarter=$cal->Quarter;
 $value=$cal->Value;

 $sql="insert into inqueryMBKI values(null,$CreditinfoId,$year,$quarter,$value,$subjectid)";
   print $sql;
DatabaseHandler::Execute($sql);
 }
}
$updateReport=$xml->xpath('//Report/@updated');
 print_r($updateReport[0]);
$updateReport=$updateReport[0]['updated'];
 $sql="insert into InfoReportMBKI values(null,'$updateReport',$subjectid)";
 print $sql;
DatabaseHandler::Execute($sql);


$xmm=$xml->xpath('//SummaryInformation/NegativeInfoType/parent::*');

$NumberOfUsersReportingNegativeStatus=$xmm[0]->NumberOfUsersReportingNegativeStatus;
$sql="insert into negativeinfoMBKI values($CreditinfoId,$NumberOfUsersReportingNegativeStatus,$subjectid)";
DatabaseHandler::Execute($sql);
$xmm=$xml->xpath('//Inquiers');

$NumberOfInquiers=$xmm[0]->NumberOfInquiers;

$sql="insert into inqueriesMBKI values(null,$CreditinfoId,$NumberOfInquiers,$subjectid)";
DatabaseHandler::Execute($sql);
$xmm=$xml->xpath('//SummaryInformation/SummaryType[position()=1]/parent::*');

$SummaryType=$xmm[0]->SummaryType;
if (isset($xmm[0]->NumberOfExistingContracts)) $NumberOfExistingContracts=$xmm[0]->NumberOfExistingContracts;
else $NumberOfExistingContracts=0;
if (isset($xmm[0]->TotalOutstandingDebt->Amount->Value)) $TotalOutstandingDebtAmountValue=$xmm[0]->TotalOutstandingDebt->Amount->Value;
else $TotalOutstandingDebtAmountValue=0;
$TotalOutstandingDebtAmountCurrency=$xmm[0]->TotalOutstandingDebt->Amount->Currency;
if (isset($xmm[0]->NumberOfTerminatedContracts)) $NumberOfTerminatedContracts=$xmm[0]->NumberOfTerminatedContracts;
else $NumberOfTerminatedContracts=0;
if (isset($xmm[0]->TotalDebtOverdue->Amount->Value)) $TotalDebtOverdueAmountValue=$xmm[0]->TotalDebtOverdue->Amount->Value;
else $TotalDebtOverdueAmountValue=0;
$TotalDebtOverdueAmountCurrency=$xmm[0]->TotalDebtOverdue->Amount->Currency;
if (isset($xmm[0]->NumberOfUnsolvedApplications)) $NumberOfUnsolvedApplications=$xmm[0]->NumberOfUnsolvedApplications;
else $NumberOfUnsolvedApplications=0;
if (isset($xmm[0]->NumberOfUnpaidInstalments)) $NumberOfUnpaidInstalments=$xmm[0]->NumberOfUnpaidInstalments;
else $NumberOfUnpaidInstalments=0;
if (isset($xmm[0]->NumberOfRejectedApplications)) $NumberOfRejectedApplications=$xmm[0]->NumberOfRejectedApplications;
else $NumberOfRejectedApplications=0;
if (isset($xmm[0]->NumberOfRevokedApplications)) $NumberOfRevokedApplications=$xmm[0]->NumberOfRevokedApplications;
else $NumberOfRevokedApplications=0;
if (isset($xmm[0]->NumberOfUsersReportingNegativeStatus)) 
$NumberOfUsersReportingNegativeStatus=$xmm[0]->NumberOfUsersReportingNegativeStatus;
else $NumberOfUsersReportingNegativeStatus=0;
 $ContractType=$xmm[0]->ContractType;


$sql="insert into summaryinformationdebtorMBKI values($CreditinfoId,$NumberOfExistingContracts,$TotalOutstandingDebtAmountValue,'$TotalOutstandingDebtAmountCurrency',
$NumberOfTerminatedContracts,
$TotalDebtOverdueAmountValue,
'$TotalDebtOverdueAmountCurrency',
$NumberOfUnsolvedApplications,
$NumberOfUnpaidInstalments,
$NumberOfRejectedApplications,
$NumberOfRevokedApplications,
$NumberOfUsersReportingNegativeStatus,
 '$ContractType',$subjectid)";
print $sql;
DatabaseHandler::Execute($sql);

$xmm=$xml->xpath('//SummaryInformation/ContractType[text()= "Contract.Type.Financial.Credit_by_installments"]/parent::*');

$SummaryType=$xmm[0]->SummaryType;
if (isset($xmm[0]->NumberOfExistingContracts)) $NumberOfExistingContracts=$xmm[0]->NumberOfExistingContracts;
else $NumberOfExistingContracts=0;
if (isset($xmm[0]->TotalOutstandingDebt->Amount->Value)) $TotalOutstandingDebtAmountValue=$xmm[0]->TotalOutstandingDebt->Amount->Value;
else $TotalOutstandingDebtAmountValue=0;
$TotalOutstandingDebtAmountCurrency=$xmm[0]->TotalOutstandingDebt->Amount->Currency;
if (isset($xmm[0]->NumberOfTerminatedContracts)) $NumberOfTerminatedContracts=$xmm[0]->NumberOfTerminatedContracts;
else $NumberOfTerminatedContracts=0;
if (isset($xmm[0]->TotalDebtOverdue->Amount->Value)) $TotalDebtOverdueAmountValue=$xmm[0]->TotalDebtOverdue->Amount->Value;
else $TotalDebtOverdueAmountValue=0;
$TotalDebtOverdueAmountCurrency=$xmm[0]->TotalDebtOverdue->Amount->Currency;
if (isset($xmm[0]->NumberOfUnsolvedApplications)) $NumberOfUnsolvedApplications=$xmm[0]->NumberOfUnsolvedApplications;
else $NumberOfUnsolvedApplications=0;
if (isset($xmm[0]->NumberOfUnpaidInstalments)) $NumberOfUnpaidInstalments=$xmm[0]->NumberOfUnpaidInstalments;
else $NumberOfUnpaidInstalments=0;
if (isset($xmm[0]->NumberOfRejectedApplications)) $NumberOfRejectedApplications=$xmm[0]->NumberOfRejectedApplications;
else $NumberOfRejectedApplications=0;
if (isset($xmm[0]->NumberOfRevokedApplications)) $NumberOfRevokedApplications=$xmm[0]->NumberOfRevokedApplications;
else $NumberOfRevokedApplications=0;
if (isset($xmm[0]->NumberOfUsersReportingNegativeStatus)) 
$NumberOfUsersReportingNegativeStatus=$xmm[0]->NumberOfUsersReportingNegativeStatus;
else $NumberOfUsersReportingNegativeStatus=0; 
$ContractType=$xmm[0]->ContractType;
$sql="insert into summaryinformationdebtorMBKI values($CreditinfoId,$NumberOfExistingContracts,$TotalOutstandingDebtAmountValue,'$TotalOutstandingDebtAmountCurrency',
$NumberOfTerminatedContracts,
$TotalDebtOverdueAmountValue,
'$TotalDebtOverdueAmountCurrency',
$NumberOfUnsolvedApplications,
$NumberOfUnpaidInstalments,
$NumberOfRejectedApplications,
$NumberOfRevokedApplications,
$NumberOfUsersReportingNegativeStatus,
 '$ContractType',$subjectid)";
DatabaseHandler::Execute($sql);
$xmm=$xml->xpath('//SummaryInformation/ContractType[text()= "Contract.Type.Financial.Credit_by_non-installment_over_draught_etc."]/parent::*');
$SummaryType=$xmm[0]->SummaryType;
if (isset($xmm[0]->NumberOfExistingContracts)) $NumberOfExistingContracts=$xmm[0]->NumberOfExistingContracts;
else $NumberOfExistingContracts=0;
if (isset($xmm[0]->TotalOutstandingDebt->Amount->Value)) $TotalOutstandingDebtAmountValue=$xmm[0]->TotalOutstandingDebt->Amount->Value;
else $TotalOutstandingDebtAmountValue=0;
$TotalOutstandingDebtAmountCurrency=$xmm[0]->TotalOutstandingDebt->Amount->Currency;
if (isset($xmm[0]->NumberOfTerminatedContracts)) $NumberOfTerminatedContracts=$xmm[0]->NumberOfTerminatedContracts;
else $NumberOfTerminatedContracts=0;
if (isset($xmm[0]->TotalDebtOverdue->Amount->Value)) $TotalDebtOverdueAmountValue=$xmm[0]->TotalDebtOverdue->Amount->Value;
else $TotalDebtOverdueAmountValue=0;
$TotalDebtOverdueAmountCurrency=$xmm[0]->TotalDebtOverdue->Amount->Currency;
if (isset($xmm[0]->NumberOfUnsolvedApplications)) $NumberOfUnsolvedApplications=$xmm[0]->NumberOfUnsolvedApplications;
else $NumberOfUnsolvedApplications=0;
if (isset($xmm[0]->NumberOfUnpaidInstalments)) $NumberOfUnpaidInstalments=$xmm[0]->NumberOfUnpaidInstalments;
else $NumberOfUnpaidInstalments=0;
if (isset($xmm[0]->NumberOfRejectedApplications)) $NumberOfRejectedApplications=$xmm[0]->NumberOfRejectedApplications;
else $NumberOfRejectedApplications=0;
if (isset($xmm[0]->NumberOfRevokedApplications)) $NumberOfRevokedApplications=$xmm[0]->NumberOfRevokedApplications;
else $NumberOfRevokedApplications=0;
if (isset($xmm[0]->NumberOfUsersReportingNegativeStatus)) 
$NumberOfUsersReportingNegativeStatus=$xmm[0]->NumberOfUsersReportingNegativeStatus;
else $NumberOfUsersReportingNegativeStatus=0;
 $ContractType=$xmm[0]->ContractType;
$sql="insert into summaryinformationdebtorMBKI values($CreditinfoId,$NumberOfExistingContracts,$TotalOutstandingDebtAmountValue,'$TotalOutstandingDebtAmountCurrency',
$NumberOfTerminatedContracts,
$TotalDebtOverdueAmountValue,
'$TotalDebtOverdueAmountCurrency',
$NumberOfUnsolvedApplications,
$NumberOfUnpaidInstalments,
$NumberOfRejectedApplications,
$NumberOfRevokedApplications,
$NumberOfUsersReportingNegativeStatus,
 '$ContractType',$subjectid)";

DatabaseHandler::Execute($sql);
$xmm=$xml->xpath('//SummaryInformation/ContractType[text()= "Contract.Type.Financial.Credit_Card_or_renewable_credit"]/parent::*');
$SummaryType=$xmm[0]->SummaryType;
if (isset($xmm[0]->NumberOfExistingContracts)) $NumberOfExistingContracts=$xmm[0]->NumberOfExistingContracts;
else $NumberOfExistingContracts=0;
if (isset($xmm[0]->TotalOutstandingDebt->Amount->Value)) $TotalOutstandingDebtAmountValue=$xmm[0]->TotalOutstandingDebt->Amount->Value;
else $TotalOutstandingDebtAmountValue=0;
$TotalOutstandingDebtAmountCurrency=$xmm[0]->TotalOutstandingDebt->Amount->Currency;
if (isset($xmm[0]->NumberOfTerminatedContracts)) $NumberOfTerminatedContracts=$xmm[0]->NumberOfTerminatedContracts;
else $NumberOfTerminatedContracts=0;
if (isset($xmm[0]->TotalDebtOverdue->Amount->Value)) $TotalDebtOverdueAmountValue=$xmm[0]->TotalDebtOverdue->Amount->Value;
else $TotalDebtOverdueAmountValue=0;
$TotalDebtOverdueAmountCurrency=$xmm[0]->TotalDebtOverdue->Amount->Currency;
if (isset($xmm[0]->NumberOfUnsolvedApplications)) $NumberOfUnsolvedApplications=$xmm[0]->NumberOfUnsolvedApplications;
else $NumberOfUnsolvedApplications=0;
if (isset($xmm[0]->NumberOfUnpaidInstalments)) $NumberOfUnpaidInstalments=$xmm[0]->NumberOfUnpaidInstalments;
else $NumberOfUnpaidInstalments=0;
if (isset($xmm[0]->NumberOfRejectedApplications)) $NumberOfRejectedApplications=$xmm[0]->NumberOfRejectedApplications;
else $NumberOfRejectedApplications=0;
if (isset($xmm[0]->NumberOfRevokedApplications)) $NumberOfRevokedApplications=$xmm[0]->NumberOfRevokedApplications;
else $NumberOfRevokedApplications=0;
if (isset($xmm[0]->NumberOfUsersReportingNegativeStatus)) 
$NumberOfUsersReportingNegativeStatus=$xmm[0]->NumberOfUsersReportingNegativeStatus;
else $NumberOfUsersReportingNegativeStatus=0;
 $ContractType=$xmm[0]->ContractType;
$sql="insert into summaryinformationdebtorMBKI values($CreditinfoId,$NumberOfExistingContracts,$TotalOutstandingDebtAmountValue,'$TotalOutstandingDebtAmountCurrency',
$NumberOfTerminatedContracts,
$TotalDebtOverdueAmountValue,
'$TotalDebtOverdueAmountCurrency',
$NumberOfUnsolvedApplications,
$NumberOfUnpaidInstalments,
$NumberOfRejectedApplications,
$NumberOfRevokedApplications,
$NumberOfUsersReportingNegativeStatus,
 '$ContractType',$subjectid)";
 
DatabaseHandler::Execute($sql);
$xmm=$xml->xpath('//SummaryInformation/ContractType[text()= "Contract.Type.Financial.Financial_or_operating_leasing"]/parent::*');
$SummaryType=$xmm[0]->SummaryType;
if (isset($xmm[0]->NumberOfExistingContracts)) $NumberOfExistingContracts=$xmm[0]->NumberOfExistingContracts;
else $NumberOfExistingContracts=0;
if (isset($xmm[0]->TotalOutstandingDebt->Amount->Value)) $TotalOutstandingDebtAmountValue=$xmm[0]->TotalOutstandingDebt->Amount->Value;
else $TotalOutstandingDebtAmountValue=0;
$TotalOutstandingDebtAmountCurrency=$xmm[0]->TotalOutstandingDebt->Amount->Currency;
if (isset($xmm[0]->NumberOfTerminatedContracts)) $NumberOfTerminatedContracts=$xmm[0]->NumberOfTerminatedContracts;
else $NumberOfTerminatedContracts=0;
if (isset($xmm[0]->TotalDebtOverdue->Amount->Value)) $TotalDebtOverdueAmountValue=$xmm[0]->TotalDebtOverdue->Amount->Value;
else $TotalDebtOverdueAmountValue=0;
$TotalDebtOverdueAmountCurrency=$xmm[0]->TotalDebtOverdue->Amount->Currency;
if (isset($xmm[0]->NumberOfUnsolvedApplications)) $NumberOfUnsolvedApplications=$xmm[0]->NumberOfUnsolvedApplications;
else $NumberOfUnsolvedApplications=0;
if (isset($xmm[0]->NumberOfUnpaidInstalments)) $NumberOfUnpaidInstalments=$xmm[0]->NumberOfUnpaidInstalments;
else $NumberOfUnpaidInstalments=0;
if (isset($xmm[0]->NumberOfRejectedApplications)) $NumberOfRejectedApplications=$xmm[0]->NumberOfRejectedApplications;
else $NumberOfRejectedApplications=0;
if (isset($xmm[0]->NumberOfRevokedApplications)) $NumberOfRevokedApplications=$xmm[0]->NumberOfRevokedApplications;
else $NumberOfRevokedApplications=0;
if (isset($xmm[0]->NumberOfUsersReportingNegativeStatus)) 
$NumberOfUsersReportingNegativeStatus=$xmm[0]->NumberOfUsersReportingNegativeStatus;
else $NumberOfUsersReportingNegativeStatus=0;
 $ContractType=$xmm[0]->ContractType;
$sql="insert into summaryinformationdebtorMBKI values($CreditinfoId,$NumberOfExistingContracts,$TotalOutstandingDebtAmountValue,'$TotalOutstandingDebtAmountCurrency',
$NumberOfTerminatedContracts,
$TotalDebtOverdueAmountValue,
'$TotalDebtOverdueAmountCurrency',
$NumberOfUnsolvedApplications,
$NumberOfUnpaidInstalments,
$NumberOfRejectedApplications,
$NumberOfRevokedApplications,
$NumberOfUsersReportingNegativeStatus,
 '$ContractType',$subjectid)";
DatabaseHandler::Execute($sql);
$xmm=$xml->xpath('//SummaryInformation/ContractType[text()= "Contract.Type.Financial.Financial_obligation_on_the_basis_of_contract"]/parent::*');
$SummaryType=$xmm[0]->SummaryType;
if (isset($xmm[0]->NumberOfExistingContracts)) $NumberOfExistingContracts=$xmm[0]->NumberOfExistingContracts;
else $NumberOfExistingContracts=0;
if (isset($xmm[0]->TotalOutstandingDebt->Amount->Value)) $TotalOutstandingDebtAmountValue=$xmm[0]->TotalOutstandingDebt->Amount->Value;
else $TotalOutstandingDebtAmountValue=0;
$TotalOutstandingDebtAmountCurrency=$xmm[0]->TotalOutstandingDebt->Amount->Currency;
if (isset($xmm[0]->NumberOfTerminatedContracts)) $NumberOfTerminatedContracts=$xmm[0]->NumberOfTerminatedContracts;
else $NumberOfTerminatedContracts=0;
if (isset($xmm[0]->TotalDebtOverdue->Amount->Value)) $TotalDebtOverdueAmountValue=$xmm[0]->TotalDebtOverdue->Amount->Value;
else $TotalDebtOverdueAmountValue=0;
$TotalDebtOverdueAmountCurrency=$xmm[0]->TotalDebtOverdue->Amount->Currency;
if (isset($xmm[0]->NumberOfUnsolvedApplications)) $NumberOfUnsolvedApplications=$xmm[0]->NumberOfUnsolvedApplications;
else $NumberOfUnsolvedApplications=0;
if (isset($xmm[0]->NumberOfUnpaidInstalments)) $NumberOfUnpaidInstalments=$xmm[0]->NumberOfUnpaidInstalments;
else $NumberOfUnpaidInstalments=0;
if (isset($xmm[0]->NumberOfRejectedApplications)) $NumberOfRejectedApplications=$xmm[0]->NumberOfRejectedApplications;
else $NumberOfRejectedApplications=0;
if (isset($xmm[0]->NumberOfRevokedApplications)) $NumberOfRevokedApplications=$xmm[0]->NumberOfRevokedApplications;
else $NumberOfRevokedApplications=0;
if (isset($xmm[0]->NumberOfUsersReportingNegativeStatus)) 
$NumberOfUsersReportingNegativeStatus=$xmm[0]->NumberOfUsersReportingNegativeStatus;
else $NumberOfUsersReportingNegativeStatus=0;
 $ContractType=$xmm[0]->ContractType;
$sql="insert into summaryinformationdebtorMBKI values($CreditinfoId,$NumberOfExistingContracts,$TotalOutstandingDebtAmountValue,'$TotalOutstandingDebtAmountCurrency',
$NumberOfTerminatedContracts,
$TotalDebtOverdueAmountValue,
'$TotalDebtOverdueAmountCurrency',
$NumberOfUnsolvedApplications,
$NumberOfUnpaidInstalments,
$NumberOfRejectedApplications,
$NumberOfRevokedApplications,
$NumberOfUsersReportingNegativeStatus,
 '$ContractType',$subjectid)";

DatabaseHandler::Execute($sql);
$xmm=$xml->xpath('//SummaryInformation/ContractType[text()= "Contract.Type.Financial.Financial_obligation_on_the_basis_of_account"]/parent::*');
$SummaryType=$xmm[0]->SummaryType;
if (isset($xmm[0]->NumberOfExistingContracts)) $NumberOfExistingContracts=$xmm[0]->NumberOfExistingContracts;
else $NumberOfExistingContracts=0;
if (isset($xmm[0]->TotalOutstandingDebt->Amount->Value)) $TotalOutstandingDebtAmountValue=$xmm[0]->TotalOutstandingDebt->Amount->Value;
else $TotalOutstandingDebtAmountValue=0;
$TotalOutstandingDebtAmountCurrency=$xmm[0]->TotalOutstandingDebt->Amount->Currency;
if (isset($xmm[0]->NumberOfTerminatedContracts)) $NumberOfTerminatedContracts=$xmm[0]->NumberOfTerminatedContracts;
else $NumberOfTerminatedContracts=0;
if (isset($xmm[0]->TotalDebtOverdue->Amount->Value)) $TotalDebtOverdueAmountValue=$xmm[0]->TotalDebtOverdue->Amount->Value;
else $TotalDebtOverdueAmountValue=0;
$TotalDebtOverdueAmountCurrency=$xmm[0]->TotalDebtOverdue->Amount->Currency;
if (isset($xmm[0]->NumberOfUnsolvedApplications)) $NumberOfUnsolvedApplications=$xmm[0]->NumberOfUnsolvedApplications;
else $NumberOfUnsolvedApplications=0;
if (isset($xmm[0]->NumberOfUnpaidInstalments)) $NumberOfUnpaidInstalments=$xmm[0]->NumberOfUnpaidInstalments;
else $NumberOfUnpaidInstalments=0;
if (isset($xmm[0]->NumberOfRejectedApplications)) $NumberOfRejectedApplications=$xmm[0]->NumberOfRejectedApplications;
else $NumberOfRejectedApplications=0;
if (isset($xmm[0]->NumberOfRevokedApplications)) $NumberOfRevokedApplications=$xmm[0]->NumberOfRevokedApplications;
else $NumberOfRevokedApplications=0;
if (isset($xmm[0]->NumberOfUsersReportingNegativeStatus)) 
$NumberOfUsersReportingNegativeStatus=$xmm[0]->NumberOfUsersReportingNegativeStatus;
else $NumberOfUsersReportingNegativeStatus=0;
 $ContractType=$xmm[0]->ContractType;
$sql="insert into summaryinformationdebtorMBKI values($CreditinfoId,$NumberOfExistingContracts,$TotalOutstandingDebtAmountValue,'$TotalOutstandingDebtAmountCurrency',
$NumberOfTerminatedContracts,
$TotalDebtOverdueAmountValue,
'$TotalDebtOverdueAmountCurrency',
$NumberOfUnsolvedApplications,
$NumberOfUnpaidInstalments,
$NumberOfRejectedApplications,
$NumberOfRevokedApplications,
$NumberOfUsersReportingNegativeStatus,
 '$ContractType',$subjectid)";
 
DatabaseHandler::Execute($sql);
} else {$idmbki=0;}
} else {$idmbki=0;}
}
if ($idmbki<>0){
		$sql="select id from subjectMBKI where idmbki=$idmbki and TaxpayerNumber='$inn'";
	$subjectid=DatabaseHandler::GetOne($sql);
if ($subjectid=='') {$subjectid=0;}
	// 1 перевірка
	$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=1 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="SELECT ifnull(NumberOfUsersReportingNegativeStatus,0) FROM negativeinfoMBKI WHERE subjectid=$subjectid";

$NumberOfExistingContracts=DatabaseHandler::GetOne($sql);
if (is_null($NumberOfExistingContracts) or ($NumberOfExistingContracts==''))  $NumberOfExistingContracts=0;
if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
	$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  WhiteZone {$resArray['Name']} =$NumberOfExistingContracts',$applicationid,'$Zone',1)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  GreyZone {$resArray['Name']} =$NumberOfExistingContracts',$applicationid,'$Zone',1)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  redZone{$resArray['Name']} =$NumberOfExistingContracts',$applicationid,'$Zone',1)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']} =$NumberOfExistingContracts',$applicationid,'$Zone',1)";

}

DatabaseHandler::Execute($sql);}
	// 2 перевірка
	$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=2 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (strlen($mobile_phone)>10) $mobile_phone1=substr($mobile_phone,-10);
	else $mobile_phone1='';
if (strlen($home_phone)>10) $home_phone1=substr($home_phone,-10);
	else  $home_phone1='';
if (strlen($home_phone_residence)>10) $home_phone_residence1=substr($home_phone_residence,-10);
	else $home_phone_residence1='';
if (strlen($work_phone)>10) $work_phone1=substr($work_phone,-10);
	else $work_phone1='';
	
	$mobile_phone2=str_replace('+','',$mobile_phone);
		$home_phone2=str_replace('+','',$home_phone);
			$home_phone_residence2=str_replace('+','',$home_phone_residence);
				$work_phone2=str_replace('+','',$work_phone);
$sql="select count(*) from contactMBKI where subjectid=$subjectid ";
$existPhone=DatabaseHandler::GetOne($sql);
if (is_null($existPhone) or ($existPhone=='')) $existPhone=0;
if (($existPhone<>0) and (($mobile_phone1<>'') or ($home_phone1<>'') or ($home_phone_residence1<>'') or ($work_phone1<>'') or ($mobile_phone<>'') or ($work_phone<>'')or ($home_phone<>'') )){ 
$sql="select count(*) from contactMBKI where subjectid=$subjectid and RIGHT(value,10) in ('$mobile_phone','$home_phone','$home_phone_residence','$work_phone','$mobile_phone1','$home_phone1','$home_phone_residence1','$work_phone1','$mobile_phone2','$home_phone2','$home_phone_residence2','$work_phone2') and ImportCode<5";
print $sql;
$countPhone=DatabaseHandler::GetOne($sql);
if (is_null($countPhone)){$countPhone=0;}
if (($countPhone>=$resArray['whiteZoneFrom']) and ($countPhone<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']} ',$applicationid,'$Zone',2)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($countPhone>=$resArray['greyZoneFrom']) and ($countPhone<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']} ',$applicationid,'$Zone',2)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($countPhone>=$resArray['redZoneFrom']) and ($countPhone<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']} ',$applicationid,'$Zone',2)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ Значення не входить в матрицю рішень {$resArray['Name']} =$NumberOfExistingContracts',$applicationid,'$Zone',2)";

}
}

// print $sql;
DatabaseHandler::Execute($sql);
// 3 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=3 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(*) from contactMBKI where subjectid=$subjectid and ImportCode=5";
$existMail=DatabaseHandler::GetOne($sql);
if (is_null($existMail) or ($existMail=='')) $existMail=0;
print $existMail;
if (($existMail>0) and (($email<>''))){ 
$sql="select count(*) from contactMBKI where subjectid=$subjectid and value in ('$email')";
$countEmail=DatabaseHandler::GetOne($sql);
if (is_null($countEmail)) {$countEmail=0;}
if (($countEmail>=$resArray['whiteZoneFrom']) and ($countEmail<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}  є співпадіння',$applicationid,'$Zone',3)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($countEmail>=$resArray['greyZoneFrom']) and ($countEmail<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}  ',$applicationid,'$Zone',3)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($countEmail>=$resArray['redZoneFrom']) and ($countEmail<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']} ',$applicationid,'$Zone',3)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ Email Значення не входить в матрицю рішень {$resArray['Name']} =$NumberOfExistingContracts',$applicationid,'$Zone',3)";

}
}
// print $sql;
DatabaseHandler::Execute($sql);
// 4 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=4 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(*) from contractsMBKI where subjectid=$subjectid and ContractType='Existing' and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01')";
print $sql;
$NumberOfExistingContracts=DatabaseHandler::GetOne($sql);
if (is_null($NumberOfExistingContracts) or ($NumberOfExistingContracts==''))  $NumberOfExistingContracts=0;
if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$NumberOfExistingContracts ',$applicationid,'$Zone',4)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',4)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',4)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',4)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 5 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=5 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select sum(OverdueAmountValue) from contractsMBKI where subjectid=$subjectid and ContractType='Existing' and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01')";
$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
if (is_null($TotalOutstandingDebtValue) or ($TotalOutstandingDebtValue==''))  $TotalOutstandingDebtValue=0;
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
if ($statusCheck==0) {$statusCheck=1;}
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',5)";
}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',5)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',5)";

$statusCheck=3;

} else {
	$Zone='';

	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',5)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 6 перевірка
$Zone='';

$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=6 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select  count(distinct a.id) cnt from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where a.subjectid=$subjectid 
and (`ContractPhase`<>'Закінчено'  and `ContractPhase`<>'Припинено достроково') and (`ExportCode`='Contract.Type.Financial.Credit_by_installments' or PurposeOfCredit='Кредит на карту' or a.CreditorType='Фінансова компанія - онлайн кредитування')";
$NumberOfExistingContracts=DatabaseHandler::GetOne($sql);
if (is_null($NumberOfExistingContracts) or ($NumberOfExistingContracts==''))  $NumberOfExistingContracts=0;
if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$NumberOfExistingContracts ',$applicationid,'$Zone',6)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',6)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',6)";

$statusCheck=3;

} else {
	$Zone='';

	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',6)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 7 перевірка
$Zone='';

$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=7 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(a.id) cnt from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and (`ContractPhase`='Закінчено' or `ContractPhase`='Припинено достроково')  and (`ExportCode`='Contract.Type.Financial.Credit_by_installments' or PurposeOfCredit='Кредит на карту'  or a.CreditorType='Фінансова компанія - онлайн кредитування') and description='/' and type='-12' and cast(concat('20',SUBSTRING_INDEX(month12Name, '/', -1), case when length(SUBSTRING_INDEX(month12Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month12Name, '/', 1) ) else SUBSTRING_INDEX(month12Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $paramclosecreditperiod Day AND SYSDATE()";
$NumberOfTerminatedContracts=DatabaseHandler::GetOne($sql);
if (is_null($NumberOfTerminatedContracts) or ($NumberOfTerminatedContracts==''))  $NumberOfTerminatedContracts=0;
if (($NumberOfTerminatedContracts>=$resArray['whiteZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$NumberOfTerminatedContracts ',$applicationid,'$Zone',7)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfTerminatedContracts>=$resArray['greyZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',7)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfTerminatedContracts>=$resArray['redZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',7)";

$statusCheck=3;

} else {
	$Zone='';

	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',7)";

}
// print $sql;
DatabaseHandler::Execute($sql);

// 8 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=8 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
/*$sql="select sum(cnt)/100 from (select (b.month1value) as cnt from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month1value>20000 and b.description='Несплачена прострочена сума платежів' 
and LOCATE('/', month1Name)>0 and cast(concat('20',SUBSTRING_INDEX(month1Name, '/', -1), case when length(SUBSTRING_INDEX(month1Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month1Name, '/', 1) ) else SUBSTRING_INDEX(month1Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
 select (b.month2value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month2value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month2Name)>0 and cast(concat('20',SUBSTRING_INDEX(month2Name, '/', -1), case when length(SUBSTRING_INDEX(month2Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month2Name, '/', 1) ) else SUBSTRING_INDEX(month2Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month3value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month3value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month3Name)>0 and cast(concat('20',SUBSTRING_INDEX(month3Name, '/', -1), case when length(SUBSTRING_INDEX(month3Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month3Name, '/', 1) ) else SUBSTRING_INDEX(month3Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month4value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month4value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month4Name)>0 and cast(concat('20',SUBSTRING_INDEX(month4Name, '/', -1), case when length(SUBSTRING_INDEX(month4Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month4Name, '/', 1) ) else SUBSTRING_INDEX(month4Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month5value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month5value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month5Name)>0 and cast(concat('20',SUBSTRING_INDEX(month5Name, '/', -1), case when length(SUBSTRING_INDEX(month5Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month5Name, '/', 1) ) else SUBSTRING_INDEX(month5Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month6value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month6value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month6Name)>0 and cast(concat('20',SUBSTRING_INDEX(month6Name, '/', -1), case when length(SUBSTRING_INDEX(month6Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month6Name, '/', 1) ) else SUBSTRING_INDEX(month6Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month7value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month7value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month7Name)>0 and cast(concat('20',SUBSTRING_INDEX(month7Name, '/', -1), case when length(SUBSTRING_INDEX(month7Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month7Name, '/', 1) ) else SUBSTRING_INDEX(month7Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month8value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month8value>20000 and b.description='Несплачена прострочена сума платежів'
and LOCATE('/', month8Name)>0 and cast(concat('20',SUBSTRING_INDEX(month8Name, '/', -1), case when length(SUBSTRING_INDEX(month8Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month8Name, '/', 1) ) else SUBSTRING_INDEX(month8Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() 
 union all
 select (b.month9value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month9value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month9Name)>0 and cast(concat('20',SUBSTRING_INDEX(month9Name, '/', -1), case when length(SUBSTRING_INDEX(month9Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month9Name, '/', 1) ) else SUBSTRING_INDEX(month9Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month10value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month10value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month10Name)>0 and cast(concat('20',SUBSTRING_INDEX(month10Name, '/', -1), case when length(SUBSTRING_INDEX(month10Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month10Name, '/', 1) ) else SUBSTRING_INDEX(month10Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month11value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month11value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month11Name)>0 and cast(concat('20',SUBSTRING_INDEX(month11Name, '/', -1), case when length(SUBSTRING_INDEX(month11Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month11Name, '/', 1) ) else SUBSTRING_INDEX(month11Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month12value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and totalamountamounttype<>'CreditLimit' and b.month12value>20000 and b.description='Несплачена прострочена сума платежів'
 and LOCATE('/', month12Name)>0 and cast(concat('20',SUBSTRING_INDEX(month12Name, '/', -1), case when length(SUBSTRING_INDEX(month12Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month12Name, '/', 1) ) else SUBSTRING_INDEX(month12Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()) b";*/
 $sql="select sum(OverdueAmountValue) sm from contractsMBKI where subjectid=$subjectid and ((ExportCode='Contract.Type.Financial.Credit_by_installments') or  (PurposeOfCredit='Кредит на карту'))";
print $sql;
$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
if ((is_null($TotalOutstandingDebtValue) or ($TotalOutstandingDebtValue==''))and ($TotalOutstandingDebtValue<=200))  $TotalOutstandingDebtValue=0;
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',8)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',8)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',8)";

$statusCheck=3;

} else {
$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',8)";

}
// print $sql;
DatabaseHandler::Execute($sql);

// 9 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=9 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(cnt) from (select (b.month1value) as cnt from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month1value>20000 and b.description='Несплачена прострочена сума платежів' 
and LOCATE('/', month1Name)>0 and cast(concat('20',SUBSTRING_INDEX(month1Name, '/', -1), case when length(SUBSTRING_INDEX(month1Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month1Name, '/', 1) ) else SUBSTRING_INDEX(month1Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
 select (b.month2value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month2value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month2Name)>0 and cast(concat('20',SUBSTRING_INDEX(month2Name, '/', -1), case when length(SUBSTRING_INDEX(month2Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month2Name, '/', 1) ) else SUBSTRING_INDEX(month2Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month3value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month3value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month3Name)>0 and cast(concat('20',SUBSTRING_INDEX(month3Name, '/', -1), case when length(SUBSTRING_INDEX(month3Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month3Name, '/', 1) ) else SUBSTRING_INDEX(month3Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month4value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month4value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month4Name)>0 and cast(concat('20',SUBSTRING_INDEX(month4Name, '/', -1), case when length(SUBSTRING_INDEX(month4Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month4Name, '/', 1) ) else SUBSTRING_INDEX(month4Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month5value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month5value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month5Name)>0 and cast(concat('20',SUBSTRING_INDEX(month5Name, '/', -1), case when length(SUBSTRING_INDEX(month5Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month5Name, '/', 1) ) else SUBSTRING_INDEX(month5Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month6value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month6value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month6Name)>0 and cast(concat('20',SUBSTRING_INDEX(month6Name, '/', -1), case when length(SUBSTRING_INDEX(month6Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month6Name, '/', 1) ) else SUBSTRING_INDEX(month6Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month7value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month7value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month7Name)>0 and cast(concat('20',SUBSTRING_INDEX(month7Name, '/', -1), case when length(SUBSTRING_INDEX(month7Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month7Name, '/', 1) ) else SUBSTRING_INDEX(month7Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month8value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month8value>20000 and b.description='Несплачена прострочена сума платежів'
and LOCATE('/', month8Name)>0 and cast(concat('20',SUBSTRING_INDEX(month8Name, '/', -1), case when length(SUBSTRING_INDEX(month8Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month8Name, '/', 1) ) else SUBSTRING_INDEX(month8Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() 
 union all
 select (b.month9value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month9value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month9Name)>0 and cast(concat('20',SUBSTRING_INDEX(month9Name, '/', -1), case when length(SUBSTRING_INDEX(month9Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month9Name, '/', 1) ) else SUBSTRING_INDEX(month9Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month10value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month10value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month10Name)>0 and cast(concat('20',SUBSTRING_INDEX(month10Name, '/', -1), case when length(SUBSTRING_INDEX(month10Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month10Name, '/', 1) ) else SUBSTRING_INDEX(month10Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month11value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month11value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month11Name)>0 and cast(concat('20',SUBSTRING_INDEX(month11Name, '/', -1), case when length(SUBSTRING_INDEX(month11Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month11Name, '/', 1) ) else SUBSTRING_INDEX(month11Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
 union all
 select (b.month12value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid  and ((totalamountamounttype<>'CreditLimit') or  (PurposeOfCredit='Кредит на карту')) and b.month12value>20000 and b.description='Несплачена прострочена сума платежів'
 and LOCATE('/', month12Name)>0 and cast(concat('20',SUBSTRING_INDEX(month12Name, '/', -1), case when length(SUBSTRING_INDEX(month12Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month12Name, '/', 1) ) else SUBSTRING_INDEX(month12Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()) b";
print "\n";
print $sql;
$NumberOfUnpaidInstalments=DatabaseHandler::GetOne($sql);
if (is_null($NumberOfUnpaidInstalments) or ($NumberOfUnpaidInstalments==''))  $NumberOfUnpaidInstalments=0;
if (($NumberOfUnpaidInstalments>=$resArray['whiteZoneFrom']) and ($NumberOfUnpaidInstalments<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$NumberOfUnpaidInstalments ',$applicationid,'$Zone',9)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfUnpaidInstalments>=$resArray['greyZoneFrom']) and ($NumberOfUnpaidInstalments<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$NumberOfUnpaidInstalments',$applicationid,'$Zone',9)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfUnpaidInstalments>=$resArray['redZoneFrom']) and ($NumberOfUnpaidInstalments<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$NumberOfUnpaidInstalments',$applicationid,'$Zone',9)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfUnpaidInstalments',$applicationid,'$Zone',9)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 10 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=10 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(*) from contractsMBKI where subjectid=$subjectid and ContractType='Existing' and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and ExportCode='Contract.Type.Financial.Credit_Card_or_renewable_credit' and PurposeOfCredit<>'Кредит на карту' and CreditorType<>'Фінансова компанія - онлайн кредитування'";
$NumberOfExistingContracts=DatabaseHandler::GetOne($sql);
if (is_null($NumberOfExistingContracts) or ($NumberOfExistingContracts==''))  $NumberOfExistingContracts=0;
if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$NumberOfExistingContracts ',$applicationid,'$Zone',10)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',10)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone З{$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',10)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',10)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 11 перевірка
	$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=11 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select sum(OverdueAmountValue) from contractsMBKI where subjectid=$subjectid and ContractType='Existing' and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and ExportCode='Contract.Type.Financial.Credit_Card_or_renewable_credit'  and PurposeOfCredit<>'Кредит на карту'";
$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
if (is_null($TotalOutstandingDebtValue) or ($TotalOutstandingDebtValue=='')) $TotalOutstandingDebtValue=0;
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
	$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',11)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',11)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',11)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',11)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 12 перевірка
// todo credithistory
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=12 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select sum(cnt) from (select count(b.month1value) as cnt from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit' and b.month1value>20000 and b.description='Несплачена прострочена сума платежів' 
and LOCATE('/', month1Name)>0 and cast(concat('20',SUBSTRING_INDEX(month1Name, '/', -1), case when length(SUBSTRING_INDEX(month1Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month1Name, '/', 1) ) else SUBSTRING_INDEX(month1Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту'
union all
select count(b.month2value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit' and b.month2value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month2Name)>0 and cast(concat('20',SUBSTRING_INDEX(month2Name, '/', -1), case when length(SUBSTRING_INDEX(month2Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month2Name, '/', 1) ) else SUBSTRING_INDEX(month2Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту'
union all
select count(b.month3value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit' and b.month3value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month3Name)>0 and cast(concat('20',SUBSTRING_INDEX(month3Name, '/', -1), case when length(SUBSTRING_INDEX(month3Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month3Name, '/', 1) ) else SUBSTRING_INDEX(month3Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту'
union all
select count(b.month4value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit' and b.month4value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month4Name)>0 and cast(concat('20',SUBSTRING_INDEX(month4Name, '/', -1), case when length(SUBSTRING_INDEX(month4Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month4Name, '/', 1) ) else SUBSTRING_INDEX(month4Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту'
union all
select count(b.month5value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit' and b.month5value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month5Name)>0 and cast(concat('20',SUBSTRING_INDEX(month5Name, '/', -1), case when length(SUBSTRING_INDEX(month5Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month5Name, '/', 1) ) else SUBSTRING_INDEX(month5Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту'
union all
select count(b.month6value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit' and b.month6value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month6Name)>0 and cast(concat('20',SUBSTRING_INDEX(month6Name, '/', -1), case when length(SUBSTRING_INDEX(month6Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month6Name, '/', 1) ) else SUBSTRING_INDEX(month6Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту'
union all
select count(b.month7value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit' and b.month7value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month7Name)>0 and cast(concat('20',SUBSTRING_INDEX(month7Name, '/', -1), case when length(SUBSTRING_INDEX(month7Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month7Name, '/', 1) ) else SUBSTRING_INDEX(month7Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту'
union all
select count(b.month8value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit'  and b.month8value>20000 and b.description='Несплачена прострочена сума платежів'
and LOCATE('/', month8Name)>0 and cast(concat('20',SUBSTRING_INDEX(month8Name, '/', -1), case when length(SUBSTRING_INDEX(month8Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month8Name, '/', 1) ) else SUBSTRING_INDEX(month8Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту'
union all
select count(b.month9value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit' and b.month9value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month9Name)>0 and cast(concat('20',SUBSTRING_INDEX(month9Name, '/', -1), case when length(SUBSTRING_INDEX(month9Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month9Name, '/', 1) ) else SUBSTRING_INDEX(month9Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту'
union all
select count(b.month10value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit' and b.month10value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month10Name)>0 and cast(concat('20',SUBSTRING_INDEX(month10Name, '/', -1), case when length(SUBSTRING_INDEX(month10Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month10Name, '/', 1) ) else SUBSTRING_INDEX(month10Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту'
union all
select count(b.month11value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit' and b.month11value>20000 and b.description='Несплачена прострочена сума платежів' 
 and LOCATE('/', month11Name)>0 and cast(concat('20',SUBSTRING_INDEX(month11Name, '/', -1), case when length(SUBSTRING_INDEX(month11Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month11Name, '/', 1) ) else SUBSTRING_INDEX(month11Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту'
union all
select count(b.month12value) from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where b.subjectid=$subjectid and totalamountamounttype='CreditLimit' and b.month12value>20000 and b.description='Несплачена прострочена сума платежів'
 and LOCATE('/', month12Name)>0 and cast(concat('20',SUBSTRING_INDEX(month12Name, '/', -1), case when length(SUBSTRING_INDEX(month12Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month12Name, '/', 1) ) else SUBSTRING_INDEX(month12Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE() and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and PurposeOfCredit<>'Кредит на карту') b";

$NumberOfTerminatedContracts=DatabaseHandler::GetOne($sql);
if (is_null($NumberOfTerminatedContracts) or ($NumberOfTerminatedContracts=='')) $NumberOfTerminatedContracts=0;
if (($NumberOfTerminatedContracts>=$resArray['whiteZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$NumberOfTerminatedContracts ',$applicationid,'$Zone',12)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfTerminatedContracts>=$resArray['greyZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',12)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfTerminatedContracts>=$resArray['redZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',12)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',12)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 14 перевірка
$Zone='';

$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=13 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(*) from contractsMBKI where subjectid=$subjectid and ContractType<>'Existing' and not(CodeOfContract  like 'EXT%' and CreditStartDate<'2015-03-01') and (ExportCode='Contract.Type.Financial.Credit_Card_or_renewable_credit' and PurposeOfCredit<>'Кредит на карту' and CreditorType<>'Фінансова компанія - онлайн кредитування')
";
$NumberOfTerminatedContracts=DatabaseHandler::GetOne($sql);
if (is_null($NumberOfTerminatedContracts) or ($NumberOfTerminatedContracts==''))  $NumberOfTerminatedContracts=0;
if (($NumberOfTerminatedContracts>=$resArray['whiteZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$NumberOfTerminatedContracts ',$applicationid,'$Zone',13)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfTerminatedContracts>=$resArray['greyZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',13)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfTerminatedContracts>=$resArray['redZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',13)";

$statusCheck=3;

} else {
	$Zone='';

	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',13)";

}
// print $sql;
DatabaseHandler::Execute($sql);

// 15 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=14 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="SELECT SUM(val) from (SELECT count(Month1Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month1Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month1Name)>0 and cast(concat('20',SUBSTRING_INDEX(month1Name, '/', -1), case when length(SUBSTRING_INDEX(month1Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month1Name, '/', 1) ) else SUBSTRING_INDEX(month1Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month2Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month2Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month2Name)>0 and cast(concat('20',SUBSTRING_INDEX(month2Name, '/', -1), case when length(SUBSTRING_INDEX(month2Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month2Name, '/', 1) ) else SUBSTRING_INDEX(month2Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month3Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month3Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month3Name)>0 and cast(concat('20',SUBSTRING_INDEX(month3Name, '/', -1), case when length(SUBSTRING_INDEX(month3Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month3Name, '/', 1) ) else SUBSTRING_INDEX(month3Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month4Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month4Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month4Name)>0 and cast(concat('20',SUBSTRING_INDEX(month4Name, '/', -1), case when length(SUBSTRING_INDEX(month4Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month4Name, '/', 1) ) else SUBSTRING_INDEX(month4Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month5Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month5Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month5Name)>0 and cast(concat('20',SUBSTRING_INDEX(month5Name, '/', -1), case when length(SUBSTRING_INDEX(month5Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month5Name, '/', 1) ) else SUBSTRING_INDEX(month5Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month6Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month6Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month6Name)>0 and cast(concat('20',SUBSTRING_INDEX(month6Name, '/', -1), case when length(SUBSTRING_INDEX(month6Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month6Name, '/', 1) ) else SUBSTRING_INDEX(month6Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month7Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month7Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month7Name)>0 and cast(concat('20',SUBSTRING_INDEX(month7Name, '/', -1), case when length(SUBSTRING_INDEX(month7Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month7Name, '/', 1) ) else SUBSTRING_INDEX(month7Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month8Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month8Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month8Name)>0 and cast(concat('20',SUBSTRING_INDEX(month8Name, '/', -1), case when length(SUBSTRING_INDEX(month8Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month8Name, '/', 1) ) else SUBSTRING_INDEX(month8Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month9Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month9Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month9Name)>0 and cast(concat('20',SUBSTRING_INDEX(month9Name, '/', -1), case when length(SUBSTRING_INDEX(month9Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month9Name, '/', 1) ) else SUBSTRING_INDEX(month9Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month10Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month10Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month10Name)>0 and cast(concat('20',SUBSTRING_INDEX(month10Name, '/', -1), case when length(SUBSTRING_INDEX(month10Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month10Name, '/', 1) ) else SUBSTRING_INDEX(month10Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month11Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month11Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month11Name)>0 and cast(concat('20',SUBSTRING_INDEX(month11Name, '/', -1), case when length(SUBSTRING_INDEX(month11Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month11Name, '/', 1) ) else SUBSTRING_INDEX(month11Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month12Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month12Value=1 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month12Name)>0 and cast(concat('20',SUBSTRING_INDEX(month12Name, '/', -1), case when length(SUBSTRING_INDEX(month12Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month12Name, '/', 1) ) else SUBSTRING_INDEX(month12Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()) b
";

$CntValue=DatabaseHandler::GetOne($sql);
if (is_null($CntValue) or ($CntValue=='')) $CntValue=0;
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',14)";
if ($statusCheck==0) {$statusCheck=1;}

}else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',14)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',14)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$CntValue',$applicationid,'$Zone',14)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 16 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=15 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="SELECT SUM(val) from (SELECT count(Month1Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month1Value=2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month1Name)>0 and cast(concat('20',SUBSTRING_INDEX(month1Name, '/', -1), case when length(SUBSTRING_INDEX(month1Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month1Name, '/', 1) ) else SUBSTRING_INDEX(month1Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month2Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month2Value=2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month2Name)>0 and cast(concat('20',SUBSTRING_INDEX(month2Name, '/', -1), case when length(SUBSTRING_INDEX(month2Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month2Name, '/', 1) ) else SUBSTRING_INDEX(month2Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month3Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month3Value=2)   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month3Name)>0 and cast(concat('20',SUBSTRING_INDEX(month3Name, '/', -1), case when length(SUBSTRING_INDEX(month3Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month3Name, '/', 1) ) else SUBSTRING_INDEX(month3Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month4Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month4Value=2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month4Name)>0 and cast(concat('20',SUBSTRING_INDEX(month4Name, '/', -1), case when length(SUBSTRING_INDEX(month4Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month4Name, '/', 1) ) else SUBSTRING_INDEX(month4Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month5Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month5Value=2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month5Name)>0 and cast(concat('20',SUBSTRING_INDEX(month5Name, '/', -1), case when length(SUBSTRING_INDEX(month5Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month5Name, '/', 1) ) else SUBSTRING_INDEX(month5Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month6Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month6Value=2)   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month6Name)>0 and cast(concat('20',SUBSTRING_INDEX(month6Name, '/', -1), case when length(SUBSTRING_INDEX(month6Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month6Name, '/', 1) ) else SUBSTRING_INDEX(month6Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month7Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month7Value=2)   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month7Name)>0 and cast(concat('20',SUBSTRING_INDEX(month7Name, '/', -1), case when length(SUBSTRING_INDEX(month7Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month7Name, '/', 1) ) else SUBSTRING_INDEX(month7Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month8Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month8Value=2)   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month8Name)>0 and cast(concat('20',SUBSTRING_INDEX(month8Name, '/', -1), case when length(SUBSTRING_INDEX(month8Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month8Name, '/', 1) ) else SUBSTRING_INDEX(month8Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month9Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month9Value=2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month9Name)>0 and cast(concat('20',SUBSTRING_INDEX(month9Name, '/', -1), case when length(SUBSTRING_INDEX(month9Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month9Name, '/', 1) ) else SUBSTRING_INDEX(month9Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month10Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month10Value=2)   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month10Name)>0 and cast(concat('20',SUBSTRING_INDEX(month10Name, '/', -1), case when length(SUBSTRING_INDEX(month10Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month10Name, '/', 1) ) else SUBSTRING_INDEX(month10Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month11Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month11Value=2)   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month11Name)>0 and cast(concat('20',SUBSTRING_INDEX(month11Name, '/', -1), case when length(SUBSTRING_INDEX(month11Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month11Name, '/', 1) ) else SUBSTRING_INDEX(month11Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month12Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month12Value=2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month12Name)>0 and cast(concat('20',SUBSTRING_INDEX(month12Name, '/', -1), case when length(SUBSTRING_INDEX(month12Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month12Name, '/', 1) ) else SUBSTRING_INDEX(month12Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()) b
";
$CntValue=DatabaseHandler::GetOne($sql);
if (is_null($CntValue) or ($CntValue=='')) $CntValue=0;
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',15)";
if ($statusCheck==0) {$statusCheck=1;}

} else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',15)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',15)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$CntValue',$applicationid,'$Zone',15)";

}
// print $sql;
DatabaseHandler::Execute($sql);

// 17 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=16 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="SELECT SUM(val) from (SELECT count(Month1Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month1Value>2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month1Name)>0 and cast(concat('20',SUBSTRING_INDEX(month1Name, '/', -1), case when length(SUBSTRING_INDEX(month1Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month1Name, '/', 1) ) else SUBSTRING_INDEX(month1Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month2Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month2Value>2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month2Name)>0 and cast(concat('20',SUBSTRING_INDEX(month2Name, '/', -1), case when length(SUBSTRING_INDEX(month2Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month2Name, '/', 1) ) else SUBSTRING_INDEX(month2Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month3Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month3Value>2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month3Name)>0 and cast(concat('20',SUBSTRING_INDEX(month3Name, '/', -1), case when length(SUBSTRING_INDEX(month3Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month3Name, '/', 1) ) else SUBSTRING_INDEX(month3Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month4Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month4Value>2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month4Name)>0 and cast(concat('20',SUBSTRING_INDEX(month4Name, '/', -1), case when length(SUBSTRING_INDEX(month4Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month4Name, '/', 1) ) else SUBSTRING_INDEX(month4Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month5Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month5Value>2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month5Name)>0 and cast(concat('20',SUBSTRING_INDEX(month5Name, '/', -1), case when length(SUBSTRING_INDEX(month5Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month5Name, '/', 1) ) else SUBSTRING_INDEX(month5Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month6Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month6Value>2)   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month6Name)>0 and cast(concat('20',SUBSTRING_INDEX(month6Name, '/', -1), case when length(SUBSTRING_INDEX(month6Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month6Name, '/', 1) ) else SUBSTRING_INDEX(month6Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month7Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month7Value>2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month7Name)>0 and cast(concat('20',SUBSTRING_INDEX(month7Name, '/', -1), case when length(SUBSTRING_INDEX(month7Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month7Name, '/', 1) ) else SUBSTRING_INDEX(month7Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month8Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month8Value>2)   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month8Name)>0 and cast(concat('20',SUBSTRING_INDEX(month8Name, '/', -1), case when length(SUBSTRING_INDEX(month8Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month8Name, '/', 1) ) else SUBSTRING_INDEX(month8Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month9Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month9Value>2)   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month9Name)>0 and cast(concat('20',SUBSTRING_INDEX(month9Name, '/', -1), case when length(SUBSTRING_INDEX(month9Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month9Name, '/', 1) ) else SUBSTRING_INDEX(month9Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month10Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month10Value>2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month10Name)>0 and cast(concat('20',SUBSTRING_INDEX(month10Name, '/', -1), case when length(SUBSTRING_INDEX(month10Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month10Name, '/', 1) ) else SUBSTRING_INDEX(month10Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month11Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month11Value>2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month11Name)>0 and cast(concat('20',SUBSTRING_INDEX(month11Name, '/', -1), case when length(SUBSTRING_INDEX(month11Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month11Name, '/', 1) ) else SUBSTRING_INDEX(month11Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()
union all
SELECT count(Month12Value) as val FROM historicalcalendarsMBKI WHERE subjectid=$subjectid and (month12Value>2 )   and description =  'Сумарна кількість просторочених платежів' and LOCATE('/', month12Name)>0 and cast(concat('20',SUBSTRING_INDEX(month12Name, '/', -1), case when length(SUBSTRING_INDEX(month12Name, '/', 1))=1 then concat('0',SUBSTRING_INDEX(month12Name, '/', 1) ) else SUBSTRING_INDEX(month12Name, '/', 1) end,'01') as date) between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()) b
";
$CntValue=DatabaseHandler::GetOne($sql);
if (is_null($CntValue) or ($CntValue=='')) $CntValue=0;
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',16)";

if ($statusCheck==0) {$statusCheck=1;}
}else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',16)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',16)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$CntValue',$applicationid,'$Zone',16)";

}
// print $sql;
DatabaseHandler::Execute($sql);
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=17 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql=" SELECT sum(DueInterestAmountValue) FROM `contractsMBKI` WHERE `PereodicityOfPayments`='В останній день строку дії договору' and ((`CreditorType`='Фінансова компанія') or (`CreditorType`='Фінансова компанія - онлайн кредитування') or  (PurposeOfCredit='Кредит на карту')) and `TotalAmountValue`<=20000 and `SubjectId`=$subjectid";
$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
if (is_null($TotalOutstandingDebtValue) or ($TotalOutstandingDebtValue=='')) $TotalOutstandingDebtValue=0;
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
	$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',17)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',17)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',17)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',17)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 19 перевірка
	$Zone='';

$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=18 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select max(ifnull(month1value,0)+ifnull(month2value,0)+ifnull(month3value,0)+ifnull(month4value,0)+ifnull(month5value,0)+ifnull(month6value,0)+ifnull(month7value,0)+ifnull(month8value,0)+ifnull(month9value,0)+ifnull(month10value,0)+ifnull(month11value,0)+ifnull(month12value,0)) as cnt from contractsMBKI a join historicalcalendarsMBKI b on a.id=b.contractid where `PereodicityOfPayments`='В останній день строку дії договору' and ((`CreditorType`='Фінансова компанія') or (`CreditorType`='Фінансова компанія - онлайн кредитування') or  (PurposeOfCredit='Кредит на карту')) and `TotalAmountValue`<=20000 and b.subjectid=$subjectid and (b.description='Сумарна кількіс' or b.description='Сумарна кількість просторочених платежів')";
print "<br />";
 print $sql;
 print "<br />";
$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
if (is_null($TotalOutstandingDebtValue) or ($TotalOutstandingDebtValue=='')) $TotalOutstandingDebtValue=0;
$TotalOutstandingDebtValue=number_format($TotalOutstandingDebtValue, 0, ',', ' ');
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
	$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',18)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',18)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',18)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',18)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 20 перевірка
	$Zone='';

$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=39 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(distinct a.id) cnt from contractsMBKI a where ((`CreditorType`='Фінансова компанія') or (`CreditorType`='Фінансова компанія - онлайн кредитування') or  (PurposeOfCredit='Кредит на карту')) and `TotalAmountValue`<=20000 and a.subjectid=$subjectid and (`ContractPhase`='Закінчено' or `ContractPhase`='Припинено достроково') and ContractEndDate between  CURDATE() - INTERVAL 366 Day AND SYSDATE() ";
print "<br />";
 print $sql;
 print "<br />";
$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
if (is_null($TotalOutstandingDebtValue) or ($TotalOutstandingDebtValue=='')) $TotalOutstandingDebtValue=0;
$TotalOutstandingDebtValue=number_format($TotalOutstandingDebtValue, 0, ',', ' ');
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
	$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',20)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',20)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',20)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка МБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',20)";

}
// print $sql;
DatabaseHandler::Execute($sql);



} else {
	
$Zone='';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Пустий звіт МБКІ ',$applicationid,'$Zone',37)";
DatabaseHandler::Execute($sql);
$mbkiExists=1;
 $mbkiemptyf=1;
	 
}
}
if (($site_source=='http://pozichka.com/') or ($site_source=='http://private.pozichka.ua/') or ($site_source=='https://private.pozichka.ua/') or ($site_source=='https://pozichka.ua/')) {$site_source='http://pozichka.ua';}

 if (($bki['NameBki']==='МБКІ') and ($decisionAfterScorFirst==1) and (isset($subjectid)) and ($subjectid>0))
 {
	 print "<br />";
print "Скоринг1=";
	 print "<br />";
	 print $bki['NameBki'];
	
 $sql="select a.id scoringcardid,a.share,a.name scoringcardname from scoringcards a  join  parameterrunscorcard b on a.id=b.scoringcardid where a.date_end>CURDATE() and source='$site_source' and b.BKI=2 and b.status=1 and  `type`=$typeSc";
print "<br />";
 print $sql;
 print "<br />";
 $results=DatabaseHandler::GetAll($sql); 
print "<br />";
for($i = 0; $i < count($results); ++$i) {
 print_r($results);
 $scoreScor=0;
 $statusCheckScor=0;
  $decisionAfterScorSecond=0;
 $scoringcardid=$results[$i]['scoringcardid'];
    $scoringcardname=$results[$i]['scoringcardname'];
	$share=$results[$i]['share'];
	$share=100-$share;
	$share=substr($share, 0, 1);
	$a=intval(substr($creditid, -1));
	$b=intval($share);
	if ($b==0) {$b=1;}
	print "Скоринг1=";
	print "<br />";
print $a;
	print "<br />";
print $b;
	print "<br />";
	print ($a%$b);
print "<br />";
	print "Кінець скорингу";
		$share=['0','1','2','3','4','5','6','7','8','9'];
	if ($a % $b==0){
		
$sql="select b.id id,c.id idparameter,c.name parameter from  ascoringcard b  join detalniparametruscoringcard c on b.parameterscoringcard_id=c.id 

where  b.scoringcard_id=$scoringcardid order by c.id ";
print $sql;
 	$resultsd=DatabaseHandler::GetAll($sql);
	
for($j = 0; $j < count($resultsd); ++$j) {

	
	 $id=$resultsd[$j]['id'];
	 $idparameter=$resultsd[$j]['idparameter'];
	  $parameter=$resultsd[$j]['parameter'];
$sql="select d.conditions conditions,d.score score from   bscoringcard d 
where  ascoringcard_id=$id and conditions<>'NULL'";
 	$resultsdd=DatabaseHandler::GetAll($sql);
	print_r($resultsdd);
for($k = 0; $k < count($resultsdd); ++$k) {
	$conditions=$resultsdd[$k]['conditions'];
	 $score=$resultsdd[$k]['score'];
	print "<br />";
		 print "param=";
	 print $idparameter;
	 print "<br />";
	if ($idparameter==1)
	{
		
		$sql="select count(*) from CreditsClientsForCheckBKI where id=$creditid and ammout_of_credit {$conditions}";
print "<br />";
	 print $sql;
	 print "<br />";
		$nRowsIn=DatabaseHandler::GetOne($sql); 
		print "<br />";
	 print $nRowsIn;
	 print "<br />";
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}			
		
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
		print "<br />";
	 print $sql;
	 print "<br />";
	//	DatabaseHandler::Execute($sql);

	}
	print "<br />";
	 print $nRowsIn;
	 print "<br />";
if ($idparameter==2)
	{
if (is_null($CreditInfoId)) $CreditInfoId=0;
if (is_null($subjectid)) $subjectid=0;
if ( $mbkiemptyf==1) $subjectid=0;
$sql="SELECT count(distinct CodeOfContract) FROM `contractsMBKI` WHERE `PereodicityOfPayments`='В останній день строку дії договору' and `CreditorType`='Фінансова компанія' and `TotalAmountValue`<=20000 and `SubjectId`=$subjectid";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	if (is_null($nRowsIn) or ($nRowsIn==''))  $nRowsIn=0; 
$os=array('0','1');
if (in_array(substr($conditions, 0, 1), $os)) {
	if ($nRowsIn==$conditions){

		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\"  параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
print $sql;	
	DatabaseHandler::Execute($sql);
	$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);


	}
} else {

	if ($nRowsIn>1){

		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\"  параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
print $sql;	
	DatabaseHandler::Execute($sql);
	$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);


	}
}
print "<br />";
print $sql;
print "<br />";
print $nRowsIn;
print "<br />";
	
	
				//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);	
}
if ($idparameter==3)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and work_experience {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
print $sql;
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}	
		//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==4)
	{
		if ($conditions=='="2_Так"') {
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and (property{$conditions} or movable_property{$conditions} )";
		} else {
	$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and (property{$conditions} and movable_property{$conditions} )";
			
		}
		print "<br />";
print $sql;
print $conditions;
print "<br />";
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print $sql;
	DatabaseHandler::Execute($sql);
	$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
}	
			
		//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
		//DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==5)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and current_work_first{$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);

}	
					//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
		//DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==6)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and (cancelled_loans<>'1_Відсутні' and cancelled_loans<>'0_Виберіть відповідь з переліку')";
		print "<br />";
print $sql;
print "<br />";
	$nRowsIn=DatabaseHandler::GetOne($sql);
	print "<br />";
	print $nRowsIn;
	print "<br />";
	if (is_null($nRowsIn) or ($nRowsIn==''))  $nRowsIn=0; 
	// убкі
	if ( $mbkiemptyf==1) $subjectid=0;
$sql="select count(*) from (
select CodeOfContract  FROM `contractsMBKI` where subjectid=$subjectid and ContractType='Terminated') a
 ";
print $sql;
$сloseContracts=DatabaseHandler::GetOne($sql);
if(is_null($closeContracts)) $closeContracts=0;
print $сloseContracts;
if ($conditions=='1 and 1'){
if (($nRowsIn>0) and ($сloseContracts>0 )){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='0 and 1'){
if (($nRowsIn==0) and ($сloseContracts>0 )){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1 and 0'){
if (($nRowsIn>0) and ($сloseContracts==0 )){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='0 and 0'){
if (($nRowsIn==0) and ($сloseContracts==0 )){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}


print "<br />";
print $conditions;
print $closeContracts;
print "<br />";
 



			
		print "<br />";
		 print "sd";
	 print $sql;
	 print "<br />";
		//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
		//DatabaseHandler::Execute($sql);
	}
	
print "<br />";
print "1";
print "<br />";
	if ($idparameter==7)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and education {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);

}	
			//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==8)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and number_of_childrens {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
		}	
			//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==9)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and martial_status {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==10)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and employment_type {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}	
		//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==11)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and source_of_information {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);

}	
				//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==12)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and movable_property {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
}	
		
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==13)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and property {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==17)
	{
		$nRowsIn=-1;
		if ( $mbkiemptyf==1) $subjectid=0;
	if  (($decisionAfterScorFirst!=2) )	{
$sql="SELECT count(contracts.id) FROM `contractsMBKI` contracts  WHERE  `CreditorType` like 'Фінансова компанія%'  and contracts.SubjectId=$subjectid and `PereodicityOfPayments`='В останній день строку дії договору'  and `TotalAmountValue`<=20000 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}
		

if ($conditions=='<1'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
}

}
if ($conditions=='2')
{
if (($nRowsIn==2) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='3'){
if (($nRowsIn==3)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='>3'){
if (($nRowsIn>3)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
}

}
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==18)
	{
			$nRowsIn=-1;
			if ( $mbkiemptyf==1) $subjectid=0;
		if  (($decisionAfterScorFirst!=2) )	{
$sql="select NumberOfTerminatedContracts from summaryinformationdebtorMBKI where subjectid=$subjectid 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql);
	}
 
if ($conditions=='<1'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='2')
{
if (($nRowsIn==2) ){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='3')
{
if (($nRowsIn==3) ){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='>3'){
if (($nRowsIn>3)){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}


	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==20)
	{
		$nRowsIn=-1;
		if ( $mbkiemptyf==1) $subjectid=0;
			if  (($decisionAfterScorFirst!=2))	{
$sql="select  InquieryValue from inqueryMBKI where SubjectId=$subjectid and  id=(select min(id) from inqueryMBKI where SubjectId=$subjectid)
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}
	
if ($conditions=='1'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='2')
{
if (($nRowsIn==2) ){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='3'){
if (($nRowsIn==3)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
}

}

if ($conditions=='>3'){
if (($nRowsIn>3)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}

		
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==22)
	{
		$nRowsIn=-1;
		if ( $mbkiemptyf==1) $subjectid=0;
			if  (($decisionAfterScorFirst!=2) )	{
$sql="select count(id) from SearchInqueryMBKI where SubjectId=$subjectid
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}
		
		

if ($conditions=='0'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='between 1 and 5'){
if (($nRowsIn==1) or ($nRowsIn==2)or ($nRowsIn==3)or ($nRowsIn==4)or ($nRowsIn==5)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='>5')
{
if (($nRowsIn>5) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
}

}

			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==25)
	{
		$nRowsIn=-1;
		if ( $mbkiemptyf==1) $subjectid=0;
		if  (($decisionAfterScorFirst!=2))	{
$sql="select count(id) from SearchInqueryMBKI where Dated > (NOW() - INTERVAL 1 MONTH) and SubjectId=$subjectid
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}
if ($conditions=='0'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1')
{
if (($nRowsIn>0) ){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

		
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
}
	if ($idparameter==26)
	{
		$nRowsIn=-1;
		$nRowsInSec=-1;
		if ( $mbkiemptyf==1) $subjectid=0;
	if  (($decisionAfterScorFirst!=2) )	{
$sql="SELECT count(contracts.id) FROM `contractsMBKI` contracts  WHERE  ContractType='Existing' and`CreditorType` like 'Фінансова компанія%'  and contracts.SubjectId=$subjectid and `PereodicityOfPayments`='В останній день строку дії договору'  and `TotalAmountValue`<=20000 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	$sql="SELECT count(contracts.id) FROM `contractsMBKI` contracts  WHERE  `CreditorType` like 'Фінансова компанія%'  and contracts.SubjectId=$subjectid and `PereodicityOfPayments`='В останній день строку дії договору'  and `TotalAmountValue`<=20000 
";
print $sql;
	$nRowsInSec=DatabaseHandler::GetOne($sql); 
	if ($nRowsIn==0) {$strscoring="Ні";} else {$strscoring="Так";}
$strscoring=$strscoring."/{$nRowsInSec}";
	}
		
	print "<br />";
		 print 	"nRowsIn=";
	 print 	$nRowsIn;
	 print "<br />";	
print "<br />";
	 print 	"nRowsInSec=";
	 print 	$nRowsInSec;
	 print "<br />";	
	 print $conditions;
	 	 print "<br />";	
if ($conditions=='Ні/0'){
if (($nRowsIn==0) and ($mbkiemptyf==0) and ($nRowsInSec==0)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." {$strscoring} Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Ні/1'){
if (($nRowsIn==0) and ($nRowsInSec==1)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Ні/2')
{
if (($nRowsIn==0) and ($nRowsInSec==2) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Ні/3'){
if (($nRowsIn==0) and ($nRowsInSec==3) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Ні/99'){
if (($nRowsIn==0) and ($nRowsInSec>3) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Так/0'){
if (($nRowsIn>0) and ($nRowsInSec==0)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
	DatabaseHandler::Execute($sql);
}

}

if ($conditions=='Так/1'){
if (($nRowsIn>0) and ($nRowsInSec==1)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Так/2')
{
if (($nRowsIn>0) and ($nRowsInSec==2) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Так/3'){
if (($nRowsIn>0) and ($nRowsInSec==3) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Так/99'){
if (($nRowsIn>0) and ($nRowsInSec>3) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
}
if ($idparameter==27)
	{
		$sql="select status_of_residence,property from ClientsForCheckBKI where creditid=$creditid ";
print $sql;
	$nRowsIn=DatabaseHandler::GetAll($sql); 
	
if ($nRowsIn[0]['status_of_residence']=='1_Одноосібна власність'){
	if ($conditions=='="1_Одноосібна власність"'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,1,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
}
if (($nRowsIn[0]['status_of_residence']!='1_Одноосібна власність') and ($nRowsIn[0]['property']=='2_Так')){
	if ($conditions=='="2_Так"'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
}
if (($nRowsIn[0]['status_of_residence']!='1_Одноосібна власність') and (($nRowsIn[0]['property']=='1_Ні') )){
	if ($conditions=='="1_Ні"'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
}
if (($nRowsIn[0]['status_of_residence']!='1_Одноосібна власність') and (($nRowsIn[0]['property']!='1_Ні') and ($nRowsIn[0]['property']!='2_Так'))){
	if ($conditions=='Не заповнено'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,2,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
}

}

}
$sql="select d.conditions conditions,d.score score from   bscoringcard d 
where  ascoringcard_id=$id and conditions='NULL'";
print $sql;
 	$resultsdd=DatabaseHandler::GetAll($sql);
	print_r($resultsdd);
for($k = 0; $k < count($resultsdd); ++$k) {
	$conditions=$resultsdd[$k]['conditions'];
	 $score=$resultsdd[$k]['score'];
	 	$sql="select 1 from scoringresult where scoringcardid=$scoringcardid and idparameter=$idparameter and applicationid=$applicationid and idrunscoring=2";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
print $nRowsIn;
if (($nRowsIn==0)){
	$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,2,$scoreScor)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}
}


	}
$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,100,0,2,$scoreScor)";
				print "<br />";
	 print $sql;
	 print "<br />";
		//DatabaseHandler::Execute($sql);

$sql="SELECT minBall FROM `parameterrunscorcard` where scoringcardid=$scoringcardid and status=1 and BKI=2";
print $sql;
$minBall1=DatabaseHandler::GetOne($sql);
if (is_null($minBall1) or ($minBall1=='')) $minBall1=0;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\"  Заг сума скорингу=$scoreScor. Мін сума для переходу в 2 БКІ=$minBall1',$applicationid,'Scoring',3000)";
			DatabaseHandler::Execute($sql);
						print "<br />";
			print abs($minBall1);
			print "<br />";
			print abs($scoreScor);
						print "<br />";
			if ((abs($minBall1)<abs($scoreScor)) and ($minBall1<0) and ($scoreScor<0))
			{
				print "-";
			$decisionAfterScorSecond=2;	
			} else if (($scoreScor<$minBall1) and ($minBall1>0) and ($scoreScor>0))
{
			print "+";
//		$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='null'";
$decisionAfterScorSecond=2;
/*
 	$resultsddddd=DatabaseHandler::GetAll($sql);
for($r = 0; $r < count($resultsddddd); ++$r) {
	 $zoneSc=$resultsddddd[$r]['zone'];
	 $rejectTo=$resultsddddd[$r]['rejectTo'];
	 $approveFrom=$resultsddddd[$r]['approveFrom'];

if ($approveFrom<=$scoreScor){
$statusCheck=1;
	} else
if ($rejectTo>$scoreScor){
	
	$statusCheck=3;
	
}}*/
} else {$decisionAfterScorSecond=1; print "-+";}

}
if (count($results)==0) $decisionAfterScorSecond=1;

}
 }

if ($statusCheck>0) $ubkiemptyf=1;
print "<br />";
print $statusCheck;
print "<br />";
print "УБКІ";
if (($bki['NameBki']=='УБКІ') and (($statusCheck==0) or ($statusCheck==1) or ($statusCheck==2)) and  ($decisionAfterScorSecond!=2)){
	$ubkiExists=2;
$req ="<?xml version='1.0' encoding='utf-8'?>";
$req1 ="<doc><auth login='041179' pass='Inf052016'/></doc>";
$login=$ubkilogin;
$pass=$ubkipassword;
//Боевая среда/тестовая среда
if($_SERVER['SERVER_PORT']==80) $SERVER_PORT = '443';
else $SERVER_PORT = $_SERVER['SERVER_PORT'];
$ident=$inn;
$sql="select IFNULL(id,0) from credit_buroUBKI a where StateCode='$ident'  and (id=$idubki or $idubki=0) and CreatedAt>= CURDATE() - INTERVAL 30 DAY and exists(select 1 from ckiUBKI where idubki=a.id)";

$CreditInfoId=DatabaseHandler::GetOne($sql); 

$idubki=$CreditInfoId;
$req_xml="";
print $CreditInfoId;
if ($CreditInfoId==0) 
{
 $url = 'https://secure.ubki.ua/b2_api_xml/ubki/auth';
print 'Start';
$req = 
'<?xml version="1.0" encoding="utf-8" ?>'.
'<doc>'.
'<auth login="'.$login.'" pass="'.$pass.'"/>'.
'</doc>';
print $req;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, '');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POSTFIELDS, base64_encode($req) );
  print_r($ch);
	 $res = curl_exec($ch);

$rxml = simplexml_load_string($res);
print_r($rxml);
$sessid=$rxml->auth['sessid'];

print $sessid;
        if (curl_errno($ch)) {
            echo curl_error($ch);
          }

        curl_close($ch);
$sql="select max(id) from subjectMBKI where TaxpayerNumber='".$ident."'";
print $sql;


$subjectId=DatabaseHandler::GetOne($sql);
print "asddas";
print $subjectId;
//$sessid='CF66E87F9083478F98DD31F9B662B8E2';

if ((isset($subjectId)) and ($subjectId>0) and ($resultZapyt==false))
{
	$sql="select Surname,Name,FathersName,DATE_FORMAT(DateOfBirth,'%Y-%m-%d') DateOfBirth,Passport from subjectMBKI where id=".$subjectId." and TaxpayerNumber='".$ident."'";
	print $sql;
$dani=DatabaseHandler::GetAll($sql);
print_r($dani);
$Surname=$dani[0]['Surname'];
$Name=$dani[0]['Name'];
$FathersName=addslashes($dani[0]['FathersName']);
$DateOfBirth=$dani[0]['DateOfBirth'];
$passport=$dani[0]['Passport'];
print $DateOfBirth;
$sql="SELECT Value FROM `contactMBKI` where SubjectId=$subjectId and ImportCode=3";	
print $sql;
$phonemb=DatabaseHandler::GetOne($sql);
if (is_null($phonemb) or ($phonemb=='')) {$phonemb=$mobile_phone;}

if (strlen($phonemb)==12) {$phonemb='+'.$phonemb;}
if (strlen($phonemb)==10) {$phonemb='+38'.$phonemb;}
	if (($id_card_number<>0) and ($id_card_number<>'')) {
$req_xml = '<request version="" reqtype="10" reqreason="4" reqdate="2017-05-23" reqidout="">'.
'<i reqlng="2">'.
'<ident okpo="'.$ident.'" lname="'.$Surname.'" fname="'.$Name.'" mname="'.$FathersName.'" bdate="'.$DateOfBirth.'"></ident>'.
'<contacts><cont ctype="3" cval="'.$phonemb.'"/></contacts>'.
 '<docs><doc dtype="17" dser="" dnom="'.$id_card_number.'"/>   </docs>'.
''.
'</i>'.
'</request>';	
} else {
$req_xml = '<request version="" reqtype="10" reqreason="4" reqdate="2017-05-23" reqidout="">'.
'<i reqlng="2">'.
'<ident okpo="'.$ident.'" lname="'.$Surname.'" fname="'.$Name.'" mname="'.$FathersName.'" bdate="'.$DateOfBirth.'"></ident>'.
'<contacts><cont ctype="3" cval="'.$phonemb.'"/></contacts>'.
 '<docs><doc dtype="1" dser="'.substr($passport,1,2).'" dnom="'.substr($passport,3,6).'"/>   </docs>'.
''.
'</i>'.
'</request>';}

print "\n";
print "<br />";

$sql="insert into testLog(`test`, `inn`, `type`, `applicationid`,`xml`)  values('Звіт з УБКІ','$ident',2,$applicationid,'$req_xml')";


//DatabaseHandler::Execute($sql);
 

print "<br />";
print "\n";
$req_envelope='<req_envelope><req_xml>'.base64_encode($req_xml).'</req_xml></req_envelope>';

$post='<?xml version="1.0" encoding="UTF-8"?><doc><ubki sessid="'.$sessid.'">'.$req_envelope.'</ubki></doc>';
print $post;
$url="https://secure.ubki.ua/b2_api_xml/ubki/xml";
$headers = array("POST ".$page." HTTP/1.0",
"Content-Type:text/xml;charset=\"utf-8\"",
"Accept: text/xml",
"Content-Length:".strlen($post));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
print_r($ch);
$resultZapyt = curl_exec($ch);
$info = curl_getinfo($ch); 
print_r($info); 
print_r($resultZapyt);
print $post;
print ' -- УБКІ ';
	
} else $subjectId=0;
if (!isset($resultZapyt)) {$resultZapyt=false;}
$kilkzapyt=3;
do{
	 curl_close($ch);
if ( ($resultZapyt==false) and ($zapyt==0))
{
	print ' -- УБКІ ';
	$zapyt=1;
if (strlen($mobile_phone)==12) {$mobile_phone='+'.$mobile_phone;}
if (strlen($mobile_phone)==10) {$mobile_phone='+38'.$mobile_phone;}	
print $mobile_phone;
print "<br />";
$req_xml='<request ';
print "<br />";
print 'Start';
if (($id_card_number<>0) and ($id_card_number<>'')) {
$req_xml = '<request version="" reqtype="10" reqreason="4" reqdate="2017-05-23" reqidout="">'.
'<i reqlng="2">'.
'<ident okpo="'.$ident.'" lname="'.$last_name.'" fname="'.$first_name.'" mname="'.$patronymic.'" bdate="'.$date_of_birth.'"></ident>'.
'<contacts><cont ctype="3" cval="'.$mobile_phone.'"/></contacts>'.
 '<docs><doc dtype="17" dser="" dnom="'.$id_card_number.'"/>   </docs>'.
''.
'</i>'.
'</request>';	
} else {
$req_xml = '<request version="" reqtype="10" reqreason="4" reqdate="2017-05-23" reqidout=""><i reqlng="2">'.
'<ident okpo="'.$ident.'" lname="'.$last_name.'" fname="'.$first_name.'" mname="'.$patronymic.'" bdate="'.$date_of_birth.'"></ident>'.
' <contacts><cont ctype="3" cval="'.$mobile_phone.'"/> </contacts>'.
'<docs>     <doc dtype="1" dser="'.strtoupper($series_of_passport).'" dnom="'.$number_of_passport.'"/>   </docs>'.
''.
'</i>'.
'</request>';}

$sql="insert into testLog(`test`, `inn`, `type`, `applicationid`,`xml`)  values('Звіт з УБКІ','$ident',2,$applicationid,'$req_xml')";


//DatabaseHandler::Execute($sql);

print "<br />";
echo "<pre>". htmlentities($req_xml) . "</pre>";

$req_envelope='<req_envelope><req_xml>'.base64_encode($req_xml).'</req_xml></req_envelope>';

$post='<?xml version="1.0" encoding="UTF-8"?><doc><ubki sessid="'.$sessid.'">'.$req_envelope.'</ubki></doc>';
print $post;
$url="https://secure.ubki.ua/b2_api_xml/ubki/xml";
$headers = array("POST ".$page." HTTP/1.0",
"Content-Type:text/xml;charset=\"utf-8\"",
"Accept: text/xml",
"Content-Length:".strlen($post));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
print_r($ch);
$resultZapyt = curl_exec($ch);
$info = curl_getinfo($ch); 
print_r($info); 
print_r($resultZapyt);
print $post;
print ' -- УБКІ ';
}
if ($resultZapyt==false) {$idubki=0;}
else {
$fileubki='';
	

	$fileubki="ubk_{$inn}_{$nameFolder}";
	if (file_exists("{$fileubki}.xml")) {
          unlink("{$fileubki}.xml");
  
}

file_put_contents("{$fileubki}.xml", $resultZapyt);
 if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/xml_bki/{$fileubki}.xml")) {
        unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/xml_bki/{$fileubki}.xml");
    }
if (file_exists("{$fileubki}.xml")) {
copy("{$fileubki}.xml","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/xml_bki/{$fileubki}.xml");
}

$xml = new SimpleXMLElement($resultZapyt);
$reqid=($xml->tech->reqinfo['reqid']);
$xmm=$xml->xpath('//comp[@id="1"]/cki');
if (isset($xmm[0]['inn'])) {
$ubkiExists=2;
$result1=addslashes($resultZapyt);
if (!isset($result1)) $result1='';
if ($result1=='') $result1=$result;

$sql="insert into credit_buroUBKI(xml,bki,StateCode) values('$result1',3,'$ident')";
;
print 'Start';
print $sql;
$results_array=DatabaseHandler::Execute($sql);

$sql="SELECT LAST_INSERT_ID()";
$idubki=DatabaseHandler::GetOne($sql); 

$bdate=($xmm[0]['bdate']);
$reqlngref=($xmm[0]['reqlngref']);
$reqlng=($xmm[0]['reqlng']);
$mname=addslashes($xmm[0]['mname']);
$fname=addslashes($xmm[0]['fname']);
$lname=addslashes($xmm[0]['lname']);
//$inn=($xmm[0]['inn']);
$sql="SET NAMES 'utf8'";
DatabaseHandler::Execute($sql);
$sql="insert into ckiUBKI values(null,'$reqid','$bdate',
'$reqlngref',
$reqlng,
'$mname',
'$fname',
'$lname',
'$ident',
$idubki,
$applicationid
)";

DatabaseHandler::Execute($sql);
$sql="SELECT LAST_INSERT_ID()";
$lastinsertId=DatabaseHandler::GetOne($sql); 
foreach ($xmm[0]->ident as $item) {
	if (isset($item['cchild']) and ($item['cchild']<>'')) 	$cchild=$item['cchild'];
	else $cchild=0;
	$sstateref=$item['sstateref'];
	if (isset($item['sstate'])) $sstate=$item['sstate'];
	$sstate=0;
	$spdref=$item['spdref'];
	if (isset($item['spd'])) 	$spd=$item['spd'];
	$spd=0;
	$cgragref=$item['cgragref'];
	if (isset($item['cgrag']) and ($item['cgrag']<>'')) 	$cgrag=$item['cgrag'];
	else $cgrag=0;
	$ceducref=$item['ceducref'];
	if (isset($item['ceduc'])) 	$ceduc=$item['ceduc'];
	$ceduc=0;
	$familyref=$item['familyref'];
	if (isset($item['family'])) 	$family=$item['family'];
	$family=0;
	$csexref=$item['csexref'];
		if (isset($item['csex'])) 	$csex=$item['csex'];
		$csex=0;
	$bdate=$item['bdate'];
	$mname=addslashes($item['mname']);
	$fname=addslashes($item['fname']);
	$lname=addslashes($item['lname']);
	$inn=$item['inn'];
	$lngref=$item['lngref'];
	$lng=$item['lng'];
	$vdate=$item['vdate'];
$sql="insert into identUBKI values(null,$lastinsertId,$cchild,
'$sstateref',
$sstate,
'$spdref',
$spd,
'$cgragref',
$cgrag,
'$ceducref',
$ceduc,
'$familyref',
$family,
'$csexref',
$csex,
'$mname',
'$fname',
'$lname',
'$inn',
'$lngref',
'$vdate',
'$bdate',
$lng
)";
print $sql;
DatabaseHandler::Execute($sql);
}
print 'Error';
foreach ($xmm[0]->work as $item) {
if (isset($item['wdohod']) and ($item['wdohod']<>'') ) 	$wdohod=$item['wdohod'];
else $wdohod=0;

if (isset($item['wstag']) and ($item['wstag']<>'')) 	$wstag=$item['wstag'];
else $wstag=0;
	
$wname='';	
$wokpo=$item['wokpo'];
	$cdolgnref=$item['cdolgnref'];
	if (isset($item['cdolgn']) and ($item['cdolgn']<>'')) 	$cdolgn=$item['cdolgn'];
	else $cdolgn=0;
	$lngref=$item['lngref'];
	if (isset($item['lng']) and ($item['lng']<>'')) 	$lng=$item['lng'];
	else $lng=0;
	$vdate=$item['vdate'];
$sql="insert into workUBKI values(null,$lastinsertId,$wdohod,
$wstag,
'$wname',
'$wokpo',
'$cdolgnref',

$cdolgn,
'$lngref',
$lng,
'$vdate'
)";
print $sql;
DatabaseHandler::Execute($sql);	

}
foreach ($xmm[0]->doc as $item) {
	$dwdt=addslashes($item['dwdt']);
	$dwho=addslashes($item['dwho']);
	$dterm=addslashes($item['dterm']);
	$dnom=addslashes($item['dnom']);
	$dser=addslashes($item['dser']);
	$dtyperef=addslashes($item['dtyperef']);
	if (isset($item['dtype'])) 	$dtype=$item['dtype'];
	$dtype=0;
	$lngref=$item['lngref'];
	if (isset($item['lng']) and ($item['lng']<>'')) $lng=$item['lng'];
	else $lng=0;
	$vdate=$item['vdate'];
$sql="insert into docUBKI values(null,$lastinsertId,'$dwdt',
'$dwho',
'$dterm',
'$dnom',
'$dser',
'$dtyperef',
$dtype,
'$lngref',
$lng,
'$vdate'
)";
print $sql;
DatabaseHandler::Execute($sql);		

}
foreach ($xmm[0]->addr as $item) {
	$adflat=addslashes($item['adflat']);
	$adcorp=addslashes($item['adcorp']);
	$adhome=addslashes($item['adhome']);
	$adstreet=addslashes($item['adstreet']);
	$adcitytyperef=$item['adcitytyperef'];
	if (isset($item['adcitytype']) and ($item['adcitytype']<>'')) 	$adcitytype=$item['adcitytype'];
	else $adcitytype=0;
	$adcity=addslashes($item['adcity']);
	$adarea=addslashes($item['adarea']);
	$adstate=addslashes($item['adstate']);
		$adindex=addslashes($item['adindex']);	
		$adcountry=addslashes($item['adcountry']);
	$adtyperef=$item['adtyperef'];
	if (isset($item['adtype'])) 		$adtype=$item['adtype'];
	else $adtype=0;
	$lngref=$item['lngref'];
	if (isset($item['lng'])) 	$lng=$item['lng'];
	else $lng=0;
	$vdate=$item['vdate'];	
	$sql="insert into addrUBKI values(null,$lastinsertId,'$adflat',
'$adcorp',
'$adhome',
'$adstreet',
'$adcitytyperef',
$adcitytype,
'$adcity',
'$adarea',
'$adstate',
'$adindex',
'$adcountry',
'$adtyperef',
$adtype,
'$lngref',
$lng,
'$vdate'
)";
print $sql;
DatabaseHandler::Execute($sql);		
}
$xmm=$xml->xpath('//comp[@id="2"]');
foreach ($xmm[0]->crdeal as $item) {
$dlamtobes=$item['dlamtobes'];
$dlrolesubref=$item['dlrolesubref'];
$dlrolesub=$item['dlrolesub'];
$dldonor=$item['dldonor'];
$dlamt=$item['dlamt'];
$dlcurrref=$item['dlcurrref'];
$dlcurr=$item['dlcurr'];
$dlporpogref=$item['dlporpogref'];
if (isset($item['dlporpog']) and ($item['dlporpog']<>''))$dlporpog=$item['dlporpog'];
else $dlporpog=0;
$dlvidobesref=$item['dlvidobesref'];
$dlvidobes=$item['dlvidobes'];
$dlcelcredref=$item['dlcelcredref'];
if (isset($item['dlcelcred']) and ($item['dlcelcred']<>'')) $dlcelcred=$item['dlcelcred'];
else $dlcelcred=0;
$bdate=$item['bdate'];
$mname=addslashes($item['mname']);
$fname=addslashes($item['fname']);
$lname=addslashes($item['lname']);
$inn=$item['inn'];
$lngref=$item['lngref'];
$lng=$item['lng'];
$dlref=$item['dlref'];
	$sql="insert into crdealUBKI values(null,$lastinsertId,'$dlamtobes',
'$dlrolesubref',
$dlrolesub,
'$dldonor',
$dlamt,
'$dlcurrref',
$dlcurr,
'$dlporpogref',
$dlporpog,
'$dlvidobesref',
'$dlvidobes',
'$dlcelcredref',
$dlcelcred,
'$bdate',
'$mname',
'$fname',
'$lname',
'$inn',
'$lngref',
$lng,
'$dlref'
)";
print $sql;
DatabaseHandler::Execute($sql);	
$sql="SELECT LAST_INSERT_ID()";
$lastinsertId2=DatabaseHandler::GetOne($sql); 	
foreach($item->deallife as $itemsub)
{
	
	$dldateclc=$itemsub['dldateclc'];
	$dlfluseref=$itemsub['dlfluseref'];
	$dlfluse=$itemsub['dlfluse'];
	$dlflbrkref=$itemsub['dlflbrkref'];
	$dlflbrk=$itemsub['dlflbrk'];
	$dlflpayref=$itemsub['dlflpayref'];
	if (isset($itemsub['dlflpay']) and ($itemsub['dlflpay']<>''))
	$dlflpay=$itemsub['dlflpay'];
else $dlflpay=0;
	if (isset($itemsub['dldayexp']) and ($itemsub['dldayexp']<>''))
	$dldayexp=$itemsub['dldayexp'];
else $dldayexp=0;
	if (isset($itemsub['dlamtexp']) and ($itemsub['dlamtexp']<>''))
	$dlamtexp=$itemsub['dlamtexp'];
else $dlamtexp=0;
if (isset($itemsub['dlamtcur']) and ($itemsub['dlamtcur']<>''))
	$dlamtcur=$itemsub['dlamtcur'];
else $dlamtcur=0;
if (isset($itemsub['dlamtpaym']) and ($itemsub['dlamtpaym']<>''))
	$dlamtpaym=$itemsub['dlamtpaym'];
else $dlamtpaym=0;
	if (isset($itemsub['dlamtlim']) and ($itemsub['dlamtlim']<>''))
	$dlamtlim=$itemsub['dlamtlim'];
else $dlamtlim=0;
	$dlflstatref=$itemsub['dlflstatref'];
	$dlflstat=$itemsub['dlflstat'];
	$dldff=$itemsub['dldff'];
	$dldpf=$itemsub['dldpf'];
	$dlds=$itemsub['dlds'];
	$dlyear=$itemsub['dlyear'];
	$dlmonth=$itemsub['dlmonth'];
	$dlref=$itemsub['dlref'];
	$sql="insert into deallifeUBKI values(null,$lastinsertId,'$dldateclc',
'$dlfluseref',
'$dlfluse',
'$dlflbrkref',
'$dlflbrk',
'$dlflpayref',
$dlflpay,
$dldayexp,
$dlamtexp,
$dlamtcur,
$dlamtpaym,
$dlamtlim,
'$dlflstatref',
$dlflstat,
'$dldff',
'$dldpf',
'$dlds',
$dlyear,
$dlmonth,
'$dlref',
$lastinsertId2
)";
print $sql;
DatabaseHandler::Execute($sql);			
	
	
	
}

}
$xmm=$xml->xpath('//comp[@id="3"]');
foreach ($xmm[0]->susd as $item) {
$inn=$item['inn'];
$voteid=$item['voteid'];
$votedate=$item['votedate'];
$voteusrst=$item['voteusrst'];
$voteusrstref=$item['voteusrstref'];
$votetype=$item['votetype'];
$votetyperef=$item['votetyperef'];
$votesudname=$item['votesudname'];
$voteurfact=$item['voteurfact'];
	$sql="insert into susdUBKI values(null,$lastinsertId,
	'$inn',
	'$voteid',
	'$votedate',
	$voteusrst,
	'$voteusrstref',
	$votetype,
	'$votetyperef',
	'$votesudname',
	$voteurfact
)";
print $sql;
DatabaseHandler::Execute($sql);			
	



}
$xmm=$xml->xpath('//comp[@id="4"]');
foreach ($xmm[0]->credres as $item) {
$org=addslashes($item['org']);
$reqreasonref=addslashes($item['reqreasonref']);
$reqreason=addslashes($item['reqreason']);
$resultref=addslashes($item['resultref']);
$result=addslashes($item['result']);
$reqid=($item['reqid']);
$inn=($item['inn']);
$redate=($item['redate']);
	$sql="insert into credresUBKI values(null,$lastinsertId,
	'$org',
	'$reqreasonref',
	'$reqreason',
	'$resultref',
	'$result',
	'$reqid',
	'$inn',
	'$redate'
)";
print $sql;
DatabaseHandler::Execute($sql);
}
foreach ($xmm[0]->reestrtime as $item) {
$hr=($item['hr']);
$da=($item['da']);
$wk=($item['wk']);
$mn=($item['mn']);
$qw=($item['qw']);
$ye=($item['ye']);
$yu=($item['yu']);

$sql="insert into reestrtimeUBKI values(null,$lastinsertId,
	$hr,
	$da,
	$wk,
	$mn,
	$qw,
	$ye,
	$yu
)";
print $sql;
DatabaseHandler::Execute($sql);
}
$xmm=$xml->xpath('//comp[@id="8"]');

foreach ($xmm[0]->urating as $item) {

$inn=$item['inn'];
$lname=addslashes($item['lname']);
$fname=addslashes($item['fname']);
$mname=addslashes($item['mname']);
$bdate=$item['bdate'];
if (isset($item['score']) and ($item['score']<>'')  and($item['score']<>'NA'))
	$score=$item['score'];
else $score=0;

$scorelast=$item['scorelast'];
$scoredate=$item['scoredate'];
$scoredatelast=$item['scoredatelast'];

$scorelevel=0;
$score3=$item['score3'];
$sql="insert into uratingUBKI values(null,$lastinsertId,
	'$inn',
	'$lname',
	'$fname',
	'$mname',
	'$bdate',
	$score,
	'$scorelast',
	'$scoredate',
	'$scoredatelast',
	$scorelevel,
	'$score'
)";
print $sql;
DatabaseHandler::Execute($sql);

if (isset($item->dinfo['all']) and ($item->dinfo['all']<>'')  )
$dinfoall=$item->dinfo['all'];
else $dinfoall=0;
if (isset($item->dinfo['open']) and ($item->dinfo['open']<>'')  )
$dinfoopen=$item->dinfo['open'];
else $dinfoopen=0;
$dinfoopentext=addslashes($item->dinfo['opentext']);

if (isset($item->dinfo['close']) and ($item->dinfo['close']<>'')  )
	$dinfoclose=$item->dinfo['close'];
else $dinfoclose=0;
$dinfoexpyear=$item->dinfo['expyear'];
$dinfomaxnowexp=$item->dinfo['maxnowexp'];
$dinfodatelastupdate=$item->dinfo['datelastupdate'];
$sql="insert into dinfoUBKI values(null,$lastinsertId,
	$dinfoall,
	$dinfoopen,
	'$dinfoopentext',
	$dinfoclose,
	'$dinfoexpyear',
	'$dinfomaxnowexp',
	'$dinfodatelastupdate'
)";

DatabaseHandler::Execute($sql);
$sql="SELECT LAST_INSERT_ID()";
$lastinsertId3=DatabaseHandler::GetOne($sql); 
foreach($item->comments->comment as $itemd){
	 $commid=$itemd['id'];
	 $commtext=addslashes($itemd['text']);
$sql="insert into commentsUBKI values(null,$lastinsertId,
	$lastinsertId3,
	'$commtext',''
)";

DatabaseHandler::Execute($sql);
}
foreach($item->pfactors->comment as $itemd){
	 
	 $commtext=addslashes($itemd['text']);
$sql="insert into commentsUBKI values(null,$lastinsertId,
	$lastinsertId3,
	'$commtext',
	'pfactors'
)";

DatabaseHandler::Execute($sql);
}
foreach($item->nfactors->comment as $itemd){
	 
	 $commtext=addslashes($itemd['text']);
$sql="insert into commentsUBKI values(null,$lastinsertId,
	$lastinsertId3,
	'$commtext',
	'nfactors'
)";

DatabaseHandler::Execute($sql);
}
}
$xmm=$xml->xpath('//comp[@id="10"]');
foreach($xmm[0]->cont as $item){
	$cval=$item['cval'];
	$ctyperef=$item['ctyperef'];
	if (isset($item['ctype']) and ($item['ctype']<>'')  )
	$ctype=$item['ctype'];
	else $ctype=0;
	$vdate=$item['vdate'];
$sql="insert into contUBKI values(null,$lastinsertId,
	'$cval',
	
	'$ctyperef',
	$ctype,
	'$vdate'
)";

DatabaseHandler::Execute($sql);
}
break;
} else {$idubki=0;}
}
$kilkzapyt--;
} while($kilkzapyt>0);
}
if ($idubki<>0){
	
		$sql="select id from ckiUBKI where idubki=$idubki and inn='$inn'";
	$CreditInfoId=DatabaseHandler::GetOne($sql);
if  ($CreditInfoId<>0){
	$Zone='';
	$ubkiemptyf=0;
	// 1 перевірка
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=19 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="SELECT count(distinct crdealid) FROM `deallifeUBKI` WHERE `dlflstat`  in (3,7,6) and cki_id=$CreditInfoId" ;
$NumberOfExistingContracts=DatabaseHandler::GetOne($sql);

if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  WhiteZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',1)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',1)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  redZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',1)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',1)";

}
// print $sql;
DatabaseHandler::Execute($sql);
	

	// 2 перевірка
	$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=20 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (strlen($mobile_phone)==12) $mobile_phone1='+'.$mobile_phone;
if (strlen($mobile_phone)>10) $mobile_phone3='+38'.substr($mobile_phone,-10);
	
if (strlen($home_phone)==12) $home_phone1='+'.$home_phone;
if (strlen($home_phone)>10) $home_phone3='+38'.substr($home_phone,-10);
if (strlen($home_phone_residence)==12) $home_phone_residence1='+'.$home_phone_residence;
if (strlen($home_phone_residence)>10) $home_phone_residence3='+38'.substr($home_phone_residence,-10);
if (strlen($work_phone)==12) $work_phone1='+'.$work_phone;
	if (strlen($mobile_phone)==10) $mobile_phone2='+38'.$mobile_phone;

if (strlen($home_phone)==10) $home_phone2='+38'.$home_phone;

if (strlen($home_phone_residence)==10) $home_phone_residence2='+38'.$home_phone_residence;

if (strlen($work_phone)==10) $work_phone2='+38'.$work_phone;
if (strlen($work_phone)>10) $work_phone3='+38'.substr($work_phone,-10);

$sql="select count(*) FROM `contUBKI` where cki_id=$CreditInfoId and `cval` in ('$mobile_phone','$home_phone','$home_phone_residence','$work_phone','$mobile_phone2','$home_phone2','$home_phone_residence2','$work_phone2','$mobile_phone1','$home_phone1','$home_phone_residence1','$work_phone1','$mobile_phone3','$home_phone3','$home_phone_residence3','$work_phone3')";
 print $sql;
$countPhone=DatabaseHandler::GetOne($sql);
if (($countPhone>=$resArray['whiteZoneFrom']) and ($countPhone<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  WhiteZone {$resArray['Name']}',$applicationid,'$Zone',2)";
if ($statusCheck==0) {$statusCheck=1;}
$match_phone_UBKI=1;
}else if (($countPhone>=$resArray['greyZoneFrom']) and ($countPhone<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  GreyZone {$resArray['Name']}',$applicationid,'$Zone',2)";

if ($statusCheck<2) {$statusCheck=2;}
$match_phone_UBKI=0;
}
else if (($countPhone>=$resArray['redZoneFrom']) and ($countPhone<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  RedZone {$resArray['Name']}',$applicationid,'$Zone',2)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',2)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 3 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=21 and status=1";
$resArray=DatabaseHandler::GetALL($sql)[0];
$sql="select count(*) from `contUBKI` where cki_id=$CreditInfoId and `cval` in ('$email')";

$countEmail=DatabaseHandler::GetOne($sql);
if (($countEmail>=$resArray['whiteZoneFrom']) and ($countEmail<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  WhiteZone {$resArray['Name']}',$applicationid,'$Zone',3)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($countEmail>=$resArray['greyZoneFrom']) and ($countEmail<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  GreyZone {$resArray['Name']}',$applicationid,'$Zone',3)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($countEmail>=$resArray['redZoneFrom']) and ($countEmail<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  RedZone {$resArray['Name']}',$applicationid,'$Zone',3)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',3)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 4 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=22 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(*) from (select crdealid,max(dlflstat) mx from  crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$CreditInfoId  and not exists(select * from deallifeUBKI  where cki_id=$CreditInfoId and dlflstat=2 and crdealid=b.crdealid )   group by crdealid) a";
$NumberOfExistingContracts=DatabaseHandler::GetOne($sql);
if ($NumberOfExistingContracts=='') $NumberOfExistingContracts=0;
if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$NumberOfExistingContracts ',$applicationid,'$Zone',4)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',4)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',4)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',4)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 5 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=23 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select sum(dlamtexp) from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where  b.cki_id=$CreditInfoId and
 concat(cast(dlyear as char(4)),'-',case when dlmonth<10 then concat('0',cast(dlmonth  as char(1))) else cast(dlmonth  as char(2)) end,'-','01')=(select max(concat(cast(dlyear as char(4)),'-',case when dlmonth<10 then concat('0',cast(dlmonth  as char(1))) else cast(dlmonth  as char(2)) end,'-','01')) from deallifeUBKI where crdealid=b.crdealid)";

$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
if ($TotalOutstandingDebtValue=='') $TotalOutstandingDebtValue=0;
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',5)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',5)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',5)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',5)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 6 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=24 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(*) from (select crdealid,max(dlflstat) mx from  crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$CreditInfoId and dlcelcred not in (31) and not exists(select * from deallifeUBKI  where cki_id=$CreditInfoId and dlflstat=2 and crdealid=b.crdealid )   group by crdealid) a ";

$NumberOfExistingContracts=DatabaseHandler::GetOne($sql);
if ($NumberOfExistingContracts=='') $NumberOfExistingContracts=0;
if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$NumberOfExistingContracts ',$applicationid,'$Zone',6)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',6)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',6)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',6)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 7 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=25 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(distinct a.id) from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$CreditInfoId and dlcelcred not in (31)  and `dlflstat`=2 and b.dldateclc between CURDATE() - INTERVAL $paramclosecreditperiod Day AND SYSDATE() ";
print $sql;
$NumberOfTerminatedContracts=DatabaseHandler::GetOne($sql);
if ($NumberOfTerminatedContracts=='') $NumberOfTerminatedContracts=0;
if (($NumberOfTerminatedContracts>=$resArray['whiteZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$NumberOfTerminatedContracts ',$applicationid,'$Zone',7)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfTerminatedContracts>=$resArray['greyZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',7)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfTerminatedContracts>=$resArray['redZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',7)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',7)";

}
// print $sql;
DatabaseHandler::Execute($sql);

// 8 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=26 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select sum(dlamtexp) from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$CreditInfoId and dlcelcred not in (31)  and concat(cast(dlyear as char(4)),'-',case when dlmonth<10 then concat('0',cast(dlmonth  as char(1))) else cast(dlmonth  as char(2)) end,'-','01')=(select max(concat(cast(dlyear as char(4)),'-',case when dlmonth<10 then concat('0',cast(dlmonth  as char(1))) else cast(dlmonth  as char(2)) end,'-','01')) from deallifeUBKI where crdealid=b.crdealid)";

print $sql;
$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
if ($TotalOutstandingDebtValue=='') $TotalOutstandingDebtValue=0;
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',8)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',8)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',8)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',8)";

}
// print $sql;
DatabaseHandler::Execute($sql);
$Zone='';
// 9 перевірка
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=27 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="SELECT count(*) FROM crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$CreditInfoId  and dlcelcred not in (31)  and `dldayexp`>0 and `dldateclc` between CURDATE() - INTERVAL 12 Month AND SYSDATE()";

$NumberOfUnpaidInstalments=DatabaseHandler::GetOne($sql);
if ($NumberOfUnpaidInstalments=='') $NumberOfUnpaidInstalments=0;
if (($NumberOfUnpaidInstalments>=$resArray['whiteZoneFrom']) and ($NumberOfUnpaidInstalments<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$NumberOfUnpaidInstalments ',$applicationid,'$Zone',9)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfUnpaidInstalments>=$resArray['greyZoneFrom']) and ($NumberOfUnpaidInstalments<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$NumberOfUnpaidInstalments',$applicationid,'$Zone',9)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfUnpaidInstalments>=$resArray['redZoneFrom']) and ($NumberOfUnpaidInstalments<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$NumberOfUnpaidInstalments',$applicationid,'$Zone',9)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfUnpaidInstalments',$applicationid,'$Zone',9)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 10 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=28 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(*) from (select crdealid,max(dlflstat) mx from  crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$CreditInfoId  and dlcelcred= (31) and not exists(select * from deallifeUBKI  where cki_id=$CreditInfoId and dlflstat=2 and crdealid=b.crdealid )   group by crdealid) a";
print $sql;
$NumberOfExistingContracts=DatabaseHandler::GetOne($sql);
if ((is_null($NumberOfExistingContracts)) and ($NumberOfExistingContracts=='')) $NumberOfExistingContracts=0;

if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$NumberOfExistingContracts ',$applicationid,'$Zone',10)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',10)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',10)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',10)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 11 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=29 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select sum(b.dlamtexp) from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$CreditInfoId and dlcelcred=31  and concat(cast(dlyear as char(4)),'-',case when dlmonth<10 then concat('0',cast(dlmonth  as char(1))) else cast(dlmonth  as char(2)) end,'-','01')=(select max(concat(cast(dlyear as char(4)),'-',case when dlmonth<10 then concat('0',cast(dlmonth  as char(1))) else cast(dlmonth  as char(2)) end,'-','01')) from deallifeUBKI where crdealid=b.crdealid)";
print $sql;

$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
print $TotalOutstandingDebtValue;
if ((is_null($TotalOutstandingDebtValue)) or ($TotalOutstandingDebtValue=='')) $TotalOutstandingDebtValue=0;
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',11)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',11)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',11)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',11)";

}
 
DatabaseHandler::Execute($sql);
// 12 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=30 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(b.dlamtpaym) from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$CreditInfoId and dlcelcred=(31)  and concat(cast(dlyear as char(4)),'-',case when dlmonth<10 then concat('0',cast(dlmonth  as char(1))) else cast(dlmonth  as char(2)) end,'-','01')=(select max(concat(cast(dlyear as char(4)),'-',case when dlmonth<10 then concat('0',cast(dlmonth  as char(1))) else cast(dlmonth  as char(2)) end,'-','01')) from deallifeUBKI where crdealid=b.crdealid) and b.dlamtpaym<>0";
$NumberOfTerminatedContracts=DatabaseHandler::GetOne($sql);
if ((is_null($NumberOfTerminatedContracts)) or ($NumberOfTerminatedContracts==''))  $NumberOfTerminatedContracts=0;
if (($NumberOfTerminatedContracts>=$resArray['whiteZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$NumberOfTerminatedContracts ',$applicationid,'$Zone',12)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfTerminatedContracts>=$resArray['greyZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',12)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfTerminatedContracts>=$resArray['redZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',12)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',12)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 14 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=31 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="select count(distinct a.id) from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$CreditInfoId and dlcelcred in (31)  and `dlflstat`=2 ";
print $sql;
$NumberOfTerminatedContracts=DatabaseHandler::GetOne($sql);
if ($NumberOfTerminatedContracts=='') $NumberOfTerminatedContracts=0;
if (($NumberOfTerminatedContracts>=$resArray['whiteZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$NumberOfTerminatedContracts ',$applicationid,'$Zone',13)";
if ($statusCheck==0) {$statusCheck=1;}
}else if (($NumberOfTerminatedContracts>=$resArray['greyZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',13)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($NumberOfTerminatedContracts>=$resArray['redZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',13)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',13)";

}
// print $sql;
DatabaseHandler::Execute($sql);


// 15 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=32 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="SELECT count(*) FROM `deallifeUBKI`  where cki_id=$CreditInfoId  and `dlamtexp`>200 and `dldayexp`between 1 and 29 and  `dldateclc`between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()";
// print $sql;
$CntValue=DatabaseHandler::GetOne($sql);
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',14)";

if ($statusCheck==0) {$statusCheck=1;}
}else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',14)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',14)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$CntValue',$applicationid,'$Zone',14)";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 16 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=33 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="SELECT count(*) FROM `deallifeUBKI`  where cki_id=$CreditInfoId  and `dlamtexp`>200 and `dldayexp`between 30 and 59 and  `dldateclc`between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()";
$CntValue=DatabaseHandler::GetOne($sql);
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',15)";

if ($statusCheck==0) {$statusCheck=1;}
} else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',15)";

if ($statusCheck<2) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',15)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$CntValue',$applicationid,'$Zone',15)";

}
// print $sql;
DatabaseHandler::Execute($sql);

// 17 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=34 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="SELECT count(*) FROM `deallifeUBKI`  where cki_id=$CreditInfoId  and `dlamtexp`>200 and `dldayexp`>59 and  `dldateclc`between CURDATE() - INTERVAL $credit_history Month AND SYSDATE()";
$CntValue=DatabaseHandler::GetOne($sql);
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
	$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',16)";
DatabaseHandler::Execute($sql);
if ($statusCheck==0) {$statusCheck=1;}
}else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',16)";
DatabaseHandler::Execute($sql);
if ($statusCheck<2) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',16)";
DatabaseHandler::Execute($sql);
$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень   {$resArray['Name']}=$CntValue',$applicationid,'$Zone',16)";
DatabaseHandler::Execute($sql);
}
// 18 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=35 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="SELECT sum(b.dlamtexp) from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid WHERE b.cki_id=$CreditInfoId  and (a.dldonor='FIN' or a.dldonor='MFO') and b.dlds between b.dldpf - INTERVAL 180 day and b.dldpf and a.dlamt<=10000
and  dldateclc=(select max(dldateclc) from deallifeUBKI  where crdealid=b.crdealid)
and b.id=(select max(id) from deallifeUBKI  where crdealid=b.crdealid)
";
$CntValue=DatabaseHandler::GetOne($sql);
if ($CntValue=='') $CntValue=0;
if ($CntValue=='0.00') $CntValue=0;
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
	$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',17)";
DatabaseHandler::Execute($sql);
if ($statusCheck==0) {$statusCheck=1;}
}else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',17)";
DatabaseHandler::Execute($sql);
if ($statusCheck<2) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',17)";
DatabaseHandler::Execute($sql);
$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень   {$resArray['Name']}=$CntValue',$applicationid,'$Zone',17)";
DatabaseHandler::Execute($sql);
}
// 19 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=36 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="SELECT count(b.id) from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid WHERE b.cki_id=$CreditInfoId  and (a.dldonor='FIN' or a.dldonor='MFO') and b.dlds between b.dldpf - INTERVAL 180 day and b.dldpf and a.dlamt<=10000 and dlamtexp>0 ";
$CntValue=DatabaseHandler::GetOne($sql);
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
	$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',18)";
DatabaseHandler::Execute($sql);
if ($statusCheck==0) {$statusCheck=1;}
}else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',18)";
DatabaseHandler::Execute($sql);
if ($statusCheck<2) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',18)";
DatabaseHandler::Execute($sql);
$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень   {$resArray['Name']}=$CntValue',$applicationid,'$Zone',18)";
DatabaseHandler::Execute($sql);
}
// 20 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=40 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
$sql="SELECT count(b.id) from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid WHERE b.cki_id=$CreditInfoId  and (a.dldonor='FIN' or a.dldonor='MFO') and b.dlds between b.dldpf - INTERVAL 366 day and b.dldpf and a.dlamt<=10000  and `dlflstat`=2 and b.dldateclc between CURDATE() - INTERVAL $paramclosecreditperiod Day AND SYSDATE()";
$CntValue=DatabaseHandler::GetOne($sql);
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
	$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',20)";
DatabaseHandler::Execute($sql);
if ($statusCheck==0) {$statusCheck=1;}
}else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',20)";
DatabaseHandler::Execute($sql);
if ($statusCheck<2) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',20)";
DatabaseHandler::Execute($sql);
$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень   {$resArray['Name']}=$CntValue',$applicationid,'$Zone',20)";
DatabaseHandler::Execute($sql);
}


// print $sql;
//DatabaseHandler::Execute($sql);
	} else{
		$Zone='';
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Пустий звіт УБКІ',$applicationid,'$Zone',37)";
DatabaseHandler::Execute($sql);
	$ubkiExists=1;
	 $ubkiemptyf=1;
} }else {
	$Zone='';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Пустий звіт УБКІ',$applicationid,'$Zone',37)";
DatabaseHandler::Execute($sql);
$ubkiExists=1;
 $ubkiemptyf=1;
}

}

//пвбкі

	
	
if (($bki['NameBki']=='ПВБКІ') and  ($statusCheck==0)){

$ident=$inn;
$sql="select IFNULL(id,0) from credit_buroPVBKI where StateCode='$ident'";

$CreditInfoId=DatabaseHandler::GetOne($sql); 
$idpvbki=$CreditInfoId;
$pvbkiExists=2;
$varb='';

if ($CreditInfoId==0) 
{
$arr=array(105,49,79,115,101,70,48,98,73,63,124,45,51,69,69,111,74,61,53,54,37,120,61,53,53,88,79,98,61,48,83,101);

foreach($arr as $a){
$varb.=(pack("C*",$a));
	
}


$wsdl = 'https://test.pvbki.com/reverse-service/Default.asmx?WSDL';


try {
$xConverterReport = new SoapClient($wsdl, array('trace' => 1));
$location = $xConverterReport ->__setLocation('https://test.pvbki.com/reverse-service/');

try {
	$headerbody = array('UserName' => $pvbkilogin,
                    'Password' => $pvbkipassword);
					
              $headerbody1 = array('Key'=>$varb,
                      'Name'=>'TestAutoReportInfinance');

//Create Soap Header.       
$ns ='https://service.pvbki.com/reverse';
$headers = array();
$headers[] =  new SOAPHeader($ns, 'AuthenticationCredential', $headerbody);       
$headers[] =  new SOAPHeader($ns, 'AuthenticationIdentity', $headerbody1);          
//set the Headers of Soap Client.

$xConverterReport->__setSoapHeaders($headers); 
try {
$pr=($xConverterReport->Statement(array("forID"=>"$ident")));

}
catch (SoapFault $exception) {
  echo $exception;      
} 


//var_export($pr);
$arr=(array)$pr;
// print_r($arr);
$xml=($arr['Report-StatementResult']);

$html_utf8 = mb_convert_encoding($xml, "utf-8", "windows-1251");


$sql="insert into credit_buroPVBKI(xml,bki,StateCode) values('$html_utf8',3,'$ident')";
$results_array=DatabaseHandler::Execute($sql);
$sql="SELECT LAST_INSERT_ID()";
$idpvbki=DatabaseHandler::GetOne($sql); 

if ($html_utf8<>''){

	file_put_contents("{$ident}_pvbki.xml", $xml );
	$pvbkiExists=2;
$xmls = simplexml_load_string($xml);
//// print_r($xmls);

if (isset($xmls->Subject->requestid)){
$requestid=( $xmls->Subject->requestid);
$lastUpdate=( $xmls->Subject->lastUpdate);
$entity=( $xmls->Subject->entity);
$gender=( $xmls->Subject->gender);
$surnameUA=( $xmls->Subject->surnameUA);
$surnameRU=( $xmls->Subject->surnameRU);
$firstNameUA=( $xmls->Subject->firstNameUA);
$firstNameRU=( $xml->Subject->firstNameRU);
$fathersNameUA=( $xmls->Subject->fathersNameUA);
$fathersNameRU=( $xmls->Subject->fathersNameRU);
$classification=( $xmls->Subject->classification);
$dateOfBirth=( $xmls->Subject->dateOfBirth);
$residency=( $xmls->Subject->residency);
$citizenship=( $xmls->Subject->citizenship);
$negativeStatus=( $xmls->Subject->negativeStatus);
if (isset($xmls->Subject->education)) $education=( $xmls->Subject->education);
else
$education=0;
if (is_null($education)) $education=0;
$sql="insert into subjectPVBKI values(null,$requestid,'$lastUpdate',
'$entity',
'$gender',
'$surnameUA',
'$surnameRU',
'$firstNameUA',
'$firstNameRU',
'$fathersNameUA',
'$fathersNameRU',
$classification,
'$dateOfBirth',
$residency,
'$citizenship',
$negativeStatus,
$education,
$idpvbki
)";
print $sql;
DatabaseHandler::Execute($sql);

$sql="SELECT LAST_INSERT_ID()";
$lastinsertId=DatabaseHandler::GetOne($sql); 
$CreditInfoId=$lastinsertId;
foreach ($xmls->Identification as $item) {
  $typeId=$item->typeId;
  $number= $item->number;
  if (isset($item->issueDate)) $issueDate=$item->issueDate; else $issueDate=NULL;
    if (isset($item->authorityUA)) $authorityUA=$item->authorityUA; else $authorityUA=NULL;
  if (isset($item->authorityRU)) $authorityRU=$item->authorityRU; else $authorityRU=NULL;
  if (isset($item-> registrationDate)) $registrationDate=$item-> registrationDate; else $registrationDate=NULL;
$sql="insert into IdentificationPVBKI values(null,$lastinsertId,$typeId,
'$number',
'$issueDate',
'$authorityUA',
'$authorityRU',
'$registrationDate'
)";

DatabaseHandler::Execute($sql);  
 
}
foreach ($xmls->Address as $item) {
  $typeId=$item->typeId;
  $locationId= $item->locationId;
  if (isset($item->streetRU)) $streetRU=$item->streetRU; else $streetRU=NULL;
  if (isset($item->streetUA)) $streetUA=$item->streetUA; else $streetUA=NULL;
   if (isset($item->postalCode)) $postalCode=$item->postalCode; else $postalCode=NULL;
  
$sql="insert into AddressPVBKI values(null,$lastinsertId,$typeId, $locationId,

'$streetUA',
'$postalCode',
'$streetRU'
)";

DatabaseHandler::Execute($sql);  
  
}
foreach ($xmls->Summary as $item) {
  $category=$item->category;

    if (isset($item->value)) $value= $item->value; else $value=0;
  if (isset($item->count)) $count=$item->count; else $count=0;
  if (isset($item->code)) $code=$item->code; else $code=NULL;
    if (isset($item-> amount)) $amount=$item->amount; else $amount=0;
$sql="insert into SummaryPVBKI values(null,$lastinsertId,'$category', $value,

$count,
'$code',
'$amount'
)";

DatabaseHandler::Execute($sql);  

  
}

foreach ($xmls->Contract as $item) {
	
  $roleId=$item->roleId;
  $provider=$item->provider;
  $contractid=$item->contractid;
  $lastUpdate=$item->lastUpdate;
  $phaseId=$item->phaseId;
  $currency=$item->currency;
  $dateOfSignature=$item->dateOfSignature;
  $negativeStatus=$item->negativeStatus;
  $applicationDate=$item->applicationDate;
  $startDate=$item->startDate;
  $expectedEndDate=$item->expectedEndDate;
  
  if (isset($item->factualEndDate)) $factualEndDate=$item->factualEndDate; else $factualEndDate=NULL;
  $type=$item->type;
  if (isset($item->paymentPeriodId)) $paymentPeriodId=$item->paymentPeriodId; else $paymentPeriodId=0;
  $actualCurrency=$item->actualCurrency;
  if (isset($item->totalAmount)) $totalAmount=$item->totalAmount; else $totalAmount=0;
  $instalmentCount=$item->instalmentCount;
  $instalmentAmountCurrency=$item->instalmentAmountCurrency;
    if (isset($item-> instalmentAmount)) $instalmentAmount=$item-> instalmentAmount; else $instalmentAmount=0;
	  $restInstalmentCount=$item->restInstalmentCount;
	    $restAmount=$item->restAmount;
		  if (isset($item->overdueCount)) $overdueCount=$item->overdueCount; else $overdueCount=0;
		  		if (isset($item->overdueAmount))  $overdueAmount=$item->overdueAmount; else $overdueAmount=0;
  if (isset($item->creditPurpose))  $creditPurpose=$item->creditPurpose; else $creditPurpose=0;
  if (isset($item->creditLimit))  $creditLimit=$item->creditLimit; else $creditLimit=0;
  
$sql="insert into ContractPVBKI values(null,$lastinsertId,$roleId,'$provider',
 $contractid,
'$lastUpdate',
$phaseId,
'$currency',
'$dateOfSignature',
$negativeStatus,
'$applicationDate',
'$startDate',
'$expectedEndDate',
'$factualEndDate',
'$type',
$paymentPeriodId,
'$actualCurrency',
$totalAmount,
$instalmentCount,
'$instalmentAmountCurrency',
$instalmentAmount,
$restInstalmentCount,
$restAmount,
$overdueCount,
$overdueAmount,
$creditPurpose,
$creditLimit
)";

DatabaseHandler::Execute($sql);  


  
}
foreach ($xmls->Collateral as $item) {
	 if (isset($item->contractid)) $contractid=$item->contractid; else $contractid=0;
	 if (isset($item->typeId)) $typeId=$item->typeId; else $typeId=0;
	 if (isset($item->value)) $value=$item->value; else $value=0;
	 $currency=$item->currency;
 $identificationtypeId=0;
	 $number=$item->number;
	$sql="insert into CollaterialPVBKI values(null,$lastinsertId,$contractid, $typeId,

$value,
'$currency',
$identificationtypeId,
'$number'
)";

DatabaseHandler::Execute($sql);  
	 
}
foreach ($xmls->Record as $item) {
	 $contractid=$item->contractid;
	 $accountingDate=$item->accountingDate;
	 if (isset($item->restAmount)) $restAmount=$item->restAmount; else $restAmount=0;
	 $restCurrency=$item->restCurrency;
	 if (isset($item->restInstalmentCount)) $restInstalmentCount=$item->restInstalmentCount; else $restInstalmentCount=0;
	 if (isset($item->overdueAmount)) $overdueAmount=$item->overdueAmount; else $overdueAmount=0;
	 $overdueCurrency=$item->overdueCurrency;
		 if (isset($item->overdueCount)) $overdueCount=$item->overdueCount;else$overdueCount=0;
	$sql="insert into recordPVBKI values(null,$lastinsertId,$contractid, '$accountingDate',

$restAmount,
' $restCurrency',
$restInstalmentCount,
$overdueAmount,
'$overdueCurrency',
$overdueCount
)";

DatabaseHandler::Execute($sql);  
	 
}

}
} 

}catch (SoapFault $e) {
var_dump($e);
}
}catch (SoapFault $e) {
var_dump($e);
}

}
	
	DatabaseHandler::Execute("set names utf8");
	// 1 перевірка


if ($idpvbki<>0) {
	$sql="select id from subjectPVBKI where idpvbki=$idpvbki";
	$CreditInfoId=DatabaseHandler::GetOne($sql);
	if ($CreditInfoId<>0){
	$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=1 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="SELECT ifnull(negativeStatus,0) FROM ContractPVBKI WHERE subject_id=$CreditInfoId";
$Zone='';
$NumberOfExistingContracts=DatabaseHandler::GetOne($sql);

if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  WhiteZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',1)";

}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  GreyZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',1)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ redZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',1)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',1)";

}

DatabaseHandler::Execute($sql);
	}
	/* пропускаэмо  перевырки 2 і 3
	// 2 перевірка
$sql="select whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=2 and status=1";
$resArray=DatabaseHandler::GetOne($sql);
$sql="select count(*) FROM `contUBKI` where cki_id=$CreditInfoId and cval in ('$mobile_phone','$home_phone','$home_phone_residence','$work_phone')";
// print $sql;
$countPhone=DatabaseHandler::GetOne($sql);
if (($countPhone>=$resArray['whiteZoneFrom']) and ($countPhone<=$resArray['whiteZoneTo'])){
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ Телефони WhiteZone є співпадіння',$applicationid,'$Zone')";

}else if (($countPhone>=$resArray['greyZoneFrom']) and ($countPhone<=$resArray['greyZoneTo'])){
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ Телефони GreyZone є співпадіння',$applicationid,'$Zone')";

$statusCheck=2;
}
else if (($countPhone>=$resArray['redZoneFrom']) and ($countPhone<=$resArray['redZoneTo'])){
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ Телефони RedZone є співпадіння',$applicationid,'$Zone')";

$statusCheck=3;

} else {
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ Телефони Значення не входить в матрицю рішень Кількість Договорів=$NumberOfExistingContracts',$applicationid,'$Zone')";

}
// print $sql;
DatabaseHandler::Execute($sql);
// 3 перевірка
$sql="select whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=3 and status=1";
$resArray=DatabaseHandler::GetOne($sql);
$sql="select count(*) from `contUBKI` where cki_id=$CreditInfoId and сval in ('$email')";
$countEmail=DatabaseHandler::GetOne($sql);
if (($countEmail>=$resArray['whiteZoneFrom']) and ($countEmail<=$resArray['whiteZoneTo'])){
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ Телефони WhiteZone є співпадіння',$applicationid,'$Zone')";

}else if (($countEmail>=$resArray['greyZoneFrom']) and ($countEmail<=$resArray['greyZoneTo'])){
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ Телефони GreyZone є співпадіння',$applicationid,'$Zone')";

$statusCheck=2;
}
else if (($countEmail>=$resArray['redZoneFrom']) and ($countEmail<=$resArray['redZoneTo'])){
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ Телефони RedZone є співпадіння',$applicationid,'$Zone')";

$statusCheck=3;

} else {
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ Телефони Значення не входить в матрицю рішень Кількість Договорів=$NumberOfExistingContracts',$applicationid,'$Zone')";

}
// print $sql;
DatabaseHandler::Execute($sql);*/
// 4 перевірка

$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=4 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="select sum(ifnull(count,0)) as kilkdogovoriv from SummaryPVBKI where  subject_id=$CreditInfoId and category='openAmount'";
$Zone='';
$NumberOfExistingContracts=DatabaseHandler::GetAll($sql)[0]['kilkdogovoriv'];
if (is_null($NumberOfExistingContracts)){$NumberOfExistingContracts=0;}

if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$NumberOfExistingContracts ',$applicationid,'$Zone',4)";

}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',4)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',4)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',4)";

}

DatabaseHandler::Execute($sql);
}
// 5 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=5 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="SELECT sum(amount) FROM SummaryPVBKI where subject_id=$CreditInfoId and category='overdueAmount' and code='UAH'";
$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
if (is_null($TotalOutstandingDebtValue)) {$TotalOutstandingDebtValue=0;}
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',5)";

}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',5)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',5)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',5)";

}
DatabaseHandler::Execute($sql);
}
// 6 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=6 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="select count  from SummaryPVBKI where subject_id=$CreditInfoId and category='type'  and code='instalmentAmount'";
$NumberOfExistingContracts=DatabaseHandler::GetOne($sql);
if (is_null($NumberOfExistingContracts)) {$NumberOfExistingContracts=0;}

if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$NumberOfExistingContracts ',$applicationid,'$Zone',6)";

}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',6)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',6)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',6)";

}

DatabaseHandler::Execute($sql);
}
// 7 перевірка
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=7 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {

$sql="select count from SummaryPVBKI where subject_id=$CreditInfoId and  category='type'  and code='instalment'";
$NumberOfTerminatedContracts1=DatabaseHandler::GetOne($sql);

if (is_null($NumberOfTerminatedContracts1)) {$NumberOfTerminatedContracts1=0;}
$Zone='';
$NumberOfTerminatedContracts=$NumberOfTerminatedContracts1-$NumberOfExistingContracts;
if (($NumberOfTerminatedContracts>=$resArray['whiteZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$NumberOfTerminatedContracts ',$applicationid,'$Zone',7)";

}else if (($NumberOfTerminatedContracts>=$resArray['greyZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',7)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($NumberOfTerminatedContracts>=$resArray['redZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',7)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',7)";

}

DatabaseHandler::Execute($sql);
}
// 8 перевірка
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=8 and status=1";
$Zone='';
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="SELECT sum(amount) FROM `SummaryPVBKI` where subject_id=$CreditInfoId and  category='overdueAmount'  and code='UAH' ";
$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',8)";

}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',8)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',8)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',8)";

}

DatabaseHandler::Execute($sql);
}
// 9 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=9 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="SELECT overduecount FROM ContractPVBKI where subject_id=$CreditInfoId and type='Instalment' ";
$NumberOfUnpaidInstalments=DatabaseHandler::GetOne($sql);
if (($NumberOfUnpaidInstalments>=$resArray['whiteZoneFrom']) and ($NumberOfUnpaidInstalments<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$NumberOfUnpaidInstalments ',$applicationid,'$Zone',9)";

}else if (($NumberOfUnpaidInstalments>=$resArray['greyZoneFrom']) and ($NumberOfUnpaidInstalments<=$resArray['greyZoneTo'])){
$Zone='GreyZone';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$NumberOfUnpaidInstalments',$applicationid,'$Zone',9)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($NumberOfUnpaidInstalments>=$resArray['redZoneFrom']) and ($NumberOfUnpaidInstalments<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$NumberOfUnpaidInstalments',$applicationid,'$Zone',9)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfUnpaidInstalments',$applicationid,'$Zone',9)";

}

DatabaseHandler::Execute($sql);
}
// 10 перевірка

$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=10 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="select count  from SummaryPVBKI where subject_id=$CreditInfoId and category='type'  and code='nonInstalment'";
$Zone='';
$NumberOfExistingContracts=DatabaseHandler::GetOne($sql);
if (is_null($NumberOfExistingContracts)) {$NumberOfExistingContracts=0;}

if (($NumberOfExistingContracts>=$resArray['whiteZoneFrom']) and ($NumberOfExistingContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$NumberOfExistingContracts ',$applicationid,'$Zone',10)";

}else if (($NumberOfExistingContracts>=$resArray['greyZoneFrom']) and ($NumberOfExistingContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',10)";


if ($statusCheck==0) {$statusCheck=2;}
}
else if (($NumberOfExistingContracts>=$resArray['redZoneFrom']) and ($NumberOfExistingContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',10)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfExistingContracts',$applicationid,'$Zone',10)";

}

DatabaseHandler::Execute($sql);
}
// 11 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=11 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="select sum(overdueamount) FROM ContractPVBKI where subject_id=$CreditInfoId and type='nonInstalment' ";
$TotalOutstandingDebtValue=DatabaseHandler::GetOne($sql);
if (is_null($TotalOutstandingDebtValue)) {$TotalOutstandingDebtValue=0;}
if (($TotalOutstandingDebtValue>=$resArray['whiteZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$TotalOutstandingDebtValue ',$applicationid,'$Zone',11)";

}else if (($TotalOutstandingDebtValue>=$resArray['greyZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',11)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($TotalOutstandingDebtValue>=$resArray['redZoneFrom']) and ($TotalOutstandingDebtValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',11)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$TotalOutstandingDebtValue',$applicationid,'$Zone',11)";

}

DatabaseHandler::Execute($sql);
}
// 12 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=12 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="select count(overduecount) FROM ContractPVBKI where subject_id=$CreditInfoId and type='nonInstalment'  and overduecount>0";
$NumberOfTerminatedContracts=DatabaseHandler::GetOne($sql);
if (is_null($NumberOfTerminatedContracts)) {$NumberOfTerminatedContracts=0;}
if (($NumberOfTerminatedContracts>=$resArray['whiteZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$NumberOfTerminatedContracts ',$applicationid,'$Zone',12)";

}else if (($NumberOfTerminatedContracts>=$resArray['greyZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',12)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($NumberOfTerminatedContracts>=$resArray['redZoneFrom']) and ($NumberOfTerminatedContracts<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',12)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$NumberOfTerminatedContracts',$applicationid,'$Zone',12)";

}

DatabaseHandler::Execute($sql);
}
// 14 перевірка


// 15 перевірка

$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=14 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="select count(overdueCount) FROM recordPVBKI where subject_id=$CreditInfoId  and overdueCount=1";
$Zone='';
$CntValue=DatabaseHandler::GetOne($sql);
if (is_null($CntValue)) {$CntValue=0;}
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',14)";


}else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',14)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',14)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$CntValue',$applicationid,'$Zone',14)";

}

DatabaseHandler::Execute($sql);
}
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=15 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="select count(overdueCount) FROM recordPVBKI where subject_id=$CreditInfoId  and overdueCount=2";
$CntValue=DatabaseHandler::GetOne($sql);
if (is_null($CntValue)) {$CntValue=0;}
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',15)";


} else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',15)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',15)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка УБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$CntValue',$applicationid,'$Zone',15)";

}

DatabaseHandler::Execute($sql);
}
// 17 перевірка
$Zone='';
$sql="select Name,whiteZoneFrom,whiteZoneTo,greyZoneFrom,greyZoneTo,redZoneFrom,redZoneTo from estalginfo where id=16 and status=1";
$resArray=DatabaseHandler::GetAll($sql)[0];
if (count($resArray)>0) {
$sql="select count(overdueCount) FROM recordPVBKI where subject_id=$CreditInfoId  and overdueCount>2 ";
$CntValue=DatabaseHandler::GetOne($sql);
if (is_null($CntValue)) {$CntValue=0;}
if (($CntValue>=$resArray['whiteZoneFrom']) and ($CntValue<=$resArray['whiteZoneTo'])){
$Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ WhiteZone {$resArray['Name']}=$CntValue ',$applicationid,'$Zone',16)";


}else if (($CntValue>=$resArray['greyZoneFrom']) and ($CntValue<=$resArray['greyZoneTo'])){
$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ GreyZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',16)";

if ($statusCheck==0) {$statusCheck=2;}
}
else if (($CntValue>=$resArray['redZoneFrom']) and ($CntValue<=$resArray['redZoneTo'])){
$Zone='RedZone';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ RedZone {$resArray['Name']}=$CntValue',$applicationid,'$Zone',16)";

$statusCheck=3;

} else {
	$Zone='';
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка ПВБКІ  Значення не входить в матрицю рішень {$resArray['Name']}=$CntValue',$applicationid,'$Zone',16)";

}

DatabaseHandler::Execute($sql);
}
	} else {
		$Zone='';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Пустий звіт  ПВБКІ ',$applicationid,'$Zone',1000)";
DatabaseHandler::Execute($sql);
$pvbkiExists=1;
	}
} else {
	$Zone='';

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Пустий звіт  ПВБКІ ',$applicationid,'$Zone',1000)";
DatabaseHandler::Execute($sql);
$pvbkiExists=1;

	

}

}
}
if ($statusCheck==1) { $Zone='WhiteZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка БКІ Рішення=$statusCheck',$applicationid,'$Zone',1300)";

DatabaseHandler::Execute($sql);
$sql="update Applications set decisionAfterBKI=$statusCheck where creditid=$creditid";
//print $sql;
  DatabaseHandler::Execute($sql);


}
if ($statusCheck==3) {$Zone='RedZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка БКІ Рішення=$statusCheck',$applicationid,'$Zone',1300)";

DatabaseHandler::Execute($sql);
$sql="update Applications set decisionAfterBKI=$statusCheck where creditid=$creditid";
//print $sql;
  DatabaseHandler::Execute($sql);


}
if (($statusCheck==0) or ($statusCheck==2)) {$Zone='GreyZone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка БКІ Рішення=$statusCheck',$applicationid,'$Zone',1300)";

DatabaseHandler::Execute($sql);
$sql="update Applications set decisionAfterBKI=$statusCheck where creditid=$creditid";
//print $sql;
  DatabaseHandler::Execute($sql);


}

 }
 $sql="select max(id) from ckiUBKI where inn='$inn'";
 $maxubki=DatabaseHandler::GetOne($sql);
 $sql="select max(id) from subjectMBKI where TaxpayerNumber='$inn'";
 $maxmbki=DatabaseHandler::GetOne($sql);
  if (is_null($maxmbki)) {$maxmbki=0;}
  if (is_null($maxubki)) {$maxubki=0;}
 if ( $mbkiemptyf==1) $maxmbki=0;
 if ( $ubkiemptyf==1) $maxubki=0;
 
 print "<br />";
 print $maxubki;
print $maxmbki; 
print "<br />";
$zone_card_arr = [
        '1' => 'WhiteZone',
        '2' => 'GreyZone',
		'3'=>'RedZone',
		'4'=>'WhiteZone/GreyZone',
		'5'=>'WhiteZone/RedZone',
		'6'=>'GreyZone/RedZone',
		'7'=>'WhiteZone/GreyZone/RedZone'
      
    ];
	$itemsqd = [
        '1' => 'МБКІ',
        '2' => 'УБКІ',
		'3'=>'МБКІ/УБКІ'
      
    ];
	$sql="select Name from estalginfo where status=1 and kind=1 and Name not in ('Пустий звіт','МР 1.2')";
$estalginfo_card=DatabaseHandler::GetAll($sql);

$sql="SELECT * FROM setofparameters a  WHERE   typeApplication=$typeSc and (parameterFromScor is null and parameterToScor is null)";

	$nMatrix=DatabaseHandler::GetAll($sql);
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Початок перевірки по картках',$applicationid,'',25500)";
print $sql;
 DatabaseHandler::Execute($sql);
$carddecisionname='';
print "<br />";
print_r($nMatrix);
print "<br />";
$decisionCard=0;

if ($statusInternalRates!=5)
{
if (count($nMatrix)>0)
{
 
for($r = 0; $r < count($nMatrix); ++$r) {

	$id_card=$nMatrix[$r]['id'];
	$name_card=$nMatrix[$r]['name'];
	$typeApplication_card=$nMatrix[$r]['typeApplication'];
	$limit_card=$nMatrix[$r]['limit'];
	$decision_card=$nMatrix[$r]['decision'];
		$bki=$nMatrix[$r]['bki'];
	$parameterFromScor=$nMatrix[$r]['parameterFromScor'];
$parameterToScor=$nMatrix[$r]['parameterToScor'];
	
	$pieces = explode('/', $itemsqd[$bki]);
	if (count($pieces)==0) {$pieces[0]=$bki;}
	print "<br />";
	print_r(count($pieces));
	print "<br />";
	$stopcheck=0;
	for($pi = 0; $pi < count($pieces); ++$pi)
	{
		
	if ($bki<>3){
$sql="SELECT * FROM conditioncard  WHERE  setofparameterid=$id_card  order by id ";
	} else {
$sql="SELECT * FROM conditioncard  WHERE  setofparameterid=$id_card and ((bki=$pi+1) or (bki is null)) order by id ";
		
	}
	$nMatrix_card=DatabaseHandler::GetAll($sql);
print_r($nMatrix_card);	
if (count($nMatrix)>0)
{
	for($z = 0; $z < count($nMatrix_card); ++$z) {
	print "<br />";
	 print_r($nMatrix_card[$z]);
	print "<br />";
	
	if ($stopcheck==0)
	{
	$matrixdecisionid_cardone=$nMatrix_card[$z]['matrixdecisionid'];
	$zone_cardzone=$nMatrix_card[$z]['zone'];
	$val_cardzone=$nMatrix_card[$z]['value'];
	$bki_cardone=$nMatrix_card[$z]['bki'];
if (($matrixdecisionid_cardone<>37) and ($matrixdecisionid_cardone<>38)){
$sql="select 1 from historyLog where applicationid=$applicationid and algId=1 and Text like '%{$pieces[$pi]}%' ";
	print $sql;
$checkBkicard=DatabaseHandler::GetOne($sql);
	} else {$checkBkicard=1;}
	if ($checkBkicard<>1) {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card не здійсненна Не проходила по БКІ заявка ОFF',$applicationid,'',25900)";
print $sql;
DatabaseHandler::Execute($sql);
 		$stopcheck=1;
	} else {
		print "<br />";
		print "Cardone=";
		print $matrixdecisionid_cardone;
		print "<br />";
		
	if (($matrixdecisionid_cardone<>37) and ($matrixdecisionid_cardone<>38)) {
	$sql="select Zone from historyLog where applicationid=$applicationid and algId=$matrixdecisionid_cardone and Text like '%{$pieces[$pi]}%' ";
	print $sql;
	$ZoneDecision=DatabaseHandler::GetOne($sql);
if (($matrixdecisionid_cardone==39) or ($matrixdecisionid_cardone==40)) $matrixdecisionid_cardone=20; 
		$sql="select substring(Text, LOCATE('=',Text)+1,length(Text)-LOCATE('=',Text)-1) Value from historyLog where applicationid=$applicationid and algId=$matrixdecisionid_cardone and Text like '%{$pieces[$pi]}%' ";
	print $sql;
	$ValueDecision=DatabaseHandler::GetOne($sql);
	
	print "<br />";
	print $ZoneDecision;
	print "<br />";
	print $val_cardzone;
	print "<br />";
	print $matrixdecisionid_cardone;
	print "<br />";
	print_r($estalginfo_card);
	print "<br />";
	
	print $estalginfo_card[$matrixdecisionid_cardone-1]['Name'];
	print "<br />";
	if (($val_cardzone=='')){
	if ($ZoneDecision<>''){
	
	$pos=strpos( $zone_card_arr[$zone_cardzone],$ZoneDecision);
	
	print $zone_card_arr[$zone_cardzone];
	print "<br />";
	if ($pos===false)
	{
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card Умова={$estalginfo_card[$matrixdecisionid_cardone-1]['Name']} Зона по картці=$zone_card_arr[$zone_cardzone] Зона по бкі=$ZoneDecision bki=$pieces[$pi]',$applicationid,'',25600)";
print $sql;
 DatabaseHandler::Execute($sql);
    
	$stopcheck=1;
	
	} else {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card Умова=$matrixdecisionid_cardone ОК',$applicationid,'',25600)";
print $sql;
 //DatabaseHandler::Execute($sql);
		
	}
	} else if ($matrixdecisionid_cardone==17){$stopcheck=1;}
	} else  if ($zone_cardzone=='') {
		unset($fromVal);
unset($toVal);
 if (strpos($val_cardzone, "-") !== false) {
   
 
list($fromVal, $toVal) = preg_split('/[-]/', $val_cardzone);
 }
if (is_null($fromVal)) {$fromVal=$val_cardzone;$toVal=$val_cardzone;}
	print $fromVal;
	print "<br />";
	print $toVal;
	print "<br />";
	print $ValueDecision;
	print "<br />";
	if (((double)$ValueDecision>=(double)$fromVal) && ((double)$ValueDecision<=(double)$toVal)){
	$pos=true;
	} else {
		$pos=false;
	}
	//print $zone_card_arr[$zone_cardzone];
	print "<br />";
	if ($pos===false)
	{
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card Умова={$estalginfo_card[$matrixdecisionid_cardone-1]['Name']} Значення по картці=$val_cardzone Значення по бкі=$ValueDecision bki=$pieces[$pi]',$applicationid,'',25600)";
print $sql;
 DatabaseHandler::Execute($sql);
    
	$stopcheck=1;
	
	} else {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card Умова=$matrixdecisionid_cardone ОК',$applicationid,'',25600)";
print $sql;
 //DatabaseHandler::Execute($sql);
		
	} 
	}	else if ($matrixdecisionid_cardone==17){$stopcheck=1;}
	
	
	} else if ($matrixdecisionid_cardone==37) {
	
	$sql="select 1 from historyLog where applicationid=$applicationid and algId=37 and Text like '%Пустий звіт {$pieces[$pi]}%'";
	print $sql;
	$ZoneDecision=DatabaseHandler::GetOne($sql);
		print "<br />";
		print "ZoneDecision=".$ZoneDecision;
		print "<br />";
	if ($ZoneDecision==''){
	
	$stopcheck=1;
	}
	} else {
	
	$sql="select Zone from historyLog where applicationid=$applicationid and algId=300 and Text like '%Перевірка MatrixDecision1.2 Назва контролю=Вік%'";
	print $sql;
	$ZoneDecision=DatabaseHandler::GetOne($sql);

	print "<br />";
	print $ZoneDecision;
	print "<br />";
	print "<br />";
	print $ZoneMatrix;
	print "<br />";
	
	if ($ZoneDecision==''){
	$sql="select Zone from historyLog where applicationid=$applicationid and algId=300 and Text like '%Перевірка MatrixDecision1.2 Рішення=0%'";
	print $sql;
	$ZoneDecision=DatabaseHandler::GetOne($sql);
	
	if ($ZoneDecision=='БКІ') $ZoneDecision='WhiteZone';
	}
	$pos=strpos( $zone_card_arr[$zone_cardzone],$ZoneDecision);
	
	print $zone_card_arr[$zone_cardzone];
	print "<br />";
	if ($pos===false){
	
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card Умова={$estalginfo_card[$matrixdecisionid_cardone-1]['Name']} Зона по картці=$zone_card_arr[$zone_cardzone] Зона по бкі=$ZoneDecision bki=$pieces[$pi]',$applicationid,'',25600)";
print $sql;
 DatabaseHandler::Execute($sql);
    
	$stopcheck=1;
	
	
	} 
	}
	
	
	}
	
}
}	
	}
	
}
if ($stopcheck==0)
{
	if ($decisionCard<$decision_card) {$decisionCard=$decision_card;}
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Результат перевірки по картці $name_card ОК',$applicationid,'WhiteZone',25700)";
print $sql;
DatabaseHandler::Execute($sql);
	$carddecisionname=$name_card;
	
if(	$limit_card>0) {
	if (($ammout_of_credit>$limit_card) and ($decision_card==1)) {
$sql="update CreditsClientsForCheckBKI  set  ammout_of_credit=$limit_card where id=$creditid";
print $sql;
DatabaseHandler::Execute($sql);
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Зменшення суми кредиту=Сума ліміту Початкова сума=$ammout_of_credit Змінена=$limit_card',$applicationid,'',25700)";
print $sql;
DatabaseHandler::Execute($sql);

$ammout_of_credit=$limit_card;
$statusCheck=1;

} 
} 
}
else {

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Результат перевірки по картці $name_card OFF',$applicationid,'GreyZone',25700)";
print $sql;
DatabaseHandler::Execute($sql);
	
}
}
if ($decisionCard==0) {
	if ($ZoneMatrix==1) {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Завершення перевірки по картках Фінальне рішення=Manual',$applicationid,'',25500)";
$statusCheck=2;

	}  else {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Завершення перевірки по картках Фінальне рішення=Не визначено',$applicationid,'',25500)";
	}
	
}
if ($decisionCard==1) {
	if ($ZoneMatrix==1) {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Завершення перевірки по картках Фінальне рішення=Manual',$applicationid,'',25500)";
$statusCheck=2;

	} else {

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Завершення перевірки по картках Фінальне рішення=Approve',$applicationid,'',25500)";
$statusCheck=1;
}
DatabaseHandler::Execute($sql);
$sql="update Applications set carddecision='$carddecisionname' where creditid=$creditid";
	DatabaseHandler::Execute($sql);

}
if ($decisionCard==2) {
	$statusCheck=3;
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Завершення перевірки по картках Фінальне рішення=Reject',$applicationid,'',25500)";
$sql="update Applications set carddecision='$carddecisionname' where creditid=$creditid";
	DatabaseHandler::Execute($sql);

}

print $sql;
DatabaseHandler::Execute($sql);

}
}
print "скоринг";
//скорингова карта
$scoringcardname='';
$scoreScor=0;
$solutScor='';
 if  ($statusAvto==0) {
	 print "starts";
	 print "<br />";
	 print $decisionCard;	
	 print "<br />";
	 if (($site_source=='http://pozichka.com/') or ($site_source=='http://private.pozichka.ua/') or ($site_source=='https://private.pozichka.ua/') or ($site_source=='https://pozichka.ua/')) {$site_source='http://pozichka.ua';}

 $sql="select a.id scoringcardid,a.share,a.name scoringcardname from scoringcards a  where a.date_end>CURDATE() and source='$site_source' and `type`=$typeSc";
print "<br />";
 print $sql;
 $results=DatabaseHandler::GetAll($sql); 
print "<br />";
for($i = 0; $i < count($results); ++$i) {
 $scoreScor=0;
 $statusCheckScor=0;
 $scoringcardid=$results[$i]['scoringcardid'];
    $scoringcardname=$results[$i]['scoringcardname'];
	$share=$results[$i]['share'];
	$share=100-$share;
	$share=substr($share, 0, 1);
	$a=intval(substr($creditid, -1));
	$b=intval($share);
	if ($b==0) {$b=1;}
	print "Скоринг3=";
	print "<br />";
print $a;
	print "<br />";
print $b;
	print "<br />";
	print ($a%$b);

		$share=['0','1','2','3','4','5','6','7','8','9'];
	if ($a % $b==0){
	
$sql="select b.id id,c.id idparameter,c.name parameter from  ascoringcard b  join detalniparametruscoringcard c on b.parameterscoringcard_id=c.id 

	where  b.scoringcard_id=$scoringcardid  order by c.id ";
	
	
print $sql;
 	$resultsd=DatabaseHandler::GetAll($sql);
for($j = 0; $j < count($resultsd); ++$j) {
	 $id=$resultsd[$j]['id'];
	 $idparameter=$resultsd[$j]['idparameter'];
	  $parameter=$resultsd[$j]['parameter'];
$sql="select d.conditions conditions,d.score score from   bscoringcard d 
where  ascoringcard_id=$id and conditions<>'NULL'";
 	$resultsdd=DatabaseHandler::GetAll($sql);
	print_r($resultsdd);
for($k = 0; $k < count($resultsdd); ++$k) {
	$conditions=$resultsdd[$k]['conditions'];
	 $score=$resultsdd[$k]['score'];
	print "<br />";
		 print "param=";
	 print $idparameter;
	 print "<br />";
	  print "decisionAfterScorFirst=";
	 print $decisionAfterScorFirst;
	 	 print "<br />";
	  print "decisionAfterScorSecond=";	 
	 print $decisionAfterScorSecond;
	 	 print "<br />";
	if ($idparameter==1)
	{
		
		$sql="select count(*) from CreditsClientsForCheckBKI where id=$creditid and ammout_of_credit {$conditions}";
print "<br />";
	 print $sql;
	 print "<br />";
		$nRowsIn=DatabaseHandler::GetOne($sql); 
		print "<br />";
	 print $nRowsIn;
	 print "<br />";
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}			
			
	//	DatabaseHandler::Execute($sql);

	}
	print "<br />";
	 print $nRowsIn;
	 print "<br />";
if ($idparameter==2)
	{
		$nRowsIn=-1;
if (is_null($maxubki)) $maxubki=0;
if (is_null($maxmbki)) $maxmbki=0;
	if  (($decisionAfterScorFirst!=2) and ($decisionAfterScorSecond!=2))	{
$sql="select count(a.cnt) from ( SELECT b.dlref as cnt from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid WHERE b.cki_id=$maxubki and (a.dldonor='FIN' or a.dldonor='MFO') and b.dlds between b.dldpf - INTERVAL 180 day and b.dldpf and a.dlamt<=10000
union
SELECT CodeOfContract FROM `contractsMBKI` WHERE `PereodicityOfPayments`='В останній день строку дії договору' and `CreditorType`='Фінансова компанія' and `TotalAmountValue`<=20000 and `SubjectId`=$maxmbki) a 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}
	if  (($decisionAfterScorFirst!=2) and ($decisionAfterScorSecond==2))	{
$sql="
SELECT count(CodeOfContract) FROM `contractsMBKI` WHERE `PereodicityOfPayments`='В останній день строку дії договору' and `CreditorType`='Фінансова компанія' and `TotalAmountValue`<=20000 and `SubjectId`=$maxmbki
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}
	if  (($decisionAfterScorFirst==2) and ($decisionAfterScorSecond!=2))	{
$sql="
 SELECT count(b.dlref)  from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid WHERE b.cki_id=$maxubki and (a.dldonor='FIN' or a.dldonor='MFO') and b.dlds between b.dldpf - INTERVAL 180 day and b.dldpf and a.dlamt<=10000
 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}

	if (is_null($nRowsIn) or ($nRowsIn==''))  $nRowsIn=0; 
$os=array('0','1');
if (in_array(substr($conditions, 0, 1), $os)) {
	if ($nRowsIn==$conditions){

		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\"  параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print $sql;
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);

	}
} else {
/*$sql="select count(a.cnt) from ( SELECT b.dlref as cnt from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid WHERE b.cki_id=$maxubki and a.dldonor='FIN' and b.dlds between b.dldpf - INTERVAL 180 day and b.dldpf and a.dlamt<=10000
union
SELECT CodeOfContract FROM `contractsMBKI` WHERE `PereodicityOfPayments`='В останній день строку дії договору' and `Creditor`='B-2' and `TotalAmountValue`<=20000 and `SubjectId`=$maxmbki) a 
";	
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql);*/ 
	if ($nRowsIn>1){

		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\"  параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print $sql;
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);

	}
}
print "<br />";
print $sql;
print "<br />";
print $nRowsIn;
print "<br />";
	
	
			
	//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);	
}
if ($idparameter==3)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and work_experience {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print $sql;
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==4)
	{
		if ($conditions=='="2_Так"') {
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and (property{$conditions} or movable_property{$conditions} )";
		} else {
	$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and (property{$conditions} and movable_property{$conditions} )";
			
		}
		print "<br />";
print $sql;
print $conditions;
print "<br />";
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
		//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
		//DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==5)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and current_work_first{$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
		//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
		//DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==6)
	{
		if (is_null($maxubki)) $maxubki=0;
if (is_null($maxmbki)) $maxmbki=0;
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and (cancelled_loans<>'1_Відсутні' and cancelled_loans<>'0_Виберіть відповідь з переліку')";
		print "<br />";
print $sql;
print "<br />";
	$nRowsIn=DatabaseHandler::GetOne($sql);
	print "<br />";
	print $nRowsIn;
	print "<br />";
	$сloseContracts=-1;
	if (is_null($nRowsIn) or ($nRowsIn==''))  $nRowsIn=0; 
	// убкі
	if  (($decisionAfterScorFirst!=2) and ($decisionAfterScorSecond!=2))	{
$sql="select count(*) from (select b.dlref as cnt from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$maxubki  and `dlflstat`=2
union
select CodeOfContract  FROM `contractsMBKI` where subjectid=$maxmbki and ContractType='Terminated') a
 ";
 print $sql;
$сloseContracts=DatabaseHandler::GetOne($sql);
	}
	if  (($decisionAfterScorFirst!=2) and ($decisionAfterScorSecond==2))	{
$sql="
select count(CodeOfContract)  FROM `contractsMBKI` where subjectid=$maxmbki and ContractType='Terminated'
";
print $sql;
$сloseContracts=DatabaseHandler::GetOne($sql);
	}
	if  (($decisionAfterScorFirst==2) and ($decisionAfterScorSecond==1))	{
$sql="
 select count(b.dlref) from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$maxubki  and `dlflstat`=2
 
";
print $sql;
$сloseContracts=DatabaseHandler::GetOne($sql);
	}


if(is_null($closeContracts)) $closeContracts=0;
print $сloseContracts;
if ($conditions=='1 and 1'){
if (($nRowsIn>0) and ($сloseContracts>0 )){
		$scoreScor=$scoreScor+$score;
		
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		print "<br />";
		 print "sd";
	 print $sql;
	 print "<br />";
	 $sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='0 and 1'){
if (($nRowsIn==0) and ($сloseContracts>0 )){
		$scoreScor=$scoreScor+$score;
		
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		print "<br />";
		 print "sd";
	 print $sql;
	 print "<br />";
	 $sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1 and 0'){
if (($nRowsIn>0) and ($сloseContracts==0 )){
		$scoreScor=$scoreScor+$score;
		
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		print "<br />";
		 print "sd";
	 print $sql;
	 print "<br />";
	 $sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='0 and 0'){
if (($nRowsIn==0) and ($сloseContracts==0 )){
		$scoreScor=$scoreScor+$score;
		
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
		print "<br />";
		 print "sd";
	 print $sql;
	 print "<br />";
	 $sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}


print "<br />";
print $conditions;
print $closeContracts;
print "<br />";
 



		//$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
		//DatabaseHandler::Execute($sql);
	}
	
print "<br />";
print "1";
print "<br />";
	if ($idparameter==7)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and education {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==8)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and number_of_childrens {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==9)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and martial_status {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==10)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and employment_type {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==11)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and source_of_information {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
if ($idparameter==12)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and movable_property {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==13)
	{
		$sql="select count(*) from ClientsForCheckBKI where creditid=$creditid and property {$conditions}";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
if ($nRowsIn>0){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	
	if ($idparameter==14)
	{
if (is_null($maxubki)) $maxubki=0;
if (is_null($maxmbki)) $maxmbki=0;
		$nRowsIn=-1;
		
		if  (($decisionAfterScorFirst!=2) and ($decisionAfterScorSecond!=2))	{
	$sql="select count(distinct a.id) from crdealUBKI a join deallifeUBKI b on a.id=b.crdealid where b.cki_id=$maxubki  and `dlflstat`=2 ";

print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}
	
	

if ($conditions=='<1'){
if (($nRowsIn==0) and ($maxubki>0)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='>1'){
if (($nRowsIn>1)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
		
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==15)
	{
		$nRowsIn=-1;
		if (is_null($maxubki)) $maxubki=0;
if (is_null($maxmbki)) $maxmbki=0;
		if  (($decisionAfterScorFirst!=2) and ($decisionAfterScorSecond!=2))	{
	$sql="SELECT count(distinct a.id)  from crdealUBKI a   join deallifeUBKI b on a.id=b.crdealid WHERE b.cki_id=$maxubki and (a.dldonor='FIN' or a.dldonor='MFO') and b.dlds between b.dldpf - INTERVAL 180 day and b.dldpf and a.dlamt<=10000 and a.cki_id=$maxubki 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}
	
	

if ($conditions=='<1'){
if (($nRowsIn==0) and ($maxubki>0)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='>1'){
if (($nRowsIn>1)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
		
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==17)
	{
		$nRowsIn=-1;
		if (is_null($maxubki)) $maxubki=0;
if (is_null($maxmbki)) $maxmbki=0;

	if  (($decisionAfterScorFirst!=2) and ($decisionAfterScorSecond==1))	{
$sql="SELECT count(contracts.id) FROM `contractsMBKI` contracts  WHERE  `CreditorType` like 'Фінансова компанія%'  and contracts.SubjectId=$maxmbki and `PereodicityOfPayments`='В останній день строку дії договору'  and `TotalAmountValue`<=20000 
";

print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}	
		

if ($conditions=='<1'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='2')
{
if (($nRowsIn==2) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		//DatabaseHandler::Execute($sql);
}

}
if ($conditions=='3'){
if (($nRowsIn==3)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='>3'){
if (($nRowsIn>3)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==18)
	{
			$nRowsIn=-1;
			if (is_null($maxubki)) $maxubki=0;
if (is_null($maxmbki)) $maxmbki=0;
		if  (($decisionAfterScorFirst!=2) and ($decisionAfterScorSecond==1))	{
$sql="select NumberOfTerminatedContracts from summaryinformationdebtorMBKI where subjectid=$maxmbki 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql);
	}
	
		
 
if ($conditions=='<1'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='2')
{
if (($nRowsIn==2) ){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
DatabaseHandler::Execute($sql);
}

}
if ($conditions=='3')
{
if (($nRowsIn==3) ){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='>3'){
if (($nRowsIn>3)){
		$scoreScor=$scoreScor+$score;
					$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}


	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==20)
	{
		$nRowsIn=-1;
		if (is_null($maxubki)) $maxubki=0;
if (is_null($maxmbki)) $maxmbki=0;
			if  (($decisionAfterScorFirst!=2) and ($decisionAfterScorSecond==1))	{
$sql="select  InquieryValue from inqueryMBKI where SubjectId=$maxmbki and  id=(select min(id) from inqueryMBKI where SubjectId=$maxmbki)
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}
	
		

if ($conditions=='0'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1'){
if (($nRowsIn==1)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='2')
{
if (($nRowsIn==2) ){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='3'){
if (($nRowsIn==3)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}

if ($conditions=='>3'){
if (($nRowsIn>3)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}

		
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==22)
	{
		$nRowsIn=-1;
if (is_null($maxubki)) $maxubki=0;
if (is_null($maxmbki)) $maxmbki=0;
		
	if  (($decisionAfterScorFirst!=2) and ($decisionAfterScorSecond==1))	{
$sql="select count(id) from SearchInqueryMBKI where SubjectId=$maxmbki
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}	
		

if ($conditions=='0'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='between 1 and 5'){
if (($nRowsIn==1) or ($nRowsIn==2)or ($nRowsIn==3)or ($nRowsIn==4)or ($nRowsIn==5)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='>5')
{
if (($nRowsIn>5) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
DatabaseHandler::Execute($sql);
}

}

			
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
	if ($idparameter==25)
	{
		$nRowsIn=-1;
		if (is_null($maxubki)) $maxubki=0;
if (is_null($maxmbki)) $maxmbki=0;
		if  (($decisionAfterScorFirst!=2) and ($decisionAfterScorSecond==1))	{
$sql="select count(id) from SearchInqueryMBKI where Dated > (NOW() - INTERVAL 1 MONTH) and SubjectId=$maxmbki
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	}
	
		

if ($conditions=='0'){
if (($nRowsIn==0)){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='1')
{
if (($nRowsIn>0) ){
		$scoreScor=$scoreScor+$score;
			$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

		
	//	$sql="insert into parametersscoringcard(id,name,score,scoringcard_id,applicationid) values($idparameter,'$parameter',$scoreScor,$scoringcardid,$applicationid)";
	//	DatabaseHandler::Execute($sql);
		
	}
}
	if ($idparameter==26)
	{
		$nRowsIn=-1;
		$nRowsInSec=-1;
		if (is_null($maxubki)) $maxubki=0;
if (is_null($maxmbki)) $maxmbki=0;
	if  (($decisionAfterScorFirst!=2) )	{
$sql="SELECT count(contracts.id) FROM `contractsMBKI` contracts  WHERE  ContractType='Existing' and`CreditorType` like 'Фінансова компанія%'  and contracts.SubjectId=$maxmbki and `PereodicityOfPayments`='В останній день строку дії договору'  and `TotalAmountValue`<=20000 
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
	$sql="SELECT count(contracts.id) FROM `contractsMBKI` contracts  WHERE  `CreditorType` like 'Фінансова компанія%'  and contracts.SubjectId=$maxmbki and `PereodicityOfPayments`='В останній день строку дії договору'  and `TotalAmountValue`<=20000 
";
print $sql;
	$nRowsInSec=DatabaseHandler::GetOne($sql); 
	if ($nRowsIn==0) {$strscoring="Ні";} else {$strscoring="Так";}
$strscoring=$strscoring."/{$nRowsInSec}";
	}
		
	print "<br />";
		 print 	"nRowsIn=";
	 print 	$nRowsIn;
	 print "<br />";	
print "<br />";
	 print 	"nRowsInSec=";
	 print 	$nRowsInSec;
	 print "<br />";	
	 print $conditions;
	 	 print "<br />";	
if ($conditions=='Ні/0'){
if (($nRowsIn==0) and ($nRowsInSec==0) and ($maxmbki>0)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." {$strscoring} Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Ні/1'){
if (($nRowsIn==0) and ($nRowsInSec==1)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Ні/2')
{
if (($nRowsIn==0) and ($nRowsInSec==2) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Ні/3'){
if (($nRowsIn==0) and ($nRowsInSec==3) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Ні/99'){
if (($nRowsIn==0) and ($nRowsInSec>3) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Так/0'){
if (($nRowsIn>0) and ($nRowsInSec==0)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}

if ($conditions=='Так/1'){
if (($nRowsIn>0) and ($nRowsInSec==1)){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Так/2')
{
if (($nRowsIn>0) and ($nRowsInSec==2) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Так/3'){
if (($nRowsIn>0) and ($nRowsInSec==3) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
if ($conditions=='Так/99'){
if (($nRowsIn>0) and ($nRowsInSec>3) ){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,$nRowsIn,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}

}
}
if ($idparameter==27)
	{
		$sql="select status_of_residence,property from ClientsForCheckBKI where creditid=$creditid ";
print $sql;
	$nRowsIn=DatabaseHandler::GetAll($sql); 
	
if ($nRowsIn[0]['status_of_residence']=='1_Одноосібна власність'){
	if ($conditions=='="1_Одноосібна власність"'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,3,$score)";
		print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
}
if (($nRowsIn[0]['status_of_residence']!='1_Одноосібна власність') and ($nRowsIn[0]['property']=='2_Так')){
	if ($conditions=='="2_Так"'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,3,$score)";
			print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
}
if (($nRowsIn[0]['status_of_residence']!='1_Одноосібна власність') and (($nRowsIn[0]['property']=='1_Ні') )){
	if ($conditions=='="1_Ні"'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,3,$score)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
}
if (($nRowsIn[0]['status_of_residence']!='1_Одноосібна власність') and (($nRowsIn[0]['property']!='1_Ні') and ($nRowsIn[0]['property']!='2_Так'))){
	if ($conditions=='Не заповнено'){
		$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
		$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,3,$score)";
			print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
}	
}

}

}
$sql="select d.conditions conditions,d.score score from   bscoringcard d 
where  ascoringcard_id=$id and conditions='NULL'";
print $sql;
 	$resultsdd=DatabaseHandler::GetAll($sql);
	print_r($resultsdd);
for($k = 0; $k < count($resultsdd); ++$k) {
	$conditions=$resultsdd[$k]['conditions'];
	 $score=$resultsdd[$k]['score'];
	 	$sql="select 1 from scoringresult where scoringcardid=$scoringcardid and idparameter=$idparameter and applicationid=$applicationid and idrunscoring=3
";
print $sql;
	$nRowsIn=DatabaseHandler::GetOne($sql); 
print $nRowsIn;
if (($nRowsIn==0)){
	$scoreScor=$scoreScor+$score;
		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Cкорингу назва скорингової карти=\"{$scoringcardname}\" параметр=\"{$parameter}\" умова" .addslashes($conditions)." Заг сума скорингу=$scoreScor',$applicationid,'Scoring',3000)";
	print "<br />";
	 print $sql;
	 print "<br />";
			DatabaseHandler::Execute($sql);
			$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,$idparameter,0,3,$scoreScor)";
				print "<br />";
	 print $sql;
	 print "<br />";
		DatabaseHandler::Execute($sql);
	
}
}
}
	$sql="insert into scoringresult(id,applicationid,scoringcardid,idparameter,value,idrunscoring,sumScor) values(null,$applicationid,$scoringcardid,100,0,3,$scoreScor)";
			print "<br />";
	 print $sql;
	 print "<br />";
		//DatabaseHandler::Execute($sql);
	if (($Zone=='Grey Zone') or ($Zone=='БКІ')) $Zone='GreyZone';
		if ($Zone=='Red Zone') $Zone='RedZone';
				if ($Zone=='White Zone') $Zone='WhiteZone';
print "<br />";
print "end";
print "<br />";
	 print $Zone;
	 print "<br />";	
		print "<br />";	
		
//$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='"+substr_replace($Zone,' ',strpos($Zone,'Zone'),0)+"'";
if ($Zone=='GreyZone') {
$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='Grey Zone'";}
if ($Zone=='WhiteZone') {
$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='White Zone'";}
if ($Zone=='RedZone') {
$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='Red Zone'";}
if (($decisionAfterScorFirst==2) and (($Zone!='GreyZone') and ($Zone!='WhiteZone') and ($Zone!='RedZone'))) {
$sql="SELECT  `zone`, `rejectTo`, `approveFrom` FROM `decisionscoringcard` where scoringcard_id=$scoringcardid and zone='null'";}


 
print $sql;
 print "<br />";	
		print "<br />";	
 	$resultsddddd=DatabaseHandler::GetAll($sql);
	
for($r = 0; $r < count($resultsddddd); ++$r) {
	 $zoneSc=$resultsddddd[$r]['zone'];
	 $rejectTo=$resultsddddd[$r]['rejectTo'];
	 $approveFrom=$resultsddddd[$r]['approveFrom'];
/*	 
if ($approveFrom<=$scoreScor){
	if ($zoneSc=='White Zone'){
		$statusCheck=1;
	}
	if ($zoneSc=='Grey Zone'){
		$statusCheck=0;
	}
	if ($zoneSc=='Red Zone'){
		$statusCheck=2;
	}*/
	/*
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Результат Cкорингу назва скорингової карти=\"{$scoringcardname}\"  Заг сума скорингу=$scoreScor Відмовлено до=$rejectTo ',$applicationid,'$zoneSc',3000)";
		DatabaseHandler::Execute($sql);
	*/
		$statusCheckScor=0;
print "<br />";
print $rejectTo;
print "<br />";
print $scoreScor;
print "<br />";

if ($approveFrom>=$scoreScor){
	
		$statusCheckScor=1;
	} else
if ($rejectTo<$scoreScor){
	
	$statusCheckScor=2;
	if ($Zone=="White Zone") {$Zone='Grey Zone';
if (($decisionCard==0)) $statusCheck=2;} else {
$Zone='Red Zone';
if (($decisionCard==0)) $statusCheck=3;}
	} else {
		$statusCheckScor=0;
		if($Zone<>'Red Zone'){
		$Zone='Grey Zone';
if (($decisionCard==0))		$statusCheck=2;}
	}
	
	
	
/*	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Результат Cкорингу назва скорингової карти=\"{$scoringcardname}\"  Заг сума скорингу=$scoreScor Схвалено від=$approveFrom ',$applicationid,'$zoneSc',3000)";
		DatabaseHandler::Execute($sql);*/
}
if ($statusCheckScor==1) { 
$Zone="White Zone";
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Скор карти Рішення=Approve',$applicationid,'$Zone',1300)";

DatabaseHandler::Execute($sql);
$solutScor='Approve';
print "<br />";
print $decisionCard;
print "<br />";
print $ZoneMatrix;
print "<br />";
print $statusCheck;
print "<br />";

if (($ZoneMatrix==0) and ($decisionCard==0)) {$statusCheck=1;}

}
if ($statusCheckScor==2) {
	$Zone='Red Zone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Скор карти Рішення=Reject',$applicationid,'$Zone',1300)";
$statusCheck=3;
DatabaseHandler::Execute($sql);
$solutScor='Reject';
}
if ($statusCheckScor==0) {
	$Zone='Grey Zone';
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка Фін.Скор карти Рішення=Manual',$applicationid,'$Zone',1300)";

DatabaseHandler::Execute($sql);
$solutScor='Manual';
}
 $sql="update Applications set scoringcardname='$scoringcardname',scoringball=$scoreScor where creditid=$creditid";
//print $sql;
  DatabaseHandler::Execute($sql);

}


	}
	$sql="SELECT * FROM setofparameters a  WHERE   typeApplication=$typeSc and (parameterFromScor is not null and parameterToScor is not null)";

	$nMatrix=DatabaseHandler::GetAll($sql);
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Початок перевірки по картках',$applicationid,'',25500)";
print $sql;
 DatabaseHandler::Execute($sql);
$carddecisionname='';
print "<br />";
print_r($nMatrix);
print "<br />";


if ($statusInternalRates!=5)
{
if (count($nMatrix)>0)
{
 
for($r = 0; $r < count($nMatrix); ++$r) {

	$id_card=$nMatrix[$r]['id'];
	$name_card=$nMatrix[$r]['name'];
	$typeApplication_card=$nMatrix[$r]['typeApplication'];
	$limit_card=$nMatrix[$r]['limit'];
	$decision_card=$nMatrix[$r]['decision'];
		$bki=$nMatrix[$r]['bki'];
	$parameterFromScor=$nMatrix[$r]['parameterFromScor'];
$parameterToScor=$nMatrix[$r]['parameterToScor'];
	
	$pieces = explode('/', $itemsqd[$bki]);
	if (count($pieces)==0) {$pieces[0]=$bki;}
	print "<br />";
	print_r(count($pieces));
	print "<br />";
	$stopcheck=0;
	for($pi = 0; $pi < count($pieces); ++$pi)
	{
		
	if ($bki<>3){
$sql="SELECT * FROM conditioncard  WHERE  setofparameterid=$id_card  order by id ";
	} else {
$sql="SELECT * FROM conditioncard  WHERE  setofparameterid=$id_card and ((bki=$pi+1) or (bki is null)) order by id ";
		
	}
	$nMatrix_card=DatabaseHandler::GetAll($sql);
print_r($nMatrix_card);	
if (count($nMatrix)>0)
{
	for($z = 0; $z < count($nMatrix_card); ++$z) {
	print "<br />";
	 print_r($nMatrix_card[$z]);
	print "<br />";
	
	if ($stopcheck==0)
	{
	$matrixdecisionid_cardone=$nMatrix_card[$z]['matrixdecisionid'];
	$zone_cardzone=$nMatrix_card[$z]['zone'];
	$val_cardzone=$nMatrix_card[$z]['value'];
	$bki_cardone=$nMatrix_card[$z]['bki'];
if (($matrixdecisionid_cardone<>37) and ($matrixdecisionid_cardone<>38)){
$sql="select 1 from historyLog where applicationid=$applicationid and algId=1 and Text like '%{$pieces[$pi]}%' ";
	print $sql;
$checkBkicard=DatabaseHandler::GetOne($sql);
	} else {$checkBkicard=1;}
	if ($checkBkicard<>1) {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card не здійсненна Не проходила по БКІ заявка ОFF',$applicationid,'',25900)";
print $sql;
DatabaseHandler::Execute($sql);
 		$stopcheck=1;
	} else {
		print "<br />";
		print "Cardone=";
		print $matrixdecisionid_cardone;
		print "<br />";
		
	if (($matrixdecisionid_cardone<>37) and ($matrixdecisionid_cardone<>38)) {
	$sql="select Zone from historyLog where applicationid=$applicationid and algId=$matrixdecisionid_cardone and Text like '%{$pieces[$pi]}%' ";
	print $sql;
	$ZoneDecision=DatabaseHandler::GetOne($sql);
		$sql="select substring(Text, LOCATE('=',Text)+1,length(Text)-LOCATE('=',Text)-1) Value from historyLog where applicationid=$applicationid and algId=$matrixdecisionid_cardone and Text like '%{$pieces[$pi]}%' ";
	$ValueDecision=DatabaseHandler::GetOne($sql);
	
	print "<br />";
	print $ZoneDecision;
	print "<br />";
	print $val_cardzone;
	print "<br />";
	print $matrixdecisionid_cardone;
	print "<br />";
	print_r($estalginfo_card);
	print "<br />";
	if ($matrixdecisionid_cardone==39) $matrixdecisionid_cardone=20;
	print $estalginfo_card[$matrixdecisionid_cardone-1]['Name'];
	print "<br />";
	if (($val_cardzone=='')){
	if ($ZoneDecision<>''){
	
	$pos=strpos( $zone_card_arr[$zone_cardzone],$ZoneDecision);
	
	print $zone_card_arr[$zone_cardzone];
	print "<br />";
	if ($pos===false)
	{
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card Умова={$estalginfo_card[$matrixdecisionid_cardone-1]['Name']} Зона по картці=$zone_card_arr[$zone_cardzone] Зона по бкі=$ZoneDecision bki=$pieces[$pi]',$applicationid,'',25600)";
print $sql;
 DatabaseHandler::Execute($sql);
    
	$stopcheck=1;
	
	} else {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card Умова=$matrixdecisionid_cardone ОК',$applicationid,'',25600)";
print $sql;
 //DatabaseHandler::Execute($sql);
		
	}
	} else if ($matrixdecisionid_cardone==17){$stopcheck=1;}
	} else  if ($ZoneDecision=='') {
 if (strpos($val_cardzone, "-") !== false) {
   
 
list($fromVal, $toVal) = preg_split('/[-]/', $val_cardzone);
 }
if (is_null($fromVal)) {$fromVal=$val_cardzone;$toVal=$val_cardzone;}
	print $fromVal;
	print $toVal;
	print $ValueDecision;
	print "<br />";
	
	if ((((double)$ValueDecision)>=((double)$fromVal)) && (((double)$ValueDecision)<=((double)$toVal))){
	$pos=true;
	} else {
		$pos=false;
	}
	print $pos;
	print "<br />";
	if ($pos===false)
	{
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card Умова={$estalginfo_card[$matrixdecisionid_cardone-1]['Name']} Значення по картці=$val_cardzone Значення по бкі=$ValueDecision bki=$pieces[$pi]',$applicationid,'',25600)";
print $sql;
 DatabaseHandler::Execute($sql);
    
	$stopcheck=1;
	
	} else {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card Умова=$matrixdecisionid_cardone ОК',$applicationid,'',25600)";
print $sql;
 //DatabaseHandler::Execute($sql);
		
	} 
	}	else if ($matrixdecisionid_cardone==17){$stopcheck=1;}
	
	
	} else if ($matrixdecisionid_cardone==37) {
	
	$sql="select 1 from historyLog where applicationid=$applicationid and algId=37 and Text like '%Пустий звіт {$pieces[$pi]}%'";
	print $sql;
	$ZoneDecision=DatabaseHandler::GetOne($sql);
		print "<br />";
		print "ZoneDecision=".$ZoneDecision;
		print "<br />";
	if ($ZoneDecision==''){
	
	$stopcheck=1;
	}
	} else {
	
	$sql="select Zone from historyLog where applicationid=$applicationid and algId=300 and Text like '%Перевірка MatrixDecision1.2 Назва контролю=Вік%'";
	print $sql;
	$ZoneDecision=DatabaseHandler::GetOne($sql);

	print "<br />";
	print $ZoneDecision;
	print "<br />";
	print "<br />";
	print $ZoneMatrix;
	print "<br />";
	
	if ($ZoneDecision==''){
	$sql="select Zone from historyLog where applicationid=$applicationid and algId=300 and Text like '%Перевірка MatrixDecision1.2 Рішення=0%'";
	print $sql;
	$ZoneDecision=DatabaseHandler::GetOne($sql);
	
	if ($ZoneDecision=='БКІ') $ZoneDecision='WhiteZone';
	}
	$pos=strpos( $zone_card_arr[$zone_cardzone],$ZoneDecision);
	
	print $zone_card_arr[$zone_cardzone];
	print "<br />";
	if ($pos===false){
	
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірки по картці $name_card Умова={$estalginfo_card[$matrixdecisionid_cardone-1]['Name']} Зона по картці=$zone_card_arr[$zone_cardzone] Зона по бкі=$ZoneDecision bki=$pieces[$pi]',$applicationid,'',25600)";
print $sql;
 DatabaseHandler::Execute($sql);
    
	$stopcheck=1;
	
	
	} 
	}
	
	
	}
	
}
}	
	}
	
}
if ($stopcheck==0)
{
	
	if (($parameterFromScor*-1<=$scoreScor*-1) and ($parameterToScor*-1>=$scoreScor*-1)){
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Результат перевірки по картці $name_card ОК',$applicationid,'WhiteZone',25700)";
print $sql;
DatabaseHandler::Execute($sql);
	$carddecisionname=$name_card;
 {$decisionCard=1;$ZoneMatrix=0;}
if(	$limit_card>0) {
	if (($ammout_of_credit>$limit_card) and ($decision_card==1)) {
$sql="update CreditsClientsForCheckBKI  set  ammout_of_credit=$limit_card where id=$creditid";
print $sql;
DatabaseHandler::Execute($sql);
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Зменшення суми кредиту=Сума ліміту Початкова сума=$ammout_of_credit Змінена=$limit_card',$applicationid,'',25700)";
print $sql;
DatabaseHandler::Execute($sql);

$ammout_of_credit=$limit_card;
$statusCheck=1;

} 
} 
} else {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Результат перевірки по картці $name_card OFF сума скорингу=$scoreScor не входить в межі $parameterFromScor до $parameterToScor ',$applicationid,'',25700)";
print $sql;
DatabaseHandler::Execute($sql);

}
}
else {

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Результат перевірки по картці $name_card OFF',$applicationid,'GreyZone',25700)";
print $sql;
DatabaseHandler::Execute($sql);
	
}
}
if ($decisionCard==0) {
	if ($ZoneMatrix==1) {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Завершення перевірки по картках Фінальне рішення=Manual',$applicationid,'',25500)";
if ($statusCheck<2) { $statusCheck=2; }

	}  else {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Завершення перевірки по картках Фінальне рішення=Не визначено',$applicationid,'',25500)";
	}
	
}
if ($decisionCard==1) {
	if ($ZoneMatrix==1) {
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Завершення перевірки по картках Фінальне рішення=Manual',$applicationid,'',25500)";
if ($statusCheck<2) { $statusCheck=2; }

	} else {

$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Завершення перевірки по картках Фінальне рішення=Approve',$applicationid,'',25500)";
if (($statusCheck==0) or ($statusCheck==2)) { $statusCheck=1; }
}
DatabaseHandler::Execute($sql);
$sql="update Applications set carddecision='$carddecisionname' where creditid=$creditid";
	DatabaseHandler::Execute($sql);

}
if ($decisionCard==2) {
	$statusCheck=3;
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Завершення перевірки по картках Фінальне рішення=Reject',$applicationid,'',25500)";
$sql="update Applications set carddecision='$carddecisionname' where creditid=$creditid";
	DatabaseHandler::Execute($sql);

}

print $sql;
DatabaseHandler::Execute($sql);

}
}
	
}

		 
print "<br />";
	print "Кінець скорингу";

 
print "sdfsdf";
print $statusCheck;
			 	$bas=basename($file);
	print $bas;
	print "dsf";
	$pathArr = explode("/", $bas);
	print "dsf";
$filename = end($pathArr);
print_r($pathArr);
echo $filename;
	print "dsf";
	
//rename("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/ppr_automatic/in/{$bas}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/ppr_automatic/in_archive/{$nameFolder}/{$bas}");
print "ftp://{$usernameftp}:{$passwordftp}@{$server_url}/ppr_automatic/in_archive/{$nameFolder}/{$bas}";
print "ftp://{$usernameftp}:{$passwordftp}@{$server_url}/ppr_automatic/in/{$bas}";
print "<br />";
print $resultMatrixDesicion;
print "<br />";
print $resultMatrixDesicionSec;
print "<br />";
print $statusCheck; 
if ($statusCheck==0) $statusCheck=2;
if ($statusCheck==1) {$zoneLim='White Zone';}
if ($statusCheck==2) {$zoneLim='Grey Zone';}
if ($statusCheck==3) {$zoneLim='Red Zone';}
$autoLimXml='';	
print $AutoLim;
print "<br />";
if ($AutoLim>0) {$limCred=$ammout_of_credit/$AutoLim;} else {$limCred=0;}
print "<br />";
print $AutoLim;
print "<br />";
print $limCred;
if (is_null($tzid) or ($tzid==''))  $tzid=0;

if ($limCred>0) {
	$sql="SELECT * FROM matrixdecisiondetail WHERE matrixdecisionid=$tzid and $limCred between LimFrom and LimTo ";
print $sql;
	$nMatrix=DatabaseHandler::GetAll($sql);
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Початок первірки визначення ліміту',$applicationid,'',5500)";
print $sql;
 DatabaseHandler::Execute($sql);

print "<br />";
print_r($nMatrix);
print "<br />";

if (count($nMatrix)>0)
{
 $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Початок первірки визначення ліміту',$applicationid,'',5500)";
print $sql;
 DatabaseHandler::Execute($sql);

for($r = 0; $r < count($nMatrix); ++$r) {
$idm=$nMatrix[$r]['id'];
	$CheckAutoLim=$nMatrix[$r]['AutoLim'];
	$zoneLim=$nMatrix[$r]['Zone'];
	$LimFrom=$nMatrix[$r]['LimFrom'];
	$LimTo=$nMatrix[$r]['LimTo'];
		$desclimit=$nMatrix[$r]['desclimit'];
	if ($CheckAutoLim==2) {$autoLimXml='Так';	} else {$autoLimXml='Ні';}
if ($zoneLim==1) {$zoneLim1='White Zone'; if ($statusCheck<1) $statusCheck=1;
if ($desclimit==1) {
	if ($ammout_of_credit>$AutoLim) {
$sql="update CreditsClientsForCheckBKI  set  ammout_of_credit=$AutoLim where id=$creditid";
DatabaseHandler::Execute($sql);
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Сума кредиту={$ammout_of_credit},Сума ліміту={$AutoLim},Коеф.ліміту={$limCred}',$applicationid,'',5500)";

print $sql;
DatabaseHandler::Execute($sql);
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Зменшення суми кредиту=Сума ліміту Початкова сума=$ammout_of_credit Змінена=$AutoLim',$applicationid,'',6500)";

DatabaseHandler::Execute($sql);
$ammout_of_credit=$AutoLim;
	}
}

}
if ($zoneLim==2) {
	$zoneLim1='Grey Zone'; 
}
if ($zoneLim==3) {$zoneLim1='Red Zone'; $statusCheck=3;}
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Зона={$zoneLim1}, АвтоЛім={$autoLimXml}, межі від {$LimFrom} до {$LimTo}',$applicationid,'',5500)";
print $sql;
DatabaseHandler::Execute($sql);
if (($zoneLim==2) and ($statusCheck==1)){
	$zoneLim1='Grey Zone'; 
 
if ($desclimit==1) {
	if ($ammout_of_credit>$AutoLim) {
$sql="update CreditsClientsForCheckBKI  set  ammout_of_credit=$AutoLim where id=$creditid";
DatabaseHandler::Execute($sql);
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Сума кредиту={$ammout_of_credit},Сума ліміту={$AutoLim},Коеф.ліміту={$limCred}',$applicationid,'',5500)";

print $sql;
DatabaseHandler::Execute($sql);
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Зменшення суми кредиту=Сума ліміту Початкова сума=$ammout_of_credit Змінена=$AutoLim',$applicationid,'',6500)";

DatabaseHandler::Execute($sql);

$ammout_of_credit=$AutoLim;
$statusCheck=1;
} 


} else {$statusCheck=2;}

}	else {	
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Сума кредиту={$ammout_of_credit},Сума ліміту={$AutoLim},Коеф.ліміту={$limCred}',$applicationid,'',5500)";

print $sql;
DatabaseHandler::Execute($sql);
}
	$sql="update Applications set LimCred=$limCred,matrixdecisionid=$idm,AutoLim='$autoLimXml',idmbki=$idmbki,idubki=$idubki where creditid=$creditid";
	DatabaseHandler::Execute($sql);

/*$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Сума кредиту={$ammout_of_credit},Сума ліміту={$AutoLim},Коеф.ліміту={$limCred}',$applicationid,'',5500)";
print $sql;
DatabaseHandler::Execute($sql);
	$sql="update Applications set LimCred=$limCred,matrixdecisionid=$idm,AutoLim='$autoLimXml'  where creditid=$creditid";
	DatabaseHandler::Execute($sql);*/
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Входить в межі',$applicationid,'$zoneLim1',5500)";
DatabaseHandler::Execute($sql);	




}



} else {
	
$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Не входить в межі',$applicationid,'$zoneLim1',5500)";
DatabaseHandler::Execute($sql);	

	print $resultMatrixDesicion;
	if ($resultMatrixDesicion=='ОК') {
		if ($statusCheck<2) $statusCheck=2; 
	$autoLimXml='Ні';
	}
	print $autoLimXml;
	}
	$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Завершення первірки визначення ліміту',$applicationid,'',5500)";

DatabaseHandler::Execute($sql);
}	else {
$autoLimXml='Ні';
}
if (($statusCheck==1) and ($ammout_of_credit>$maxsuma)) {

		$sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Встановлення суми кредиту=макс суми скорингу Початкова сума=$ammout_of_credit Змінена=$maxsuma',$applicationid,'',6500)";

DatabaseHandler::Execute($sql);
$ammout_of_credit=$maxsuma;
}
print "<br />";
print "<br />";
print "<br />";
print 
print "<br />";

if (($ZoneMatrix==1) and ($statusCheck==1)) {$statusCheck=2;}
if ($statusCheck==1) {$Zone='White Zone';}
if ($statusCheck==2) {$Zone='Grey Zone';}
if ($statusCheck==3) {$Zone='Red Zone';}
if (($statusCheck==2) and ($resultMatrixDesicionSec==1) )
{
$sql="SELECT * FROM Applications WHERE id=$applicationid and decisionAfterBKI=3 and carddecision is null ";
$nRows=DatabaseHandler::GetAll($sql);

if(count($nRows)>0){
$statusCheck=3; $Zone='Red Zone';
}
}
	if ( ($resultMatrixDesicion=='БКІ')) {

 if ($statusCheck==1){ 
 $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Фінальне рішення=Approve',$applicationid,'$Zone',1300)";

DatabaseHandler::Execute($sql);
 $sql="update Applications set typeDecision='Approve',dateDecision=NOW()  where creditid=$creditid";
//print $sql;
  DatabaseHandler::Execute($sql);
	$sql="update CreditsClientsForCheckBKI  set  ammout_of_credit=$ammout_of_credit where id=$creditid";
//print $sql;
  DatabaseHandler::Execute($sql);
 	
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/approve/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/approve/{$file}");
  
}
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/approve/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/approve/{$file}");
  
}
  copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/approve/{$file}");
			    $xml = simplexml_load_file("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/approve/{$file}");
				if (isset($filembki) and ($filembki<>'')){
						if(isset($xml->report_ibch))  {$xml->report_ibch=$filembki;}
				else 		   {$xml->addChild('report_ibch', $filembki);}

				}
				if (isset($fileubki) and ($fileubki<>'')){
						if(isset($xml->report_ubk))  {$xml->report_ubk=$fileubki;}
				else 		   {$xml->addChild('report_ubk', $fileubki);}
				

				}
					if(!is_null($xml->match_phone_UBKI))  {$xml->match_phone_UBKI=$match_phone_UBKI;}
				else 		   {$xml->addChild('match_phone_UBKI', $match_phone_UBKI);}
				if ($match_phone_UBKI==1){
					if(!is_null($xml->zone_type)  )  {$xml->zone_type='WZ';}
				else 		   {$xml->addChild('zone_type', 'WZ');}
				} else {
				
					if(!is_null($xml->zone_type)  )  {$xml->zone_type='GZ';}
				else 		   {$xml->addChild('zone_type', 'GZ');}
				}
				if(!is_null($xml->solution))  { $xml->solution='Approve';}
						  else {$xml->addChild('solution', 'Approve');}
				if(!is_null($xml->solutionsc))  { $xml->solutionsc=$solutScor;}
						  else
						  {$xml->addChild('solutionsc', $solutScor);}

						   if(!is_null($xml->matrix_solutions_1))  {$xml->matrix_solutions_1='1';}
				else 		   {$xml->addChild('matrix_solutions_1', '1');}
										if(!is_null($xml->dateDecision))  {$xml->dateDecision=strftime("%F %T");}
				else 		   {$xml->addChild('dateDecision', strftime("%F %T"));}
	if(!is_null($xml->scoringcardName))  {$xml->scoringcardName=$scoringcardname;}
				else 		   {$xml->addChild('scoringcardName', $scoringcardname);}
	if(!is_null($xml->scoringBall))  {$xml->scoringBall=$scoreScor;}
				else 		   {$xml->addChild('scoringBall', $scoreScor);}
			if(!is_null($xml->AutoLim))  {$xml->AutoLim=$autoLimXml;}
				else {$xml->addChild('AutoLim', $autoLimXml);}
$xml->ammout_of_credit=$ammout_of_credit;
								if(!is_null($xml->idSPR)) {	$xml->idSPR=$applicationid;}
			else {$xml->addChild('idSPR', $applicationid);}	  
		
									   $xml->asXML("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/approve/{$file}");
									   $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка завершена ',$applicationid,'Approve',500)";
							     DatabaseHandler::Execute($sql);
								 if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}");
  
}
						copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/approve/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}");	
rename("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in_archive/{$nameFolder}/{$file}");						

   }
 
 
               if ($statusCheck==3){ 
			   $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Фінальне рішення=Reject',$applicationid,'$Zone',1300)";

DatabaseHandler::Execute($sql);
			 $sql="update Applications set typeDecision='Reject',dateDecision=NOW() where creditid=$creditid";
//print $sql;
  DatabaseHandler::Execute($sql);   
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/reject/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/reject/{$file}");
  
}
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/reject/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/reject/{$file}");
  
}  		   
   copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/reject/{$file}");
   $xml = simplexml_load_file("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/reject/{$file}");
   if (isset($filembki) and ($filembki<>'')){
						if(isset($xml->report_ibch))  {$xml->report_ibch=$filembki;}
				else 		   {$xml->addChild('report_ibch', $filembki);}

				}
				if (isset($fileubki) and ($fileubki<>'')){
						if(isset($xml->report_ubk))  {$xml->report_ubk=$fileubki;}
				else 		   {$xml->addChild('report_ubk', $fileubki);}

				}
				if(!is_null($xml->solution))  { $xml->solution='Reject';}
						  else {$xml->addChild('solution', 'Reject');}
				if(!is_null($xml->solutionsc))  { $xml->solutionsc=$solutScor;}
						  else
						  {$xml->addChild('solutionsc', $solutScor);}
	if(!is_null($xml->match_phone_UBKI))  {$xml->match_phone_UBKI=$match_phone_UBKI;}
				else 		   {$xml->addChild('match_phone_UBKI', $match_phone_UBKI);}
					if(!is_null($xml->zone_type) )  {$xml->zone_type='RZ';}
				else 		   {$xml->addChild('zone_type', 'RZ');}
				
						   if(!is_null($xml->matrix_solutions_1))  {$xml->matrix_solutions_1='1';}
				else 		   {$xml->addChild('matrix_solutions_1', '1');}
										if(!is_null($xml->dateDecision))  {$xml->dateDecision=strftime("%F %T");}
				else 		   {$xml->addChild('dateDecision', strftime("%F %T"));}
	if(!is_null($xml->scoringcardName))  {$xml->scoringcardName=$scoringcardname;}
				else 		   {$xml->addChild('scoringcardName', $scoringcardname);}
	if(!is_null($xml->scoringBall))  {$xml->scoringBall=$scoreScor;}
				else 		   {$xml->addChild('scoringBall', $scoreScor);}
		
	if(!is_null($xml->AutoLim))  {$xml->AutoLim=$autoLimXml;}
				else {$xml->addChild('AutoLim', $autoLimXml);}
if(!is_null($xml->idSPR)) {	$xml->idSPR=$applicationid;}
			else {$xml->addChild('idSPR', $applicationid);}
									   $xml->asXML("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/reject/{$file}");
									   $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка завершена ',$applicationid,'Reject',500)";
								     DatabaseHandler::Execute($sql);
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}");
  
}
									 copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/reject/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}");
										rename("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in_archive/{$nameFolder}/{$file}");
										}
			   if ($statusCheck==2){
				   $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Фінальне рішення=Manual',$applicationid,'$Zone',1300)";

DatabaseHandler::Execute($sql);
			$sql="update Applications set typeDecision='Manual',dateDecision=NOW() where creditid=$creditid";
//print $sql;
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}");
  
}
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}");
  
}
  DatabaseHandler::Execute($sql); 	   
	 copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}");
   $xml = simplexml_load_file("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}");
   if (isset($filembki) and ($filembki<>'')){
						if(isset($xml->report_ibch))  {$xml->report_ibch=$filembki;}
				else 		   {$xml->addChild('report_ibch', $filembki);}

				}
				if (isset($fileubki) and ($fileubki<>'')){
						if(isset($xml->report_ubk))  {$xml->report_ubk=$fileubki;}
				else 		   {$xml->addChild('report_ubk', $fileubki);}

				}
				print $xml->solution;
					if(!is_null($xml->match_phone_UBKI))  {$xml->match_phone_UBKI=$match_phone_UBKI;}
				else 		   {$xml->addChild('match_phone_UBKI', $match_phone_UBKI);}
				if(!is_null($xml->zone_type) )  {$xml->zone_type='RZ';}
				else 		   {$xml->addChild('zone_type', 'RZ');}
				
				if(!is_null($xml->solution))  { $xml->solution='Manual';}
						  else {$xml->addChild('solution', 'Manual');}
				if(!is_null($xml->solutionsc))  { $xml->solutionsc=$solutScor;}
						  else
						  {$xml->addChild('solutionsc', $solutScor);}

						   if(!is_null($xml->matrix_solutions_1))  {$xml->matrix_solutions_1='1';}
				else 		   {$xml->addChild('matrix_solutions_1', '1');}
										if(!is_null($xml->dateDecision))  {$xml->dateDecision=strftime("%F %T");}
				else 		   {$xml->addChild('dateDecision', strftime("%F %T"));}
	if(!is_null($xml->scoringcardName))  {$xml->scoringcardName=$scoringcardname;}
				else 		   {$xml->addChild('scoringcardName', $scoringcardname);}
	if(!is_null($xml->scoringBall))  {$xml->scoringBall=$scoreScor;}
				else 		   {$xml->addChild('scoringBall', $scoreScor);}
			if(!is_null($xml->AutoLim))  {$xml->AutoLim=$autoLimXml;}
				else {$xml->addChild('AutoLim', $autoLimXml);}
if(!is_null($xml->idSPR)) {	$xml->idSPR=$applicationid;}
			else {$xml->addChild('idSPR', $applicationid);}
	
									   $xml->asXML("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}");
									   $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка завершена ',$applicationid,'Manual',500)";
						     DatabaseHandler::Execute($sql);
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/manual/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/manual/{$file}");
  
}
							 copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/manual/{$file}");		
rename("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in_archive/{$nameFolder}/{$file}");								
	
			   }
	}			                 
if ( ($resultMatrixDesicion<>'БКІ')) {

 if ($statusCheck==1){ 
 $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Фінальне рішення=Approve',$applicationid,'$Zone',1300)";

DatabaseHandler::Execute($sql);
 $sql="update Applications set typeDecision='Approve',dateDecision=NOW() where creditid=$creditid";
//print $sql;
  DatabaseHandler::Execute($sql);
	$sql="update CreditsClientsForCheckBKI  set  ammout_of_credit=$ammout_of_credit where id=$creditid";
//print $sql;
  DatabaseHandler::Execute($sql);	
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/approve/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/approve/{$file}");
  
}
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/approve/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/approve/{$file}");
  
}
  copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/approve/{$file}");
			    $xml = simplexml_load_file("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/approve/{$file}");
				if(!is_null($xml->solution))  { $xml->solution='Approve';}
						  else {$xml->addChild('solution', 'Approve');}
				if(!is_null($xml->solutionsc))  { $xml->solutionsc=$solutScor;}
						  else
						  {$xml->addChild('solutionsc', $solutScor);}
	if(!is_null($xml->zone_type) )  {$xml->zone_type='RZ';}
				else 		   {$xml->addChild('zone_type', 'RZ');}
						   if(!is_null($xml->matrix_solutions_1))  {$xml->matrix_solutions_1='1';}
				else 		   {$xml->addChild('matrix_solutions_1', '1');}
										if(!is_null($xml->dateDecision))  {$xml->dateDecision=strftime("%F %T");}
				else 		   {$xml->addChild('dateDecision', strftime("%F %T"));}
	if(!is_null($xml->scoringcardName))  {$xml->scoringcardName=$scoringcardname;}
				else 		   {$xml->addChild('scoringcardName', $scoringcardname);}
	if(!is_null($xml->scoringBall))  {$xml->scoringBall=$scoreScor;}
				else 		   {$xml->addChild('scoringBall', $scoreScor);}
		
							if(!is_null($xml->AutoLim))  {$xml->AutoLim=$autoLimXml;}
				else {$xml->addChild('AutoLim', $autoLimXml);}
			  if(!is_null($xml->idSPR)) {	$xml->idSPR=$applicationid;}
			else {$xml->addChild('idSPR', $applicationid);}
			$xml->ammout_of_credit=$ammout_of_credit;
									   $xml->asXML("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/approve/{$file}");
									   $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка завершена ',$applicationid,'Approve',500)";
				//					     DatabaseHandler::Execute($sql);
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}");
  
}

				copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/approve/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}");	
rename("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in_archive/{$nameFolder}/{$file}");						

   }
 
 
               if ($statusCheck==3){ 
			   $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Фінальне рішення=Reject',$applicationid,'$Zone',1300)";

DatabaseHandler::Execute($sql);
			 $sql="update Applications set typeDecision='Reject',dateDecision=NOW() where creditid=$creditid";
//print $sql;
  DatabaseHandler::Execute($sql);   
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/reject/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/reject/{$file}");
  
}
  		   if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/reject/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/reject/{$file}");
  
}
   copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/reject/{$file}");
   $xml = simplexml_load_file("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/reject/{$file}");
   			if(!is_null($xml->solution))  { $xml->solution='Reject';}
						  else {$xml->addChild('solution', 'Reject');}
				if(!is_null($xml->solutionsc))  { $xml->solutionsc=$solutScor;}
						  else
						  {$xml->addChild('solutionsc', $solutScor);}
	if(!is_null($xml->zone_type) )  {$xml->zone_type='RZ';}
				else 		   {$xml->addChild('zone_type', 'RZ');}
						   if(!is_null($xml->matrix_solutions_1))  {$xml->matrix_solutions_1='1';}
				else 		   {$xml->addChild('matrix_solutions_1', '1');}
										if(!is_null($xml->dateDecision))  {$xml->dateDecision=strftime("%F %T");}
				else 		   {$xml->addChild('dateDecision', strftime("%F %T"));}
	if(!is_null($xml->scoringcardName))  {$xml->scoringcardName=$scoringcardname;}
				else 		   {$xml->addChild('scoringcardName', $scoringcardname);}
	if(!is_null($xml->scoringBall))  {$xml->scoringBall=$scoreScor;}
				else 		   {$xml->addChild('scoringBall', $scoreScor);}
		
					if(!is_null($xml->AutoLim))  {$xml->AutoLim=$autoLimXml;}
				else {$xml->addChild('AutoLim', $autoLimXml);}
if(!is_null($xml->idSPR)) {	$xml->idSPR=$applicationid;}
			else {$xml->addChild('idSPR', $applicationid);}
									   $xml->asXML("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/reject/{$file}");
									   $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка завершена ',$applicationid,'Reject',500)";
		//						     DatabaseHandler::Execute($sql);
		if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}");
  
}

									 	copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/reject/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/out/{$file}");
										rename("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in_archive/{$nameFolder}/{$file}");
										}
			   if ($statusCheck==2){
				   $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Фінальне рішення=Manual',$applicationid,'$Zone',1300)";

DatabaseHandler::Execute($sql);
			$sql="update Applications set typeDecision='Manual',dateDecision=NOW() where creditid=$creditid";
//print $sql;
  DatabaseHandler::Execute($sql); 	   
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}");
  
}
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}");
  
}
  copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}");
   $xml = simplexml_load_file("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}");
   			print $xml->solution;
				if(!is_null($xml->solution))  { $xml->solution='Manual';}
						  else {$xml->addChild('solution', 'Manual');}
				if(!is_null($xml->solutionsc))  { $xml->solutionsc=$solutScor;}
						  else
						  {$xml->addChild('solutionsc', $solutScor);}
	if(!is_null($xml->zone_type) )  {$xml->zone_type='RZ';}
				else 		   {$xml->addChild('zone_type', 'RZ');}
						   if(!is_null($xml->matrix_solutions_1))  {$xml->matrix_solutions_1='1';}
				else 		   {$xml->addChild('matrix_solutions_1', '1');}
										if(!is_null($xml->dateDecision))  {$xml->dateDecision=strftime("%F %T");}
				else 		   {$xml->addChild('dateDecision', strftime("%F %T"));}
	if(!is_null($xml->scoringcardName))  {$xml->scoringcardName=$scoringcardname;}
				else 		   {$xml->addChild('scoringcardName', $scoringcardname);}
	if(!is_null($xml->scoringBall))  {$xml->scoringBall=$scoreScor;}
				else 		   {$xml->addChild('scoringBall', $scoreScor);}
			if(!is_null($xml->AutoLim))  {$xml->AutoLim=$autoLimXml;}
				else {$xml->addChild('AutoLim', $autoLimXml);}

	if(!is_null($xml->idSPR)) {	$xml->idSPR=$applicationid;}
			else {$xml->addChild('idSPR', $applicationid);}
									   $xml->asXML("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}");
									   $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка завершена ',$applicationid,'Manual',500)";
			//					     DatabaseHandler::Execute($sql);
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/manual/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/manual/{$file}");
  
}

			copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/manual/{$file}");		
rename("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in_archive/{$nameFolder}/{$file}");								
		}		                 

 }
} else {
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}");
  
}
	if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}");
  
}
	copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}");
   $xml = simplexml_load_file("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/temp/manual/{$file}");
 
			if(!is_null($xml->zone_type) )  {$xml->zone_type='RZ';}
				else 		   {$xml->addChild('zone_type', 'RZ');}
   								   	if(isset($xml->solution))  {$xml->solution='Manual';}
				else 		   {$xml->addChild('solution', 'Manual');}
					if(!is_null($xml->AutoLim))  {$xml->AutoLim=$autoLimXml;}
				else {$xml->addChild('AutoLim', $autoLimXml);}
if(!is_null($xml->idSPR)) {	$xml->idSPR=$applicationid;}
			else {$xml->addChild('idSPR', $applicationid);}
														   $xml->asXML("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}");
									   $sql="insert into historyLog(id,inn,Text,applicationid,Zone,algId) values(null,'$inn','Перевірка завершена ',$applicationid,'Manual',500)";
				//					     DatabaseHandler::Execute($sql);
if (file_exists("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/manual/{$file}")) {
          unlink("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/manual/{$file}");
  
}

				copy("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/manual/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/manual/{$file}");		
rename("ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in/{$file}","ftp://{$usernameftp}:{$passwordftp}@{$server_url}/xml/ppr_automatic/in_archive/{$nameFolder}/{$file}");
}
 }
 
 
 
ftp_close( $conn_id );


?>
