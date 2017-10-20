<?php
	$connection = new mysqli("db.tecnico.ulisboa.pt","ist169537","jrru2095","ist169537");
	
	$sql = "select dia from leilao where nrleilaonodia=1 and nif=50124 and valorbase=10 and tipo=1 and dia=20141001";
	$result = $connection->query($sql);
	$result = $result->fetch_assoc();
	
	$data = $result['dia'];
	var_dump($result);
	echo "$result<br>";
	var_dump($data);
	echo "data: $data<br>";
	
	$data=str_replace("-","",$data);
	var_dump($data);
	echo "substituido: $data";
	
	mysqli_close($connection);
?>
