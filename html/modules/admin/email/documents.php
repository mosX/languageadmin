<?php
$mailsubject  = _("Dear trader!");
$mailsubject  = "=?UTF-8?B?" . base64_encode($mailsubject) ."?=";
$mailbody_html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Title</title>
</head>
<body>
    <table>
        <tr>
            
            <td>
                <p>Уважаемый, трейдер,</p>
                <p>Сообщаем Вам, что процедура подтверждения документов была успешно завершена, и Вы можете совершить пополнения своего торгового счета с карты, которую Вы указали при загрузке документов.</p>
                </br>
                <p>С уважением,</p>
                <p>служба поддержки BinSecret.</p>
            </td>
        </tr>
    </table>
</body>
</html>';

$mailbody_txt =  'Уважаемый, трейдер'. "\n\n"
              . 'Сообщаем Вам, что процедура подтверждения документов была успешно завершена, и Вы можете совершить пополнения своего торгового счета с карты, которую Вы указали при загрузке документов.'."\n\n"."\n\n"
              . 'С уважением, '."\n\n"
              . 'служба поддержки BinSecret.'."\n\n"
              ;
?>