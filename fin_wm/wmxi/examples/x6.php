<?php
require_once("_header.php");
# ѕолучение и обработка данных формы
if (count($_POST) > 0) {
	$response = $wmxi->X6(
		$_POST["receiverwmid"],                            # 12 цифр
		trim($_POST["msgsubj"]),                           # произвольна€ строка от 1 до 255 символов; пробелы в начале или конце и переводы строк не допускаютс€
		trim(str_replace("\r", "", $_POST["msgtext"]))     # произвольна€ строка от 1 до 1024 символов; пробелы в начале или конце не допускаютс€
	);
	# ѕреобразовываем ответ сервера в структуру. ¬ходные параметры:
	# - XML-ответ сервера
	# - кодировка, используема€ на сайте. ѕо умолчанию используетс€ UTF-8
	$structure = $parser->Parse($response, DOC_ENCODING);
	# преобразуем индексы структуры к более удобным дл€ доступа.
	# Ќе рекомендуетс€ проводить такое преобразование с с результатом, если он содержит
	# множество однотипных строк (например, список транзакций)
	# если надобности в аттрибутах XML-тегов ответа нет, то второй параметр можно
	# установить в false - в таком случае структура выйдет более компактной
	$transformed = $parser->Reindex($structure, true);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>X6</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= DOC_ENCODING; ?>"/>
	<meta name="author" content="DKameleon"/>
	<meta name="site" content="http://my-tools.net/wmxi/"/>
	<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
ƒетальное описание параметров:
<a href="http://webmoney.ru/rus/developers/interfaces/xml/wmmail/index.shtml">http://webmoney.ru/rus/developers/interfaces/xml/wmmail/index.shtml</a>
<br/>

<form action="" method="post">

	<label>WM-идентфиикатор получател€ сообщени€:</label>
	<input type="text" name="receiverwmid" value=""/>
	<br/>

	<label>тема сообщени€:</label>
	<input type="text" name="msgsubj" value="тестирование X6 wmxi"/>
	<br/>

	<label>текст сообщени€:</label>
	<textarea name="msgtext" rows="5" cols="40">“естирование многострочного
кириллического сообщени€</textarea>
	<br/>

	<input type="submit" value="отправить сообщение"/>
	<br/>

</form>

<!--pre><?= htmlspecialchars(@$response, ENT_QUOTES); ?></pre-->
<!--pre><?= htmlspecialchars(print_r(@$structure, true), ENT_QUOTES); ?></pre-->
<!--pre><?= htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES); ?></pre-->

<pre><!-- „итаем и отображаем элементы обработанного массива после получени€ ответа с сервера -->
		ѕолучатель: <b><?= htmlspecialchars(@$transformed["w3s.response"]["message"]["receiverwmid"], ENT_QUOTES); ?></b>
		“ема: <b><?= htmlspecialchars(@$transformed["w3s.response"]["message"]["msgsubj"], ENT_QUOTES); ?></b>
		“екст: <b><?= htmlspecialchars(@$transformed["w3s.response"]["message"]["msgtext"], ENT_QUOTES); ?></b>
		—оздано: <b><?= htmlspecialchars(@$transformed["w3s.response"]["message"]["datecrt"], ENT_QUOTES); ?></b>

		 од ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		ќписание ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
