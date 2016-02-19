<?php
session_start();
require_once "funcions.php";
if (!isset($_SESSION['user'])) {
    echo "Pàgina no accessible! <br /><Strong> Error: </strong> Usuari sessió no trobat";
} else {
    $user     = $_SESSION['user'];
    $missatge = $_REQUEST["missatge"];
    $mysqli = conecta();
    $sql = "INSERT INTO MISSATGES (FK_ID_USUARI, MISSATGE) VALUES (?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("is", $user, $missatge) or die($mysqli->error . __LINE__);
    $stmt->execute();
    echo "Missagte enviat! <br />Usuari: <strong>$user</strong><br />Missatge: $missatge";
}
?>