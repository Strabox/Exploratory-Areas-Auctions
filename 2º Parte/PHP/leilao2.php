<!DOCTYPE HTML>
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
?>
<html lang="pt-PT">
	<head>
		<title>Sistema de Leilões de Recursos Marítimos</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="./content/main.css">
		<script>
			var time = 30;
			var activated;
			function check_bid(){
				base = false;
				lance = document.getElementById("valorlance").value;
				ultimo = document.getElementById("ultimolance").innerHTML;
				if(ultimo=="nenhum"){
					ultimo = document.getElementById("valorbase").innerHTML;
					base = true;
				}
				ultimo = parseFloat(ultimo);
				if(lance<=ultimo && !base){
					document.getElementById("proibido").innerHTML = "Não é permitido fazer um<br>lance igual ou inferior ao último.";
					document.getElementById("fazerlance").disabled = true;
				}else if(lance<ultimo && base){
					document.getElementById("proibido").innerHTML = "Não é permitido fazer um<br>lance inferior ao valor base.";
					document.getElementById("fazerlance").disabled = true;
				}else{
					document.getElementById("proibido").innerHTML = "&nbsp;<br>&nbsp;";
					document.getElementById("fazerlance").disabled = false;
				}
			}
			function refresh_pg(){
				if(!activated) return;
				time--;
				if(time == 0){
					document.getElementById("updateform").submit();
				}
				string = "Actualização automática em:<br>" + time;
				if(time == 1) string = string + " segundo";
				else string = string + " segundos";
				document.getElementById("actsec").innerHTML = string;
			}
			function update_auto(){
				element = document.getElementById("act_auto");
				activated = !element.checked;
				return activated;
			}
		</script>
	</head>
	<body>
		<div class="wrap">
			<div class="titulo">Sistema de Leilões de Recursos Marítimos</div>
			<?php include("./content/menu.php"); ?>
			
			<?php
				error_reporting(E_ALL);
				ini_set('display_errors', 'on');

				$file = substr($_SERVER["HTTP_REFERER"],-10,10); // obtém o nome do ficheiro que fez este pedido POST (a contar que seja o "leilao.php")
				if(isset($_POST['act_auto']))
					$_SESSION['act_auto'] = $_POST['act_auto'];
				else if(!isset($_SESSION['act_auto']))
					$_SESSION['act_auto'] = 0;
				else if(strcmp($file,"leilao.php")==0)
					$_SESSION['act_auto'] = 0;

				$nif = (int)$_POST['nif'];
				$dia = $_POST['dia'];
				$nrleilaonodia = $_POST['nrleilaonodia'];
				$mynif = $_SESSION['nif'];
				
				$connection = new_connection2();
				
				$dia_search = date_to_sql($dia);
				$sql = "SELECT nome, tipo, valorbase
						FROM leilao
						WHERE nif=$nif
							AND nrleilaonodia=$nrleilaonodia
							AND dia=$dia_search";
				$result=$connection->prepare($sql);$result->execute();$result=$result->fetchAll();
				$nomeleilao=$result[0]['nome'];$tipo=(int)$result[0]['tipo'];$valorbase=(int)$result[0]['valorbase'];
				
				$sql = "SELECT lid, nrdias
						FROM leilaor
						WHERE nif = $nif
							AND nrleilaonodia = $nrleilaonodia
							AND dia = $dia_search";
				$result=$connection->prepare($sql);$result->execute();$result=$result->fetchAll();
				$lid=(int)$result[0]['lid'];$nrdias=(int)$result[0]['nrdias'];
				echo "<br><div class='grande'>Leilão $lid - $nomeleilao</div>\n\n";
				echo "<div>\n";
				
				// informação lateral
				
				$dia_data = new DateTime($dia);
				$hoje = new DateTime("now");
				$diff = $hoje->diff($dia_data);
				$terminado = (int)$diff->format("%r1");
				
				$sql = "SELECT pessoa FROM concorrente WHERE pessoa=$mynif AND leilao=$lid";
				$result=$connection->prepare($sql);$result->execute();$result=$result->fetch(FETCH_ONE);
				echo "<div class='left'>Estado: ";
				if(empty($result)) $insc = 0; else $insc = 1;
				if($insc==0) echo "<span style='color: red'>não inscrito</span>";
				else echo "<span style='color: green'>inscrito</span>";
				echo "<form action='inscricao.php' method='post'><!--<input type='submit' value='Inscrever' ";
				if($insc==1) echo "disabled";
		//		else if($terminado==-1) echo "disabled title='Este leilão já terminou'";
				echo ">--><input type='hidden' name='lid' value='$lid'></form>";
				echo "<br><br>Lances<br>\n";
				echo "<form action='lance.php' method='post'><input type='number' name='valorlance' id='valorlance' placeholder='Valor do lance' oninput='check_bid()' ";
				if($insc==0) echo "disabled";
		//		else if($terminado==-1) echo "disabled";
				else echo "autofocus";
				echo ">&nbsp;&nbsp;<input type='submit' value='Fazer lance' id='fazerlance' ";
				if($insc==0) echo "title='Deve inscrever-se antes de poder fazer lances' disabled";
		//		else if($terminado==-1) echo "title='Este leilão já terminou' disabled";
				echo "><input type='hidden' name='lid' value='$lid'></form><br>";
				echo "<div class='pequeno' style='color: red' id='proibido'>";
				if($terminado==-1) echo "Este leilão já terminou";
				else echo "&nbsp;";
				echo "<br>&nbsp;</div>";
				echo "<form action='leilao.php' method='post' id='updateform'><input type='hidden' name='nif' value='$nif'>";
				echo "<input type='hidden' name='dia' value='$dia'><input type='hidden' name='nrleilaonodia' value='$nrleilaonodia'><input type='submit' value='Actualizar página'><br><input type='checkbox' name='act_auto' id='act_auto' value='1' onclick='update_auto()' ";
				if(isset($_SESSION['act_auto'])){
					$act_auto = (int) $_SESSION['act_auto'];
					if($act_auto == 1) echo "checked";
					else $_SESSION['act_auto'] = 0;
				}else $_SESSION['act_auto'] = 0;
				echo "><span class='pequeno'>&nbsp;Desactivar actualização automática</span></form>";
				echo "<div class='pequeno' id='actsec'>&nbsp;</div><br>";
				echo "</div>";
				
				// informação sobre o leilão
				
				echo "<center><table border='0' width='55%'>";
				echo "<tr><td width='20%' style='text-align: right'>Data:</td><td width='80%' style='text-align: left'>$dia</td></tr>";
				echo "<tr><td style='text-align: right'>N.º dias:</td><td style='text-align: left'>$nrdias</td></tr>";
				echo "<tr><td style='text-align: right'>LID:</td><td style='text-align: left'>$lid</td></tr>";
				echo "<tr><td style='text-align: right'>N.º leilão dia:</td><td style='text-align: left'>$nrleilaonodia</td></tr>";
				echo "<tr><td style='text-align: right'>NIF:</td><td style='text-align: left'>$nif</td></tr>";
				echo "<tr><td style='text-align: right'>Nome:</td><td style='text-align: left'>$nomeleilao</td></tr>";
				echo "<tr><td style='text-align: right'>Tipo:</td><td style='text-align: left'>$tipo</td></tr>";
				echo "<tr><td style='text-align: right'>Valor base:</td><td style='text-align: left' id='valorbase'>$valorbase</td></tr>";
				
				$sql = "SELECT MAX(valor) AS ultimolance FROM lance WHERE leilao=$lid";
				$result=$connection->prepare($sql);$result->execute();$result=$result->fetch(FETCH_ONE);
				
				echo "<tr><td style='text-align: right'>Último lance:</td><td style='text-align: left' id='ultimolance'>";
				$result=$result['ultimolance'];
				if(empty($result)) echo "nenhum";
				else{
					echo $result;
					$sql = "SELECT p.nome
							FROM pessoa p, lance l
							WHERE l.pessoa = p.nif
								AND l.valor = $result
								AND l.leilao = $lid";
					//$sql = "SELECT p.nome FROM pessoa p, lance l, leilao lei WHERE l.pessoa = p.nif AND l.valor = $result AND l.leilao = $lid AND lei.dia=$dia_search AND lei.nrleilaonodia=$nrleilaonodia";
					$result=$connection->query($sql);$result=$result->fetch(FETCH_ONE);$result=$result['nome'];
					echo " ($result)";
				}
				echo "</td></tr>";
				echo "</table></center></div>";
				
				$connection = null;
			?>
			<?php include("./content/footer.php"); ?>
		</div>
		<script>setInterval(function(){refresh_pg()}, 1000);update_auto();</script>
	</body>
</html>
