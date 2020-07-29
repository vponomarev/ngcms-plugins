<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Переход на страницу платежного шлюза</title>
</head>
<body style="font-family: verdana;">
<div style="background-color : #ffc; color : #444; padding : 5px 0 5px 5px; font : normal 10px verdana, sans-serif; border : 1px dotted #999;">
	<p>Переадресация на страницу платёжного шлюза...</p>
	<br/>
	<p>Если переадресация не произойдёт в течении нескольких секунд, то вам необходимо нажать на кнопку `оплатить`.</p>
	<form method="post" action="{form_url}" id="payform">
		{inputs}
		<input style="font-family: verdana;" type="submit" value="Оплатить"/>
	</form>
</div>

<script language="javascript">
	//document.getElementById('payform').submit();
</script>
</body>
</html>