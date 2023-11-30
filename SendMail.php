<?php
//if (session_status() == PHP_SESSION_NONE) {
//    session_start();
//}
if (is_file(__DIR__ . '/vendor/autoload.php')) {
    require(__DIR__ . '/vendor/autoload.php');
}
//namespace plugins;

//use AbstractPicoPlugin;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class SendMail extends AbstractPicoPlugin
{
    protected $enabled = true;
    private array $smtpConfig;
    private string $themePath;
    private array $conf;

    protected array $response = ['status' => 'ERROR', 'description' => ''];

    private function sendResponse()
    {
        $json = json_encode($this->response);
        header('Content-Type: application/json');
        // header('X_SIGNATURE: ' . $this->calcSignature($json));
        echo $json;
        exit;
//        echo json_encode($this->response);
//        exit();
    }

    public function onConfigLoaded(array &$config)
    {
        // Доступ к параметрам конфигурации
        $this->smtpConfig = isset($config['smtp']) ? $config['smtp'] : [];
        $this->themePath = dirname(__DIR__, 2) . '/themes/default/';
        $this->conf = $config;
        //  var_dump($this->smtpConfig);
    }

    public function onPagesLoaded(array &$pages)
    {
        // Здесь можно добавить ваш код для отправки писем
        // Например, проверить, была ли отправлена форма, и использовать PHPMailer для отправки письма
    }

    public function onRequestUrl(&$url)
    {
        if ($url == 'send-mail') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->sendResponse();
                return;
            }
            if (empty($_POST)) {
                $this->sendResponse();
                return;
            }
            if (!isset($_POST['action'])) {
                $this->sendResponse();
                return;
            }
            if ($_POST['action'] != 'send-order') {
                $this->sendResponse();
                return;
            }
            if (empty($_POST['approve'])) {
                $this->response['description'] = 'согласитесь Политикой конфиденциальности компании';
                $this->sendResponse();
                return;
            }
            if (empty($_POST['name'])) {
                $this->response['description'] = 'Нет имени';
                $this->sendResponse();
                return;
            }
            if (empty($_POST['phone'])) {
                $this->response['description'] = 'Укажите номер телефона';
                $this->sendResponse();
                return;
            }
            $name = strip_tags(trim($_POST['name']));
            $phone = strip_tags(trim($_POST['phone']));
            $email = filter_var(trim($_POST['mail']), FILTER_SANITIZE_EMAIL) ?? '';
            $message = '<h2>Новый заказ с сайта ' . $this->conf['site_title'] . '</h2>';
            $message .= '<p><b>Имя</b>: ' . $name . '</p>';
            $message .= '<p><b>Телефон:</b> ' . $phone . '</p>';
            $message .= '<p><b>E-mail:</b> ' . $email . '</p>';
            $sendMail = $this->send($this->smtpConfig['reply_to'], "Новое сообщение " . $this->conf['site_title'], $message);
            if (!$sendMail) {
                $this->response['description'] = 'Ошибка';
                $this->sendResponse();
            }
            $this->response['status'] = 'OK';
            $this->response['description'] = 'Письмо успешно отправлено';
            $this->sendResponse();
        }

    }


    private function send($recipient, $subject, $message)
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = 'utf-8';// Enable verbose debug output
        try {
            if ($this->smtpConfig['smtp']) {
                $mail->isSMTP();
                $mail->Host = $this->smtpConfig['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $this->smtpConfig['username'];
                $mail->Password = $this->smtpConfig['password'];
                $mail->SMTPSecure = $this->smtpConfig['security'];
                $mail->Port = $this->smtpConfig['port'];
            } else {
                $mail->isSendmail();
                //$mail->addReplyTo($this->smtpConfig['username'], $this->smtpConfig['username']);
            }
            $mail->addAddress($recipient);
            $mail->setFrom($this->smtpConfig['reply_to'], $this->smtpConfig['from_name']);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $this->renderTemplate($subject, $message);
            $mail->send();
            return true;
        } catch (Exception $e) {
            $this->response['error'] = $mail->ErrorInfo;
            $this->sendResponse();
        }
    }

    private function renderTemplate($subject, $message)
    {
        $loader = new FilesystemLoader($this->themePath);
        $twig = new Environment($loader);
        $params = $this->conf;
        return $twig->render('eml.twig', array_merge($params, [
            'subject' => $subject,
            'title' => 'Заголовок Письма',
            'message' => $message
        ]));
    }

    private function dd($mixed, $caption = null)
    {
        echo $caption ? '<br><b>' . $caption . '</b>' : '';
        $debug_backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        echo '<div style="font-size:12px; color: gray;">' . str_replace('/home/nikolas/NetBeansProjectsGit/tahograph-twig', '', $debug_backtrace[0]['file']) . ' on line ' . $debug_backtrace[0]['line'] . '</div>';
        echo '<pre style="font-size:12px; color:black;">' . print_r($mixed, 1) . '</pre>';
        exit;
    }
}