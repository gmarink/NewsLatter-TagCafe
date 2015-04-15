<?php
	
	$host = "";
	$dbname = "";
	$user = "";
	$pass = "";
	//filtrando o email retirando caracteres não aceitos
	$email = filter_var($_POST['signup-email'], FILTER_SANITIZE_EMAIL);
	$datetime = date('Y-m-d H:i:s');
	//testando a conexão com o banco de dados 
	try {
	$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
	
	if (empty($email)) {
	$status = "error";
	$message = "O email está incorreto, tente novamente";
	} else if (!preg_match('/^[^0-9][A-z0-9._%+-]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/', $email)) {
	$status = "error";
	$message = "Entre com um email v&aacute;lido";
	} else {
	$existingSignup = $db->prepare("SELECT COUNT(*) FROM signups WHERE signup_email_address='$email'");
	$existingSignup->execute();
	$data_exists = ($existingSignup->fetchColumn() > 0) ? true : false;
	
	if (!$data_exists) {
	$sql = "INSERT INTO signups (signup_email_address, signup_date) VALUES (:email, :datetime)";
	$q = $db->prepare($sql);
	$q->execute(
	array(
	':email' => $email,
	':datetime' => $datetime
	));
	
	if ($q) {
	$status = "success";
	$message = "Tudo deu certo, voc&ecirc; ser&aacute; avisado quando estivermos no ar!";
	} else {
	$status = "error";
	$message = "Ops, tente de novo!";
	}
	} else {
	$status = "error";
	$message = "Valeu, mas voc&ecirc; j&aacute; est&aacute; cadastrado!";
	}
	}
	
	$data = array(
	'status' => $status,
	'message' => $message
	);
	
	echo json_encode($data);
	
	$db = null;
	}
	catch(PDOException $e) {
	echo $e->getMessage();
	}
