<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="./content/main.css">
		<meta charset="UTF-8">
		<!--<script>
			function submit(){
				document.getElementById("theform").submit();
			}
		</script>-->
		<script>
			function successful(){
				alert("Inscreveu-se com sucesso nos leilões seleccionados.");
			}
			function failed_dates(){
				alert("Não é possível inscrever em leilões em dias diferentes. Seleccione leilões que ocorram no mesmo dia.");
			}
			function hard_fail(){
				alert("Ocorreu um erro ao fazer a inscrição no(s) leilão(ões).");
			}
		</script>
	</head>
	<body>
		<?php
			session_start();
			require_once("dbConst.php");
			if ($_SESSION['loggedin'] != 1) {
				header("Location: login.php");
				exit;
			}
			if($_SERVER['REQUEST_METHOD'] != 'POST'){
				header("Location: leiloes.php");
				exit;
			}

			$mynif = (int)$_SESSION['nif'];
			
			$connection = new_connection2();
			
			$first_val = 1;
			$accept_date = "";
			try{
				$error=0;
				foreach($_POST['choose'] as $chosen){
					$value = (int) $chosen;
					$sql = "SELECT dia FROM leilaor WHERE lid=$value";
					$result=$connection->prepare($sql);$result->execute();$result=$result->fetch(FETCH_ONE);
					$obtained = date_to_sql($result['dia']);
					if($first_val == 1){
						$accept_date = $obtained;
						$first_val = 0;
					}
					if(strcmp($obtained,$accept_date) != 0){
						echo "<script>failed_dates()</script>";
						$error=1;
						goto leave;
					}	
				}
				if($error==0) foreach($_POST['choose'] as $chosen){
					$value = (int) $chosen;
					$sql = "INSERT INTO concorrente VALUES ( $mynif, $value )";
					$result=$connection->prepare($sql);$result->execute();
				}
				
				echo "<script>successful()</script>";
			}catch(Exception $e){
				
				echo "<script>hard_fail()</script>";
			}
			
			leave:
			$connection = null;
		?>
		<script>window.history.back()</script>
		<!--<script>submit()</script>-->
	</body>
</html>