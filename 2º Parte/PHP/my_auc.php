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
				
				//$connection = new_connection();
				$connection = new_connection2();
				
				// Apresenta os leilões
				$sql = "SELECT l.nif, l.dia, l.nrleilaonodia, l.nome, l.tipo, l.valorbase
						FROM leilao l, leilaor lr, concorrente c
						WHERE l.dia=lr.dia
							AND l.nrleilaonodia=lr.nrleilaonodia
							AND l.nif=lr.nif
							AND lr.lid=c.leilao
							AND c.pessoa=$nif";
				//$result = $connection->query($sql);
				$result=$connection->prepare($sql);$result->execute();
				
				echo "<br><p class='grande'>Os meus leilões</p>";
				echo "<p class='pequeno'>Aqui pode ver os leilões em que está inscrito.<br>";
				echo "Clique em 'Ver' para ver o estado de um leilão.</p>";
				
				$idleilao = 0;
				$row = $result->fetchAll();
				$count = count($row);
				
				if($count == 0){
					echo "<div class='grande'>Não está inscrito em nenhum leilão.<br><a href='./leiloes.php'>Ver leilões em curso</a></div>";
					goto leave;
				}
				
				echo "<center><table border='1'>";
				echo "<tr><th style='width: 12.5%'>&nbsp;</th><th style='width: 12.5%'>NIF</th><th>Data</th><th style='width: 12.5%' class='pequeno'>N.º leilão dia</th><th style='width: 12.5%'>Nome</th><th style='width: 12.5%'>Tipo</th><th style='width: 12.5%'>Valor base</th><th class='pequeno' style='width: 12.5%'>Tempo remanescente</th></tr>";
				
				for($i=0; $i<$count; $i=$i+1){
					$nif = $row[$i]['nif'];
					$dia = $row[$i]['dia'];
					$nrleilaonodia = $row[$i]['nrleilaonodia'];
					
					$dia_str = date_to_sql($dia);
					
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
					echo "<td>$idleilao<br><a href='leilao.php?nif=$nif&dia=$dia&nrleilaonodia=$nrleilaonodia'>Ver</a><input type='hidden' id='e$idleilao' value='$estado'></td>";
					echo "<td>$nif</td>";
					echo "<td id='dia-$idleilao'>$dia</td>";
					echo "<td>$nrleilaonodia</td>";
					echo "<td>".$row[$i]["nome"]."</td>";
					echo "<td>".$row[$i]["tipo"]."</td>";
					echo "<td>".$row[$i]["valorbase"]."</td>";
					echo "<td id='rem-$idleilao' class='pequeno'>&nbsp;</td>";
					echo "<input type='hidden' id='nr$idleilao' value='$nrdias'></tr>";
				}
				echo "</table></center>";
				echo "<input type='hidden' id='nrleiloes' value='$idleilao'>";
				leave:
				$connection = null;
			?>
			<br><!--<div>Hora do servidor: <span id="curr_time">&nbsp;</span></div>-->
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
