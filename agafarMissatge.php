<?php
session_start();
require_once "funcions.php";
if (!isset($_SESSION['user'])) {
    echo "Pàgina no accessible! <br /><Strong> Error: </strong> Usuari sessió no trobat";
} else {
    $user   = $_SESSION['user'];
    $mysqli = conecta();
    $sql    = "SELECT MISSATGE, DATA, NOM FROM USUARIS, MISSATGES";
    $sql .= " WHERE DATA > (SELECT DARRER_MISSATGE FROM USUARIS WHERE ID_USUARI = ?) AND ID_USUARI = FK_ID_USUARI";

    $stmt = $mysqli->prepare($sql);

    $stmt->bind_param("i", $user) or die($mysqli->error . __LINE__);
    $stmt->execute();
    $stmt->bind_result($missatge, $data, $nom);

    $array1   = array();
    $last_date = null;

    while ($stmt->fetch()) {
        $nom_fmt   = ucwords(strtolower($nom));
        $last_date = $data;
        $array1[] = array("MISSATGE" => $missatge, "DATA" => $data, "NOM" => $nom_fmt);
    }
    $primerArray = array('MISSATGES' => $array1);



    $array2   = array();

    $users = "SELECT NOM FROM USUARIS WHERE TIMESTAMPDIFF(SECOND, ONLINE, NOW()) < 5";
    $stmt = $mysqli->prepare($users);

    //$stmt->bind_param("i", 5) or die($mysqli->error . __LINE__);
    $stmt->execute();
    $stmt->bind_result($nom);


    while ($stmt->fetch()) {
        $nom_fmt   = ucwords(strtolower($nom));
        $array2[] = array("ONLINE_USER" => $nom_fmt);
    }
        $segonArray = array('USUARIS' => $array2);


    $result = array_merge($primerArray, $segonArray);

    echo json_encode($result);

    if ($last_date != null) {
        $update = "update USUARIS set DARRER_MISSATGE = ? WHERE ID_USUARI = ?";
        $stmt   = $mysqli->prepare($update);

        $stmt->bind_param("si", $last_date, $user) or die($mysqli->error . __LINE__);
        $stmt->execute();

    }

    $online = "update USUARIS set ONLINE = NOW() WHERE ID_USUARI = ?";
    $stmt   = $mysqli->prepare($online);

    $stmt->bind_param("i", $user) or die($mysqli->error . __LINE__);
    $stmt->execute();

    $stmt->close();
    $mysqli->close();

}
