<?php 
require_once 'lib/vendor/autoload.php';
include_once 'headers.php';
include_once 'configuracion.php';

class EnvioNotificacionResultado {
    public $code = "";
    public $message = "";
}

$response = new EnvioNotificacionResultado;
$response->code = "OK";
$response->message = "Enviado correctamente";

//API V1 (NEW)
function getAccessToken(){
    $client = new Google\Client();
    $client->setAuthConfig('DIRCREDENCIALES/service-account.json');
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    $accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];
    return $accessToken;
}

try{

     //***Verificacion de campos
     if (isset($_POST['ID']) && isset($_POST['TITULO']) 
        && isset($_POST['MENSAJE'])) {
        $id = $_POST['ID'];
        $titulo = $_POST['TITULO'];
        $mensaje = $_POST['MENSAJE'];

        if($id==NULL || $titulo==NULL || $mensaje==NULL) {
            $response->code = "ERR";
            $response->message = "Faltan parametro";
        }else{

            $registatoin_ids = array();
            $datos = array();

            //busco en la DB el id
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

            $stmt = mysqli_prepare($mysqli, "select DEVICEID from DISPOSITIVOS WHERE ID = ?");
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);
            if ($resultado = mysqli_stmt_get_result($stmt)) {
                while ($fila = mysqli_fetch_assoc($resultado)){
                    if ($fila["DEVICEID"]!= null){
                        $registatoin_ids[] = $fila["DEVICEID"];

                    }
                }
                $stmt->close();
            }
            $mysqli->close();
            $notification= array();	
            $notification["title"] = $titulo;
            $notification["body"] = $mensaje;

            //API LEGACY (OLD)
            /*$fields = array(
                'registration_ids' => $registatoin_ids,
                'notification' => $notification,
                //'data' => $datos,
                'direct_book_ok' => true
            );*/

            //API V1 (NEW)
            $fields["message"] = array(
                'token' => $registatoin_ids[0],
                'notification' => $notification,
                //'data' => $datos,
            );

            //API LEGACY (OLD)
            //$url = 'https://fcm.googleapis.com/fcm/send';
            // Your Firebase Server API Key
            //$headers = array( "authorization: key=".FCM_APIKEY.""
            //,"content-type: application/json");

            //API V1 (NEW)
            $url = 'https://fcm.googleapis.com/v1/projects/15245642154521/messages:send';            
            $accessToken = getAccessToken();
            $headers = array( "Authorization: Bearer ".$accessToken.""
            ,"content-type: application/json;UTF-8");


            // Open curl connection
            $ch = curl_init();
            // Set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            if ($result === FALSE) {
                // die('Curl failed: ' . curl_error($ch));
            }
            curl_close($ch);
        }
    }else{
        $response->code = "ERR";
        $response->message = "Faltan campos";
    }

} catch(Exception $e1) {
    $response->code = "ERR";
    $response->message = "Error";
}
    
$myJSON = json_encode($response);
echo $myJSON;

?>