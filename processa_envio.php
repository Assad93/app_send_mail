<?php

    require './bibliotecas/PHPMailer/Exception.php';
    require './bibliotecas/PHPMailer/OAuth.php';
    require './bibliotecas/PHPMailer/PHPMailer.php';
    require './bibliotecas/PHPMailer/POP3.php'; // protocolo para recebimento de email
    require './bibliotecas/PHPMailer/SMTP.php'; // protocolo para envio de email

    use PHPMailer\PHPMailer\PHPMailer; //usa o namespace e extrai a classe PHPMailer
    use PHPMailer\PHPMailer\Exception;

    class Mensagem {
        private $para;
        private $assunto;
        private $mensagem;

        public function __get($atributo)
        {
            return $this->$atributo;
        }

        public function __set($atributo, $valor)
        {
            $this->$atributo = $valor;
        }

        public function mensagemValida()
        {
            if (empty($this->para) || empty($this->assunto) || empty($this->mensagem))
            {
                return false;
            }

            return true;
        }
    }

    //Validando Mensagem
    $para = filter_input(INPUT_POST, 'para', FILTER_SANITIZE_EMAIL);
    if (!filter_var($para, FILTER_VALIDATE_EMAIL))
    {
        echo "Email Inválido!";
    }
    else
    {
        $mensagem = new Mensagem();
        $mensagem->__set('para', $para);
        $mensagem->__set('assunto', $_POST['assunto']);
        $mensagem->__set('mensagem', $_POST['mensagem']);

        //print_r($mensagem);

        if (!$mensagem->mensagemValida())
        {
            echo 'Mensagem inválida!';
            die(); //mata o processamento do script
        } 
        
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'samir.teste1234@gmail.com';                 // SMTP username
            $mail->Password = '!@#$12345';                           // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('samir.teste1234@gmail.com', 'Mailer');
            $mail->addAddress($mensagem->__get('para'));     // Add a recipient
            //$mail->addAddress('ellen@example.com');               // Name is optional
            //$mail->addReplyTo('info@example.com', 'Information');
            //$mail->addCC('cc@example.com');
            //$mail->addBCC('bcc@example.com');

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

            //Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $mensagem->__get('assunto');
            $mail->Body    = $mensagem->__get('mensagem');
            $mail->AltBody = $mensagem->__get('mensagem');

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo 'Message could not be sent.';
            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }
}

    