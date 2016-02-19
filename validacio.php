<?php
session_start();
require_once "funcions.php";
if (isset($_REQUEST["user"]) && isset($_REQUEST["password"])) {
    $user     = $_REQUEST["user"];
    $password = $_REQUEST["password"];
    $mysqli   = conecta();

    $sql  = "SELECT ID_USUARI FROM USUARIS WHERE NOM = ? AND PASSWORD = ?";
    $stmt = $mysqli->prepare($sql);

    $stmt->bind_param("ss", $user, $password) or die($mysqli->error . __LINE__);
    $stmt->execute();
    $stmt->bind_result($id);

    $myArray = array();

    $json = "[{\"process\":";
    $result = "\"failed\"}]";
    while ($stmt->fetch()) {
        $myArray[] = array("ID_USUARI" => $id);
        if ($id != null){ //Hem trobat un usuari
        	//Cream sessió de l'usuari
        	$_SESSION["user"] = $id;
        	$result = "\"success\"}]";
        }
    }
    $json .= $result;
    echo json_encode($json);
    //echo json_encode($myArray);

    $stmt->close();
    $mysqli->close();
} else {
    echo "<p><strong>Error: </strong>Sense parametres<br />";
    echo "<strong>Esperat: </strong>";
    echo "?user=\$user&password=\$password</p>";
}