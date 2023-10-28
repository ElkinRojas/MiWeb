<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

if (is_ajax()) {
    include_once('../classes/Pina.php');
    include_once('../classes/Connection.php');
    include_once('../classes/httpPHPAltiria.php');

    $pina = new Pina();
    $link = new Connection();
    $con = $link->connect();

    // VOTES CHART
    if ($_POST['action'] === "chart" && $_POST['table'] === "canvas") {
        $colors = ['#1BA8B8', '#F1DE77', '#38B4FD', '#E64C2C', '#929A97', '#C7C2F5', '#81BA67', '#CD80AB', '#333C72', '#B87A43'];
        $color = 0;

        $restaurants = [1,2,3,4,5];

        $data = array();
        $data['labels'] = array();
        $data['datasets'] = array();

        $dataset['data'] = array();
        $dataset['backgroundColor'] = array();

        $result = array();

        foreach ($restaurants as $number) {
            $votes_count_list = $pina->get_count_votes($con, array("number"=>$number));

            foreach ($votes_count_list as $value_votes) {
                array_push($result, $value_votes);
            }
        }

        foreach ($result as $value ) {
            array_push($dataset['data'], intval($value['mi_contador']));

            if ($color == count($colors)) {
                $color = 0;
            }
            array_push($dataset['backgroundColor'], $colors[$color++]);

            array_push($data['labels'], mb_strtoupper($value['name_restaurante'], 'UTF-8') );
        }

        array_push($data['datasets'], $dataset);

        unset($_POST);

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    // NEW VOTE
    elseif ( $_POST['action'] === "send" && $_POST['table'] === "vote" ) {        
        $json['is_success'] = 0;
        $validation = true;

        $number_verification = $pina->get_info_verification($con, array("phone"=>$_POST['phone'], "status"=>1));

        if ($number_verification === "false") {
            $validation = false;
        }

        if ($validation) {
            $_POST['ip'] = $_POST['phone'];
            //verifacion de code 
            $code_verification = $pina->get_info_verification($con, array("phone"=>$_POST['phone'], "status"=>0));
            if ( $code_verification['code'] == $_POST['code'] ) {
                if ( $pina->set_vote($con, $_POST) ) {
                    if ( $pina->update_verification($con, array("phone"=>$_POST['phone'], "status"=>1)) ) {
                        $json['is_success'] = 1;
                    } else {
                        $json['message'] = "Error enviando a BD";
                    }
                } else {
                    $json['message'] = "Error enviando tu voto";
                }
            } else {
                $json['message'] = "El código de verificación es incorrecto";
            }
        } else {
            $json['message'] = "Solo se puede enviar un voto por dispositivo";
        }

        unset($_POST);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

    // SEND VALIDATION MESSAGE
    elseif ( $_POST['action'] === "send" && $_POST['table'] === "code" ) {
        $altiriaSMS = new AltiriaSMS(); 
        $validation = true;

        $number_verification = $pina->get_info_verification($con, array("phone"=>$_POST['phone'], "status"=>1));

        if ($number_verification !== false) {
            $validation = false;
        }

        if ($validation) {
            $altiriaSMS->setLogin('villabonaelkin.98@gmail.com');
            $altiriaSMS->setPassword('nyr95nny');
            // Descomentar para utilizar la autentificaci�n mediante apikey
            //$altiriaSMS->setApikey('YY');
            //$altiriaSMS->setApisecret('ZZ');
            $altiriaSMS->setDebug(true);

            //Use this ONLY with Sender allowed by altiria sales team
            //$altiriaSMS->setSenderId('TestAltiria');
            //Concatenate messages. If message length is more than 160 characters. It will consume as many credits as the number of messages needed
            //$altiriaSMS->setConcat(true);
            //Use unicode encoding (only value allowed). Can send ����� but message length reduced to 70 characters
            //$altiriaSMS->setEncoding('unicode');

            //$sDestination = '346xxxxxxxx';
            $sDestination = '57'. $_POST['phone'];
            //$sDestination = array('346xxxxxxxx','346yyyyyyyy');
            $code_random = rand(0,10000);

            $number_verification2 = $pina->get_info_verification($con, array("phone"=>$_POST['phone'], "status"=>0));

            if ( $number_verification2 !== false ) {
                $pina->delete_verification($con, $_POST['phone']);
            } 
                
            $response = $altiriaSMS->sendSMS($sDestination, "Código de verificación: " . $code_random);

            if (!$response) {
                echo "El envio ha terminado en error";
            } else {
                $data_array = array(
                    "phone"=>$_POST['phone'],
                    "code"=>$code_random,
                    "status"=>"0"
                );

                $pina->set_verification($con, $data_array);

                echo 1;
            }
            
        } else {
            echo "false";
        }
    }

    // NEW VOTE
    elseif ( $_POST['action'] === "send" && $_POST['table'] === "email" ) {        
        $json['is_success'] = 0;
        
        try {
            // Contenido del correo
            $name = $_POST["name"];
            $email = $_POST["email"];
            $subject = $_POST["subject"];
            $message = $_POST["message"];
            $para = "elkin.villaroja.15@gmail.com";
       
            if (!filter_var($para, FILTER_VALIDATE_EMAIL)) {
              throw new Exception('Dirección de correo electrónico no válida.');
            }

            require '../../assets/vendor/PHPMailer/src/Exception.php';
            require '../../assets/vendor/PHPMailer/src/PHPMailer.php';
            require '../../assets/vendor/PHPMailer/src/SMTP.php';
       
            // Intancia de PHPMailer
            $mail = new PHPMailer();
         
            // Es necesario para poder usar un servidor SMTP como gmail
            $mail->isSMTP();
         
            // Si estamos en desarrollo podemos utilizar esta propiedad para ver mensajes de error
            //SMTP::DEBUG_OFF    = off (for production use) 0
            //SMTP::DEBUG_CLIENT = client messages 1 
            //SMTP::DEBUG_SERVER = client and server messages 2
            $mail->SMTPDebug     = 0;
         
            //Set the hostname of the mail server
            $mail->Host          = 'smtp.gmail.com';
            $mail->Port          = 465; // o 587
         
            // Propiedad para establecer la seguridad de encripción de la comunicación
            $mail->SMTPSecure    = PHPMailer::ENCRYPTION_SMTPS; // tls o ssl para gmail obligado
         
            // Para activar la autenticación smtp del servidor
            $mail->SMTPAuth      = true;
       
            // Credenciales de la cuenta
            $email              = 'villabonaelkin.98@gmail.com';
            $mail->Username     = $email;
            $mail->Password     = 'toce pfxw fzzt sngy';
         
            // Quien envía este mensaje
            $mail->setFrom($email, $name);
       
            // Si queremos una dirección de respuesta
            // $mail->addReplyTo('replyto@panchos.com', 'Pancho Doe');
         
            // Destinatario
            $mail->addAddress($para, 'ELKIN');
         
            // Asunto del correo
            $mail->Subject = $subject;
       
            // Contenido
            $mail->IsHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Body    = sprintf('<h1>El mensaje es:</h1><br><p>%s</p>', $message);
         
            // Texto alternativo
            $mail->AltBody = 'Este es el correo del cliente interesado: ' . $email;
       
            // Agregar algún adjunto
            //$mail->addAttachment(IMAGES_PATH.'logo.png');
         
            // Enviar el correo
            if (!$mail->send()) {
              throw new Exception($mail->ErrorInfo);
            }
       
            // Flasher::success(sprintf('Mensaje enviado con éxito a %s', $para));
            // Redirect::back();

            $json['is_success'] = 1;
       
        } catch (Exception $e) {
            Flasher::error($e->getMessage());
            Redirect::back();
            $json['message'] = "No se ha enviado el correo";
        }

        unset($_POST);

        echo json_encode($json, JSON_UNESCAPED_UNICODE);
    }

}

//Function to check if the request is an AJAX request
function is_ajax()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
?>