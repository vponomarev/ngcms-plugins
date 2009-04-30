<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Сообщить об ошибке</title>
<style type="text/css">
body { font : normal 12px verdana, sans-serif; }
#senderror {padding-top:10px;background:#fff7e1;border:1px dashed #e8e8e8;margin-top:10px;padding-bottom:20px;}
#senderror select.error {border:1px solid #e8e8e8;background:#fff;}
#senderror input.report{border:1px solid #e8e8e8;background:#fff;}
#senderror .texth {position:relative;left:10px;}
</style>
</head>
<body>
<form method="post" action="">
<input type="hidden" name="action" value="plugin"/>
<input type="hidden" name="plugin" value="complain"/>
<input type="hidden" name="plugin_cmd" value="post"/>
<input type="hidden" name="ds_id" value="{ds_id}"/>
<input type="hidden" name="entry_id" value="{entry_id}"/>

<div id="senderror">
<div class="texth">
<u>Сообщить об ошибке:</u><br/><br/>
Тип ошибки: <select name="error" class="error">{errorlist}</select><br/><br/>
[email]Ваш e-mail: <input type="text" name="mail"/><br/>[/email]
[notify]<input type="checkbox" name="notify" value="1"/> информировать о решении проблемы <br/>[/notify]
[text]Детальное описание проблемы:<br/><textarea cols="80" rows="4" name="text"></textarea><br/>[/text]
<input type="submit" class="report" value="Отправить"/>
</div>	
</div>
</form>
</body>
</html>
