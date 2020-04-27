<?php
require_once("_header.php");
# Получение и обработка данных формы
if (count($_POST) > 0) {
	$response = $wmxi->X8(
		trim($_POST["wmid"]),     # 12 цифр
		trim($_POST["purse"])     # первый символ один из - Z,R,E,U,C,D и 12 цифр
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
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>X8</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= DOC_ENCODING; ?>"/>
	<meta name="author" content="DKameleon"/>
	<meta name="site" content="http://my-tools.net/wmxi/"/>
	<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
Детальное описание параметров:
<a href="http://webmoney.ru/rus/developers/interfaces/xml/attribute/index.shtml">http://webmoney.ru/rus/developers/interfaces/xml/attribute/index.shtml</a>
<br/>

<form action="" method="post">

	<label>WM-идентфиикатор:</label>
	<input type="text" name="wmid" value=""/>
	<br/>

	<label>кошелек:</label>
	<input type="text" name="purse" value=""/>
	<br/>

	<input type="submit" value="проверить"/>
	<br/>

</form>

<!--pre><?= htmlspecialchars(@$response, ENT_QUOTES); ?></pre-->
<!--pre><?= htmlspecialchars(print_r(@$structure, true), ENT_QUOTES); ?></pre-->
<!--pre><?= htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES); ?></pre-->

<pre><!-- Читаем и отображаем элементы обработанного массива после получения ответа с сервера -->
		Идентификатор: <b><?= htmlspecialchars(@$transformed["w3s.response"]["testwmpurse"]["wmid"], ENT_QUOTES); ?></b>
		Кошелёк: <b><?= htmlspecialchars(@$transformed["w3s.response"]["testwmpurse"]["purse"], ENT_QUOTES); ?></b>

		Код ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		Описание ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
