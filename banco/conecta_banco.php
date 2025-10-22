<?php  
	$domain="localhost";// localização
	$user="root";		// usuário
	$password="";		// senha
	$database="agronegocio";	// banco de dados	

	// instanciando a classe mysqli
	$mysqli = new mysqli($domain,$user,$password,$database);
	$mysqli->set_charset("utf8");  //caracter banco
	
	if($mysqli->connect_errno){
		echo "Não foi possível conectar com o banco de dados ";
		echo $mysqli->connect_error; 
	} // mostra mensagem em caso de erro
?>