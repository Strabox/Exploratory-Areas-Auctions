<?php
	const DB_HOST = "db.ist.utl.pt";
	//const DB_HOST = "bucho.pt";
	const DB_USER = "ist169537";
	//const DB_USER = "testes";
	const DB_PASS = "jrru2095";
	//const DB_PASS = "BatataFrita";
	const DB_NAME = 'ist169537';
	//const DB_NAME = 'testes';
	
	const FETCH_ONE = PDO::FETCH_ASSOC;
	const ONE_UNIX_DAY = 86400;
	
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	mb_internal_encoding("UTF-8");
	mb_http_output("UTF-8");
	
	// Função para limpar os dados de entrada
	function test_input($data) {
		 $data = trim($data);
		 $data = stripslashes($data);
		 $data = htmlspecialchars($data);
		 return $data;
	}
	
	function date_to_sql($data){
		return str_replace("-","",$data);
	}
	
	function new_connection(){
		$dbhost = DB_HOST;
		$dbuser = DB_USER;
		$dbpass = DB_PASS;
		$dbname = DB_NAME;
		
		$connection = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
		mysqli_set_charset ($connection, "UTF-8");
		return $connection;
	}
	
	function new_connection2(){
		$dbhost = DB_HOST;
		$dbuser = DB_USER;
		$dbpass = DB_PASS;
		$dbname = DB_NAME;
		
		$first_part = "mysql:host=$dbhost;dbname=$dbname";
		return new PDO($first_part, $dbuser, $dbpass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	}
	
	function set_session(){
		$connection = new_connection2();
		$nif = $_SESSION['nif'];
		$sql = "SELECT nome FROM pessoa WHERE nif=$nif";
		$name = $connection->prepare($sql);
		$name->execute();
		$name=$name->fetch(FETCH_ONE);
		$name = $name['nome'];
		$_SESSION['nome'] = $name;
		$connection = null;
	}
	
	function get_auction_status($dia,$nrdias){ // dia -> data do leilao
		$data_inicio = strtotime($dia);						// data unix do leilão
		$data_fim = $data_inicio + $nrdias * ONE_UNIX_DAY;	// data unix do fim do leilão
		$agora = time();									// data unix do momento presente
		
		$diff1 = $agora - $data_inicio;
		$diff2 = $agora - $data_fim;
		
		// se diff1 && diff2 < 0 o leilão ainda não começou
		// se diff1 > 0 && diff2 < 0 o leilão está a decorrer
		// se diff1 && diff2 > 0 o leilão já terminou
		// diff1 < 0 && diff2 > 0 não faz sentido (o leilão já teria acabado sem ter começado :/ )
		
		$estado = NAN; // estado = ∞
		
		if($diff1 < 0 && $diff2 < 0) $estado = -1;
		else if($diff1 > 0 && $diff2 < 0) $estado = 0;
		else if($diff1 > 0 && $diff2 > 0) $estado = 1;
		
		// estado = -1 -> ainda não começou
		// estado = 0  -> está a decorrer
		// estado = 1  -> já acabou
		
		return $estado;
	}
?>
