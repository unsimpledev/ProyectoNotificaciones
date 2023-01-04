<?php

include_once 'headers.php';
include_once 'configuracion.php';

class RegistroResultado {
    public $code = "";
    public $message = "";
    public $id = 0;
}

$response = new RegistroResultado;
$response->code = "OK";
$response->message = "";
$response->id = 0;
try{

     //***Verificacion de campos
     if (isset($_POST['DEVICEID'])) {
        $deviceid = $_POST['DEVICEID'];

        if($deviceid==NULL) {
            $response->code = "ERR";
            $response->message = "Token Nulo";
            $response->id = 0;

        }else{

            $id = null;
            if (isset($_POST['ID'])) {
                $id = $_POST['ID'];
            }

            //registro en la DB el token y el estado
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
            
            if ($id != null){
                $stmt = $mysqli->prepare("UPDATE DISPOSITIVOS SET DEVICEID = ? WHERE ID =  ?");
                $stmt->bind_param("si", $deviceid, $id);
                $resultado1 = $stmt->execute();
                $stmt->close();
            }else{
                $stmt = $mysqli->prepare("INSERT INTO DISPOSITIVOS (DEVICEID) VALUES (?)");
                $stmt->bind_param("s", $deviceid);
                $resultado1 = $stmt->execute();
                $response->id = $mysqli->insert_id;
                $stmt->close();
            }
            $mysqli -> close();
        }
     }else{
        $response->code = "ERR";
        $response->message = "Faltan campos";
        $response->id = 0;
    }

} catch(Exception $e1) {
    $response->code = "ERR";
    $response->message = "Error";
    $response->id = 0;
}

$myJSON = json_encode($response);
echo $myJSON;



?>