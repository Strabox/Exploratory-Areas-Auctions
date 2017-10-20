<!DOCTYPE HTML>
<html lang="pt-PT">
	<head>
		<link rel="stylesheet" type="text/css" href="./content/main.css">
		<meta charset="UTF-8">
		<script charset="utf-8">
			function bid_success(){
				alert("O lance foi efectuado com sucesso");
			}
			function bid_failure(){
				alert("O lance não foi efectuado com sucesso. Provavelmente alguém fez um lance de valor igual ou superior ou o valor oferecido é inferior ao valor base. Tente novamente");
			}
			function bid_no_sign(){
				alert(O lance não foi efectuado com sucesso. Deve primeiro inscrever-se no leilão antes de poder fazer um lance");
			}
			function bid_hard_failure(){
				alert("Ocorreu um erro ao fazer o lance. Tente novamente");
			}
		</script>
	</head>
	<body>
	<?php
		$success = 1;
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
		
		$valor = $_POST['valorlance'];
		$mynif = $_SESSION['nif'];
		$lid = $_POST['lid'];
		
		$connection = new_connection2();
		
		$sql = "SELECT nif, dia, nrleilaonodia FROM leilaor WHERE lid=$lid";
		$resultado = $connection->prepare($sql);
		$resultado->execute();$resultado = $resultado->fetch(FETCH_ONE);
		
		$nif = (int)$resultado['nif'];
		$dia = $resultado['dia'];
		$nrleilaonodia = (int) $resultado['nrleilaonodia'];
		
		$dia_search = date_to_sql($dia);
		
		$sql = "SELECT leilao FROM concorrente WHERE pessoa=$mynif";
		$resultado=$connection->prepare($sql);$resultado->execute();$resultado=$resultado->fetchAll();
		
		$found = 0;
		foreach($resultado as $valor_arr){
			$leilei = $valor_arr['leilao'];
			if($leilei == $lid){
				$found = 1;
				break;
			}
		}
		
		if($found == 0){
			echo "<script>bid_no_sign()</script>";
			goto leave;
		}
		
		try{
			$connection->beginTransaction();
			
			// verificar se entretanto já foi feito um lance de valor igual ou superior
			// tem que fazer também a comparação com o valor base porque o browser pode não ter o javascript a funcionar como deve ser (ou porque pode ignorar as restrições do HTML5 e fazer submit do formulário quando se carrega no enter, *cof* Internet Explorer *cof*)
			
			$sql = "SELECT valorbase FROM leilao WHERE dia=$dia_search AND nrleilaonodia=$nrleilaonodia AND nif=$nif";
			$resultado2=$connection->prepare($sql);$resultado2->execute();$resultado2=$resultado2->fetch(FETCH_ONE);
			$resultado2=(int)$resultado2["valorbase"];
			
			$sql = "SELECT MAX(valor) AS ultimo_lance FROM lance WHERE leilao = $lid";
			$resultado=$connection->prepare($sql);$resultado->execute();$resultado=$resultado->fetch(FETCH_ONE);
			$resultado=(int)$resultado["ultimo_lance"];
			
			if($resultado >= $valor || $resultado2 > $valor){
				$success = 0;
				$connection->rollback();
			}
				
			if($success == 1){
				$sql = "INSERT INTO lance VALUES ($mynif, $lid, $valor)";
				$resultado = $connection->prepare($sql);
				$resultado->execute();
				$connection->commit();
			}
		}catch(Exception $e){
			$connection->rollback();
			$success = -1;
		}
		
		echo "<script>";
		if($success == 1) echo "bid_success()";
		else if($success == -1) echo "bid_hard_failure()";
		else echo "bid_failure()";
		echo "</script>";
		
		leave:
		$connection = null;
		echo "<script>window.history.back()</script>";
	?>
	</body>
</html>