<?php
	session_start();
	$name = $_SESSION['nome'];
	echo "<div class='barra_nome'>";
		echo "<div style='float: left'>";
			echo "<a href='./my_auc.php'>Os meus leilões</a>";
			echo "<a href='./leiloes.php'>Leilões em curso</a>";
			echo "<a href='./all_auc.php'>Todos os leilões</a>";
			echo "<a href='./Logout.php'>Terminar sessão</a>";
		echo "</div>\n";
		echo "<div style='text-align: right; padding-right: 12px;'>Bem-vindo(a), $name</div>";
	echo "</div>";
?>