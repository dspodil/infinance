<?php
error_reporting(0);
Class Clientinfo {
	
	public function __construct(){
		$this->db = $this->getDB();
	}

	// Connect Database
	private function getDB() {
		$dbhost="sprinfinancetest.cr7wmkiu4l9l.eu-central-1.rds.amazonaws.com";
		$dbuser="infinance";
		$dbpass="BV793hg2r";
		$dbname="engine_moneyboom";

		$dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); 
		$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbConnection->exec("set names utf8");

		return $dbConnection;
	}

	

	public function getToken($token){
        $sql = "SELECT * FROM tf_client_info WHERE id=?";
        $stmt = $this->db->prepare($sql); 
        $stmt->execute(array($idclient));
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
	}
	
	public function outputIntID($companySendId){
        $sql = "SELECT max(id) id FROM tf_subject_info WHERE companySendId=?";
        $stmt = $this->db->prepare($sql); 
        $stmt->execute(array($companySendId));
        $data = $stmt->fetch(PDO::FETCH_OBJ);
        return $data;
	}
	
	public function insertShortApplication($creationDate, $companySendId, $key){
        $sql = "INSERT INTO tf_subject_info (creationDate, companySendId,companySendDate,`key`) VALUES (?,?,?,?)";
        $stmt = $this->db->prepare($sql); 
        $status = $stmt->execute(array($creationDate, $companySendId, date('Y-m-d H:i:s'), $key));
        
		return $status;
	}

	
	public function insertLongApplication($data, $custVisAss, $surname, $name, $patronymic, $ucn, $birthDate, $sex, $serDoc, $numDoc, $dateIssDoc, $whoIssDoc, $numDocID, $whoIssDocID, $EDRRID, $dateIssDocID, $dateToID, $citizenship, $birthPlace, $familyStatus, $surnameMer, $firstnameMer, $patrNameMer, $socStatusMer, $birthDateMer, $mobPhoneMer, $education, $numOfDep, $totalChildren, $mobPhone, $eMail){
        $sql = "INSERT INTO tf_client_info (subjectId
										,custVisAss
										,surname
										,name
										,patronymic
										,ucn
										,birthDate
										,sex
										,serDoc
										,numDoc
										,dateIssDoc
										,whoIssDoc
										,numDocID
										,whoIssDocID
										,EDRRID
										,dateIssDocID
										,dateToID
										,citizenship
										,birthPlace
										,familyStatus
										,surnameMer
										,firstnameMer
										,patrNameMer
										,socStatusMer
										,birthDateMer
										,mobPhoneMer
										,education
										,numOfDep
										,totalChildren
										,mobPhone
										,eMail) 
										VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = $this->db->prepare($sql); 
        $status = $stmt->execute(array($data, $custVisAss, $surname, $name, $patronymic, $ucn, $birthDate, $sex, $serDoc, $numDoc, $dateIssDoc, $whoIssDoc, $numDocID, $whoIssDocID, $EDRRID, $dateIssDocID, $dateToID, $citizenship, $birthPlace, $familyStatus, $surnameMer, $firstnameMer, $patrNameMer, $socStatusMer, $birthDateMer, $mobPhoneMer, $education, $numOfDep, $totalChildren, $mobPhone, $eMail));
        
		return $status;
	}

	


}
?>