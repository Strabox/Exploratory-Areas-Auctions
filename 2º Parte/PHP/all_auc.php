<!DOCTYPE HTML>
<?php 
	session_start();
	require_once("dbConst.php");
	if ($_SESSION['loggedin'] != 1) {
		header("Location: login.php");
		exit;
	}
	
	if(!isset($_SESSION['nome'])){
		set_session();
	}
	$name = $_SESSION['nome'];
	?>
<html lang="pt-PT">
	<head>
		<title>Sistema de Leilões de Recursos Marítimos</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="./content/main.css">
		<script src="funcs.js"></script>
	</head>
	<body>
		<div class="wrap">
			<div class="titulo">Sistema de Leilões de Recursos Marítimos</div>
			<?php include("./content/menu.php"); ?>
			
			<?php
				$username = $_SESSION['username']; 
				$nif = $_SESSION['nif'];
				
				$now = date('Y-m-d-H-i-s');
				echo "<input type='hidden' id='now' value='$now'>";
				
				$connection = new_connection2();
				
				// Apresenta os leilões
				$sql = "SELECT * FROM leilao"; 
				$result=$connection->prepare($sql);$result->execute();
				
				echo "<br><p class='grande'>Todos os leilões</p>";
				echo "<p class='pequeno'>Aqui pode ver todos os leilões existentes.<br>";
				echo "Clique em 'Ver' para ver o estado de um leilão.<br>";
				echo "Seleccione os leilões em que pretende concorrer e clique no botão.</p>";
				
				$idleilao = 0;
				$row = $result->fetchAll();
				$count = count($row);
				
				if($count == 0){ // se não houverem leilões não imprime a tabela, mas antes uma mensagem de aviso
					echo "<div class='grande'>Não existem leilões para mostrar.</div>";
					goto leave;
				}
				
				echo "<center>";
				echo "<form action='inscricao.php' method='post'><input type='submit' value='Concorrer aos leilões seleccionados'>";
				echo "<table border='1'>";
				echo "<tr><th style='width: 12.5%'>&nbsp;</th><th style='width: 12.5%'>NIF</th><th>Data</th><th style='width: 12.5%' class='pequeno'>N.º leilão dia</th><th style='width: 12.5%'>Nome</th><th style='width: 12.5%'>Tipo</th><th style='width: 12.5%'>Valor base</th><th class='pequeno' style='width: 12.5%'>Tempo remanescente</th></tr>";
				
				for($i=0; $i<$count; $i=$i+1){
					$dia = $row[$i]['dia'];
					$nrleilaonodia = $row[$i]['nrleilaonodia'];
					$nif = $row[$i]['nif'];
					$dia_str = date_to_sql($dia);
					
					$sql = "SELECT lid
							FROM leilaor
							WHERE nif=$nif
								AND nrleilaonodia=$nrleilaonodia
								AND dia=$dia_str";
					$result=$connection->prepare($sql);$result->execute();$result=$result->fetch(FETCH_ONE);
					$lid=$result['lid'];
					
					$sql = "SELECT nrdias
							FROM leilaor
							WHERE dia=$dia_str
								AND nrleilaonodia=$nrleilaonodia
								AND nif=$nif";
					$result=$connection->prepare($sql);$result->execute();$row_dias=$result->fetch(FETCH_ONE);
					$nrdias = (int) $row_dias['nrdias'];
					
					$estado = get_auction_status($dia,$nrdias);
					
					$idleilao = $i + 1;
					echo "<tr>";
					echo "<td>$idleilao<br><a href='leilao.php?nif=$nif&dia=$dia&nrleilaonodia=$nrleilaonodia'>Ver</a><br>";
					echo "<input type='checkbox' name='choose[]' value='$lid' ";
					if($estado == 1)
						echo "disabled title='Este leilão já terminou'";
					echo "><input type='hidden' id='e$idleilao' value='$estado'></td>";
					echo "<td>$nif</td>";
					echo "<td id='dia-$idleilao'>$dia</td>";
					echo "<td>$nrleilaonodia</td>";
					echo "<td>".$row[$i]["nome"]."</td>";
					echo "<td>".$row[$i]["tipo"]."</td>";
					echo "<td>".$row[$i]["valorbase"]."</td>";
					echo "<td id='rem-$idleilao' class='pequeno'>&nbsp;</td>";
					echo "<input type='hidden' id='nr$idleilao' value='$nrdias'></tr>";
				}
				echo "</table>";
				echo "<input type='submit' value='Concorrer aos leilões seleccionados'></form>";
				echo "</center><input type='hidden' id='nrleiloes' value='$idleilao'>";
				leave:
				$connection = null;
			?>
			<br>
			<?php include("./content/footer.php"); ?>
		</div>
		<script>
			read_server_time();
			var nr = parseInt(document.getElementById("nrleiloes").value);
			setInterval(function(){refresh_time()}, 1000);
			setInterval(function(){update_times(nr)}, 1000);
			clear_checks();
		</script>
	</body>
</html>
