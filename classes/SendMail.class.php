<?php 

class SendMail {
    
    private static $phpmailer_deja_inclus = false;

	private $to;

	private $subject;

	private $body;

	private $mail_header;

	private $pseudo;

	private $title = null;

	private $url = null;

	private $template = null;

    public function __construct($to, $subject, $pseudo, $title = null, $url = null, $template = null){
		$this->to = $to;
		$this->subject = $subject;
			
		//template
		$this->pseudo = $pseudo;
		$this->title = $title;
		$this->url = $url;
		$this->template = $template;

    }

	public function send(){
		
		global $phpmailer_deja_inclus;
		if(!$phpmailer_deja_inclus) {
			require("smtp/PHPMailerAutoload.php");
			$phpmailer_deja_inclus = true;
		}

        $mail = new PHPMailer;
		//$mail->SMTPDebug = 4;                               // Enable verbose debug output
		$mail->isSMTP();                                    // Set mailer to use SMTP
		$mail->SMTPAuth = true;  // Authentification SMTP active
		$mail->SMTPSecure = SMTP['PROTOCOLE']; // Gmail REQUIERT Le transfert securise
		$mail->Host = SMTP['HOST'];
		$mail->Port = SMTP['PORT'];
		$mail->Username = SMTP['USERNAME'];
		$mail->Password = SMTP['PASSWORD'];
		$mail->SetFrom(SMTP['SET_FROM']['EMAIL'], SMTP['SET_FROM']['WEBSITE']);
		$mail->Subject = $this->subject;
		$mail->Body =  UTILS::Encode(self::template());
        $mail->AddAddress($this->to);
        $mail->isHTML(true);
		if($ajax = false){
			if(!$mail->send()) {
				UTILS::notification('danger', 'Le message n\'a pas pu être envoyé '.$mail->ErrorInfo, false, true);
				header('location: '.$_SERVER['REQUEST_URI']);
				exit;
			} else {
				UTILS::notification('success', 'Un mail de confirmation vous a été envoyé', false, true);
				header('location: '.$_SERVER['REQUEST_URI']);
				exit;
			}
		}else{
			if(!$mail->send()) {
				$corpMessages = '<div class="notification">';
				$corpMessages .= '<div class="alert alert-danger w-50 d-flex align-items-center justify-content-between" role="alert">';
				$corpMessages .= 'Le message n\'a pas pu être envoyé '.$mail->ErrorInfo;
				$corpMessages .= '</div>';
				$corpMessages .= '</div>';
				die(json_encode($corpMessages));
			}
		}
	}

	public function template(){
		include('tplEmail/tplMail.class.php');
		$template = new TPL();
		$contenu = $template->isMail($this->to, $this->pseudo, $this->title, $this->url, $this->template);
		return $contenu;
	}
}