<?php
error_reporting(0);
function deliver_response($response){
	// Define HTTP responses
	$http_response_code = array(
		100 => 'Continue',  
		101 => 'Switching Protocols',  
		200 => 'OK',
		201 => 'Created',  
		202 => 'Accepted',  
		203 => 'Non-Authoritative Information',  
		204 => 'No Content',  
		205 => 'Reset Content',  
		206 => 'Partial Content',  
		300 => 'Multiple Choices',  
		301 => 'Moved Permanently',  
		302 => 'Found',  
		303 => 'See Other',  
		304 => 'Not Modified',  
		305 => 'Use Proxy',  
		306 => '(Unused)',  
		307 => 'Temporary Redirect',  
		400 => 'Bad Request',  
		401 => 'Unauthorized',  
		402 => 'Payment Required',  
		403 => 'Forbidden',  
		404 => 'Not Found',  
		405 => 'Method Not Allowed',  
		406 => 'Not Acceptable',  
		407 => 'Proxy Authentication Required',  
		408 => 'Request Timeout',  
		409 => 'Conflict',  
		410 => 'Gone',  
		411 => 'Length Required',  
		412 => 'Precondition Failed',  
		413 => 'Request Entity Too Large',  
		414 => 'Request-URI Too Long',  
		415 => 'Unsupported Media Type',  
		416 => 'Requested Range Not Satisfiable',  
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',  
		501 => 'Not Implemented',  
		502 => 'Bad Gateway',  
		503 => 'Service Unavailable',  
		504 => 'Gateway Timeout',  
		505 => 'HTTP Version Not Supported'
		);

	// Set HTTP Response
	header('HTTP/1.1 '.$response['status'].' '.$http_response_code[ $response['status'] ]);
	// Set HTTP Response Content Type
	header('Content-Type: application/json; charset=utf-8');
	// Format data into a JSON response
	$json_response = json_encode($response['data']);
	// Deliver formatted data
	echo $json_response;

	exit;
}


// Set default HTTP response of 'Not Found'
$response['status'] = 404;
$response['data'] = NULL;

$url_array = explode('/', $_SERVER['REQUEST_URI']);
array_shift($url_array); // remove first value as it's empty
// remove 2nd and 3rd array, because it's directory
array_shift($url_array); // 2nd = 'NativeREST'
array_shift($url_array); // 3rd = 'api'

// get the action (resource, collection)
$action = $url_array[0];
// get the method
$method = $_SERVER['REQUEST_METHOD'];
$nameFolder=date('Ymd');
$year=date('Y');

if(is_dir("//sv-ts01/fs$/documents/Target/{$year}")==false)
	{
		mkdir("//sv-ts01/fs$/documents/Target/{$year}");
	}
if(is_dir("//sv-ts01/fs$/documents/Target/{$year}/{$nameFolder}")==false)
	{
		mkdir("//sv-ts01/fs$/documents/Target/{$year}/{$nameFolder}");
	}

if(is_dir("//sv-ts01/fs$/xml_files/Target/{$year}")==false)
	{
		mkdir("//sv-ts01/fs$/xml_files/Target/{$year}");
	}
if(is_dir("//sv-ts01/fs$/xml_files/Target/{$year}/{$nameFolder}")==false)
	{
		mkdir("//sv-ts01/fs$/xml_files/Target/{$year}/{$nameFolder}");
	}		

require_once("clientinfo.php");
if( strcasecmp($action,'short') == 0){
	$Clientinfo = new Clientinfo();
				

	if($method=='GET'){
		if(!isset($url_array[1])){ // if parameter idBarang not exist
			// METHOD : GET api/barang
			$data=$Clientinfo->getAllClientinfo();
			$response['status'] = 200;
			$response['data'] = $data;
		}else{ // if parameter idBarang exist
			// METHOD : GET api/barang/:idBarang
			$idclient=$url_array[1];
			$data=$Clientinfo->getApplication($idclient);
			if(empty($data)) {
				$response['status'] = 404;
				$response['data'] = array('error' => 'Clientinfo error');	
			}else{
				$response['status'] = 200;
				$response['data'] = $data;	
			}
		}
	}
	elseif($method=='POST'){
		// METHOD : POST api/barang
		// get post from client
		$json = file_get_contents('php://input');
		//if (json_decode($json)==false) $Clientinfo->insertLog(implode ($url_array) ,"error", "error");
		$post = json_decode($json); // decode to object
		//$status = $Clientinfo->insertLog(implode ($url_array) ,serialize($post), $json);
		// check input subject data
		
		if ($post->creationDate=="" || $post->companySendId==""|| $post->key!="1qazxfhj4") {
			$response['status'] = 400;
			$response['data'] = array('error' => 'creationDate\companySendId\key is bad');
		}else{
			//insert subject data
			$status = $Clientinfo->insertSubjectinfo($post->creationDate, $post->companySendId, $post->key);
		if($status==1){
				//request subject id
				$data= $Clientinfo->outputIntID($post->companySendId);
				if(is_dir("//sv-ts01/fs$/documents/Target/{$year}/{$nameFolder}/{$data->id}")==false)
	{
		mkdir("//sv-ts01/fs$/documents/Target/{$year}/{$nameFolder}/{$data->id}");
	}
			
				if(is_dir("//sv-ts01/fs$/xml_files/Target/{$year}/{$nameFolder}/{$data->id}")==false)
	{
		mkdir("//sv-ts01/fs$/xml_files/Target/{$year}/{$nameFolder}/{$data->id}");
	}
	$myFile ="//sv-ts01/fs$/xml_files/Target/{$year}/{$nameFolder}/{$data->id}/{$data->id}z.txt";
$fh = fopen($myFile, 'w') or die("can't open file");

fwrite($fh, $json);
fclose($fh);
				// check input completeness
				if($post->client_info->surname=="" || $post->client_info->name=="" || $post->client_info->patronymic=="" || $post->client_info->ucn=="" ){
					$response['status'] = 400;
					$response['data'] = array('error' => 'Some data empty in Clientinfo block');
				}else{
					
					$status = $Clientinfo->
								insertClientinfo(
												$data->id
												,$post->client_info->custVisAss
												,$post->client_info->surname 
												,$post->client_info->name
												,$post->client_info->patronymic 
												,$post->client_info->ucn
												,$post->client_info->birthDate
												,$post->client_info->sex
												,$post->client_info->serDoc
												,$post->client_info->numDoc
												,$post->client_info->dateIssDoc
												,$post->client_info->whoIssDoc
												,$post->client_info->numDocID
												,$post->client_info->whoIssDocID
												,$post->client_info->EDRRID
												,$post->client_info->dateIssDocID
												,$post->client_info->dateToID
												,$post->client_info->citizenship
												,$post->client_info->birthPlace
												,$post->client_info->familyStatus
												,$post->client_info->surnameMer
												,$post->client_info->firstnameMer
												,$post->client_info->patrNameMer
												,$post->client_info->socStatusMer
												,$post->client_info->birthDateMer
												,$post->client_info->mobPhoneMer
												,$post->client_info->education
												,$post->client_info->numOfDep
												,$post->client_info->totalChildren
												,$post->client_info->mobPhone
												,$post->client_info->eMail
												);
					
					
					
						
						
					
				}
				
			}else{
			$response['status'] = 400;
			$response['data'] = array('error' => 'Error');
			}
			
		}
	}
	
}

// Return Response to browser
deliver_response($response);

?>