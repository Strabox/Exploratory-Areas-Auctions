<!DOCTYPE HTML>
<?php
	session_start();
	
	if ($_SESSION['loggedin'] == 1) {
		header("Location: home.php");
		exit;
	}
?>
<html lang="pt-PT">
	<head>
		<title>Sistema de Leilões de Recursos Marítimos</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="./content/main.css">
		<script>
			function check_pin(){
				pin1 = document.getElementById("pin1").value;
				pin2 = document.getElementById("pin2").value;
				if(pin1 != pin2){
					document.getElementById("error").innerHTML = "Os PINs introduzidos não são iguais";
					document.getElementById("ok").disabled = true;
					return;
				}
				document.getElementById("error").innerHTML = "&nbsp;";
				document.getElementById("ok").disabled = false;
			}
			
			function clean(){
				console.log("clean");
				document.getElementById("nif").value = "";
				document.getElementById("name").value = "";
				document.getElementById("pin1").value = "";
				document.getElementById("pin2").value = "";
				check_pin();
			}
		</script>
	</head>
	<body>
		<div class="wrap">
			<div class="titulo">Sistema de Leilões de Recursos Marítimos</div>
			<center><table border='0'>
				<form name="registo" method="post">
					<tr><td style="text-align: right;"><span class="text">NIF:</span></td><td><input type="number" id="nif" name="nif" placeholder="NIF" style="width: 100%;" required autofocus></td></tr>
					<tr><td style="text-align: right;"><span class="text">Nome:</span></td><td><input type="text" id="name" name="nome" placeholder="Nome" required></td></tr>
					<tr><td style="text-align: right;"><span class="text">PIN:</span></td><td><input type="password" id="pin1" name="pin1" onblur="check_pin()" required></td></tr>
					<tr><td style="text-align: right; width: 150px;"><span class="text">Repetir PIN:</span></td><td><input type="password" id="pin2" name="pin2" onblur="check_pin()" required></td></tr>
					<tr><td colspan='2'><input type="button" value="Cancelar" onClick="window.location.href = './login.php'">&nbsp;&nbsp;<input type="button" value="Limpar formulário" onClick="clean()">&nbsp;&nbsp;<input type="submit" id="ok" value="Registar"></td></tr>
				</form>
			</table></center>
			<div class="error" id="error">&nbsp;</div>
			<?php include("./content/footer.php"); ?>
		</div>
	</body>
</html>
