<?php
function conecta(){
//conectarse a la base de datos
return $mysqli = new mysqli("localhost", "root", "godevil90qwe", "MISSATGERIA");
	if (mysqli_connect_errno()) {
		printf("Error: %s\n", mysqli_connect_error()); exit();
	}
}

function consulta($connexio, $sql){
	return $connexio->query($sql);
}


?>