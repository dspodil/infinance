<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
$response['data']['error'] = NULL;
$url_array = explode('/', parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
 $output= explode('?', $_SERVER["REQUEST_URI"]);

if (count($output)>1) {
    $requests=explode('=',$output[1]);
if (count($requests)>0)
{
	$requestid=$requests[1];
} else {
	    $response['status'] = 400;
		$requestid=0;
		$response['data'] = array('error' => 'Not requestid');
}
}
else
{
		$requestid=0;
		   $response['data'] = array('error' => 'Not requestid');
    $response['status'] = 400;
}

array_shift($url_array); // remove first value as it's empty
// remove 2nd and 3rd array, because it's directory
array_shift($url_array); // 2nd = 'NativeREST'
array_shift($url_array); // 3rd = 'api'

// get the action (resource, collection)
$action = $url_array[2];
// get the method
$method = $_SERVER['REQUEST_METHOD'];
$nameFolder=date('Ymd');
$headers = array();
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0) {
        $headers[str_replace(' ', '', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))))] = $value;
    }
}

if (array_key_exists('XBobraService', $headers)) {
    $token=$headers['XBobraService'];
} else {$token="";}

require_once("clientinfo.php");
if( strcasecmp($action,'short') == 0){
	$Clientinfo = new Clientinfo();
    $datatoken=$Clientinfo->getToken($token);

    if ((count($datatoken)>0) && ($requestid>0)){
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
			
         
            $post = json_decode($json); // decode to object
            //$status = $Clientinfo->insertLog(implode ($url_array) ,serialize($post), $json);
            // check input subject data


            if ($post->BorrowerData->inn=="" || $post->User->tel_number=="") {
                $response['status'] = 400;
                $response['data'] = array('error' => 'inn\tel_number is bad');
            }else{
                //insert subject data
				$str=json_encode($post);
			
                $status = $Clientinfo->insertRequestApplications($str,1,  $requestid);
			
                if($status==1){
                    //request subject id
                    if($post->BorrowerData->inn=="" || $post->BorrowerData->series_of_passport=="" || $post->BorrowerData->number_of_passport==""  ){
                        $response['status'] = 400;
                        $response['data'] = array('error' => 'Some data empty in BorrowerData block');
                    }else{

                        $status = $Clientinfo->
                                    insertShortApplication(
                                                     $requestid
                                                    ,$post->BorrowerRequest->credit_term
                                                    ,$post->BorrowerRequest->amount_of_credit
                                                    ,$post->BorrowerData->inn
                                                    ,$post->BorrowerData->id_card_number
                                                    ,$post->BorrowerData->series_of_passport
                                                    ,$post->BorrowerData->number_of_passport
                                                    ,$post->User->first_name
                                                    ,$post->User->last_name
                                                    ,$post->User->patronymic
                                                    ,$post->User->tel_number
                                                      ,$post->User->email
													
                                                    );


if ($status=1) {
	 $response['status'] = 200;
	 $response['data'] = array('error' => '');
} else {
	 $response['status'] = 400;
                    $response['data'] = array('error' => 'Error create shortapplication');
}



                    }

                }else{
                    $response['status'] = 400;
                    $response['data'] = array('error' => 'Error save request');
                }

            }
        }
    } else {
        $response['status'] = 400;
        $response['data'] = array('error' => 'Error token');
    }
	 $Clientinfo->updateRequestApplications( $response['status'],$response['data']['error'],  $requestid);
}

// Return Response to browser
deliver_response($response);

?>