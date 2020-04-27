<?php
# запускаем сессию, в которой будем запоминать пользователя
session_start();
require_once("_header.php");
# Получение и обработка данных формы
if (count($_POST) > 0) {
	$response = $wmxi->X7(
		$_POST["wmid"],          # 12 цифр
		$_SESSION["plan"],       # произвольная строка от 1 до 255 символов; пробелы в начале или конце и переводы строк не допускаются
		$_POST["sign"]           # произвольная строка от 1 до 1024 символов; пробелы в начале или конце не допускаются
	);
	# Преобразовываем ответ сервера в структуру. Входные параметры:
	# - XML-ответ сервера
	# - кодировка, используемая на сайте. По умолчанию используется UTF-8
	$structure = $parser->Parse($response, DOC_ENCODING);
	# преобразуем индексы структуры к более удобным для доступа.
	# Не рекомендуется проводить такое преобразование с с результатом, если он содержит
	# множество однотипных строк (например, список транзакций)
	# если надобности в аттрибутах XML-тегов ответа нет, то второй параметр можно
	# установить в false - в таком случае структура выйдет более компактной
	$transformed = $parser->Reindex($structure, true);
	if (@$transformed["w3s.response"]["testsign"]["res"] == "yes") {
		$_SESSION["WMID"] = $_POST["wmid"];
	} else {
		unset($_SESSION["WMID"]);
	}
}
function microtime_float() {

	list($usec, $sec) = explode(" ", microtime());

	return ((float)$usec + (float)$sec);
}

# генерируем новую строку для подписи
$_SESSION["plan"] = "<access><url>dkameleon.com</url><datetime>" . date("Y-m-d H:i:s") . "</datetime><marker>" . sha1(microtime_float()) . "</marker></access>";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>X7</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= DOC_ENCODING; ?>"/>
	<meta name="author" content="DKameleon"/>
	<meta name="site" content="http://my-tools.net/wmxi/"/>
	<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
Детальное описание параметров:
<a href="http://webmoney.ru/rus/developers/interfaces/xml/identify/index.shtml">http://webmoney.ru/rus/developers/interfaces/xml/identify/index.shtml</a>
<br/>


<script type="text/javascript">
	function wm_ready() {
		if (AcceptWM.readyState == 4) {
		}
	}

	function wm_auth() {
		name = AcceptWM.strLoginName;
		if (AcceptWM /*&& (AcceptWM.readyState == 4)*/ && (name != "not login")) {
			document.getElementById("wmid").value = name;
			document.getElementById("sign").value = AcceptWM.SignString(document.getElementById("plan").value);
			if (document.getElementById("sign").value != "") return true;
		}
		return false;
	}

	AcceptWM = new GeckoActiveXObject('WMAcceptor.AcceptWM');

</script>

<object id="AcceptWM" onreadystatechange="wm_ready()" codeBase="https://w3s.webmoney.ru/WMAcceptor.dll#Version=1,0,0,31" height="1" width="1" classid="CLSID:463ED66E-431B-11D2-ADB0-0080C83DA4EB" VIEWASTEXT>
	<param NAME="nState" VALUE="3"/>
	<param NAME="LCID" VALUE="1049"/>
</object>


<form action="" method="post">

	<label>WM-идентификатор:</label>
	<b><?= isset($_SESSION["WMID"]) ? $_SESSION["WMID"] : "не авторизован"; ?></b>
	<br/>

	<input type="hidden" id="plan" name="plan" value="<?= htmlspecialchars($_SESSION["plan"], ENT_QUOTES); ?>"/>
	<input type="hidden" id="wmid" name="wmid" value=""/>
	<input type="hidden" id="sign" name="sign" value=""/>
	<br/>

	<input type="submit" id="submit" value="авторизация" onclick="return wm_auth();"/>
	<br/>

</form>

<!--pre><?= htmlspecialchars(@$response, ENT_QUOTES); ?></pre-->
<!--pre><?= htmlspecialchars(print_r(@$structure, true), ENT_QUOTES); ?></pre-->
<!--pre><?= htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES); ?></pre-->

<pre><!-- Читаем и отображаем элементы обработанного массива после получения ответа с сервера -->
		Результат: <b><?= htmlspecialchars(@$transformed["w3s.response"]["testsign"]["res"], ENT_QUOTES); ?></b>
		Код ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		Описание ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
