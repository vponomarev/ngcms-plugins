<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

function messaging($subject, $content) {
    global $lang, $mysql;

    LoadPluginLang('messaging', 'messaging', '', 'mes');

    if (!$subject || trim($subject) == "") {
        msg(array("type" => "error", "text" => $lang['mes_msge_subject']));
    }
    elseif (!$content || trim($content) == "") {
        msg(array("type" => "error", "text" => $lang['mes_msge_content']));
    }
    else {
                $mailBody = nl2br($content);
                $mailSubject = $subject;
                
                foreach ($mysql->select("SELECT mail FROM `".uprefix."_users`") as $row) {
                    $mailTo = $row['mail'];
                    sendEmailMessage($mailTo, $mailSubject, $mailBody, $filename = false, $mail_from = false, $ctype = 'text/html');
                }
                
                msg(array("text" => $lang['mes_msgo_sent']));
    }
}
?>
