<!DOCTYPE HTML>
<?php
	session_start();
	require_once("dbConst.php");
	$usernameErr = $pinErr = $required = $name = "";
	$pin = 0; 
	$correct = true;

	if(isset($_SESSION['loggedin'])){
		if($_SESSION['loggedin'] != 1){		//Se ainda nao estiver estiver loggado.
			
			/* Se for um post vindo do submit form. */
			if ($_SERVER["REQUEST_METHOD"] == "POST"){		
				$username = test_input($_POST["username"]);
				$pin = test_input($_POST["pin"]);
				
				$connection = new_connection2();
				$sql = "SELECT * FROM pessoa WHERE nif=$username";
				$result=$connection->prepare($sql);$result->execute();$row=$result->fetch(FETCH_ONE);
				if ($result){
					$safepin = $row["pin"];
					$nif = $row["nif"];
					if ($safepin == $pin ) {
						/*Passa para as variaveis da sessao.*/
						$_SESSION['username'] = $username; 
						$_SESSION['nif'] = $nif;
						$_SESSION['loggedin'] = 1;
						header("Location: my_auc.php");
					}else{
						$pinErr = "Pin Inválido";
					}
				}else{
					$usernameErr = "NIF Inválido";
				}	
				$connection = null;
			}
		}else{	//Se ja estiver loggado.
			header("Location: my_auc.php");
			exit;
		}
	}else $_SESSION['loggedin'] = 0;
?>
<html lang="pt-PT">
	<head>
		<title>Sistema de Leilões de Recursos Marítimos</title>
		<meta charset="UTF-8">
		<link rel="stylesheet" type="text/css" href="./content/main.css">
	</head>
	<body>
		<div class="wrap">
			<div class="titulo">Sistema de Leilões de Recursos Marítimos</div>
			<form id="Login" action="login.php" method="post">
				<span class="error">* Campo de preenchimento obrigatório</span><br>
				<span class="text">NIF:</span>&nbsp;<input type="number" placeholder="Introduza o seu NIF" name="username" required autofocus/>
				<span class="error">* <?php echo $usernameErr;?></span><br>
				<span class="text">PIN:</span>&nbsp;<input type="password" pattern="[0-9]+" placeholder="Introduza o seu PIN" name="pin" required/>
				<span class="error">* <?php echo $pinErr;?></span><br>
				<p><input type="submit" value="Entrar"/></p>
			</form>
			<br><div class="pequeno">Para uma melhor experiência, recomenda-se a utilização de <a href="https://www.mozilla.org/pt-PT/firefox/new/">Mozilla Firefox</a> ou <a href="https://www.google.com/chrome/browser/desktop/index.html">Google Chrome</a></div>
			<?php include("./content/footer.php"); ?>
		</div>
	</body>
</html>
