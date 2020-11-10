<?php ini_set('display_errors', 'On');

echo "<pre>";
print_r($_POST);
print_r($_FILES);
echo "</pre>";
die();

$email = 'example@yandex.ru'; // адрес куда отправлять письмо, можно несколько через запятую
$subject = 'Новое сообщение с сайта '.$_SERVER['HTTP_HOST']; // тема письма с указанием адреса сайта
$message = '<h3>Данные формы квиза:</h3>'; // вводная часть письма
$addreply = ''; // адрес куда отвечать (необязательно)
$from = 'Данные квиза'; // имя отправителя (необязательно)
$smtp = 1; // отправлять ли через почтовый ящик, 1 - да, 0 - нет, отправлять через хостинг

// настройки почтового сервера для режима $smtp = 1 (Внимание: с GMAIL не работает)
$host = 'smtp.yandex.ru'; // сервер отправки писем (приведен пример для Яндекса)
$username = 'example@yandex.ru'; // логин вашего почтового ящика
$password = 'passw0rd123'; // пароль вашего почтового ящика
$auth = 1; // нужна ли авторизация, 1 - нужна, 0 - не нужна
$secure = 'ssl'; // тип защиты
$port = 465; // порт сервера
$charset = 'utf-8'; // кодировка письма

// дополнительные настройки
$cc = ''; // копия письма
$bcc = ''; // скрытая копия

$client_email = ''; // поле откуда брать адрес клиента
$client_message = ''; // текст письма, которое будет отправлено клиенту
$client_file = ''; // вложение, которое будет отправлено клиенту

$fields = "";
// заполняем данными $fields
foreach ($_POST as $key => $value) {
    if ($value === 'on') {
        $value = 'Да';
    }
    if (is_array($value)) {
        $fields .= str_replace('_', ' ', "<b>$key</b>").':<br />&nbsp;- '.implode(', <br />&nbsp;- ', $value).'<br />';
    } else {
        if ($value !== '') {
            $fields .= str_replace('_', ' ', "<b>$key</b>").': '.$value.'<br />';
        }
    }
}

smtpmail($email, $subject, $message.'<br>'.$fields);
if ($client_email !== '') {
    $client_message === '' ? $message .= '<br>'.$fields : $message = $client_message;
    smtpmail($_POST[$client_email], $subject, $message, true);
}

/*
 * функции
 */

function smtpmail($to, $subject, $content, $client_mode = false)
{
    global $success, $smtp, $host, $auth, $secure, $port, $username, $password, $from, $addreply, $charset, $cc, $bcc, $client_email, $client_message, $client_file;

    require_once('./class-phpmailer.php');
    $mail = new PHPMailer(true);
    if ($smtp) {
        $mail->IsSMTP();
    }
    try {
        $mail->SMTPDebug  = 0;
        $mail->Host       = $host;
        $mail->SMTPAuth   = $auth;
        $mail->SMTPSecure = $secure;
        $mail->Port       = $port;
        $mail->CharSet    = $charset;
        $mail->Username   = $username;
        $mail->Password   = $password;

        if ($username !== '') $mail->SetFrom($username, $from);

        if ($addreply !== '') $mail->AddReplyTo($addreply, $from);

        $to_array = explode(',', $to);
        foreach ($to_array as $to) $mail->AddAddress($to);

        if ($cc !== '') {
            $to_array = explode(',', $cc);
            foreach ($to_array as $to) $mail->AddCC($to);
        }

        if ($bcc !== '') {
            $to_array = explode(',', $bcc);
            foreach ($to_array as $to) $mail->AddBCC($to);
        }

        $mail->Subject = htmlspecialchars($subject);
        $mail->MsgHTML($content);

        $files_array = reArrayFiles($_FILES['attachments']);
        if ($files_array !== false) {
            foreach ($files_array as $file) {
                if ($file['error'] === UPLOAD_ERR_OK) $mail->AddAttachment($file['tmp_name'], $file['name']);
            }
        }

        if ($client_file !== '' && $client_mode) $mail->AddAttachment($client_file);

        $mail->Send();
        if (!$client_mode) echo('success');

    } catch (phpmailerException $e) {
        echo $e->errorMessage();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

function reArrayFiles(&$file_post)
{
    if ($file_post === null) false;

    $files_array = [];
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);
    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) $files_array[$i][$key] = $file_post[$key][$i];
    }
    return $files_array;
}

//echo "<pre>";
//print_r($fields);
//echo "</pre>";
