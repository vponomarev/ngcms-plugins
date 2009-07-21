<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Управление инцидентами</title>
<style type="text/css">
body {
 font-family:verdana, arial; 
 font-size: 12px; 
 line-height:14px; 
 color:#000000; 
 background : #fff;
}
h1 {
 font-size: 14px;
 text-decoration: underline;
}

#mt {
 font-size: 10px;
 background: #999999;
}
#mt thead { background: #EEEEEE; font-weight: bold; }
#mt tbody { background: #FFFFFF; }
#st { font-size: 10px; margin: 5px; }
#st select { font-size: 10px; }
input  { font-size: 10px; }
</style>
</head>
<body>
<script language="javascript">
var ETEXT={ETEXT};
</script>

<h1>Список активных инцидентов</h1>

<form action="{form_url}" method="post">
<table id="mt" width="100%" cellspacing=1 cellpadding=1>
<thead>
<tr>
 <td>Дата</td>
 <td>Новость</td>
 <td>Ошибка</td>
 <td>Автор новости</td>
 <td>Автор инцидента</td>
 <td>Назначена на</td>
 <td>Статус</td>
 <td>#</td>
</tr>
</thead>
<tbody>
{entries}
</tbody>
</table>
<br/>
<b><u>Действия с выделенными инцидентами:</u></b><br/>
<table id="st" cellspacing="0" cellpadding="0">
<tr>
 <td><label><input type="checkbox" name="setowner" value="1" /> Назначить на меня</label></td>
 <td>&nbsp;</td>
</tr>
<tr>
 <td><label><input type="checkbox" name="setstatus" value="1" /> Изменить статус на</label></td>
 <td>&nbsp; &nbsp; <select name="newstatus">
	{status_options}
     </select></td>
</tr>
</table>
<input type="submit" value="Выполнить изменения"/>
</form>
<br/>
</body>
</html>