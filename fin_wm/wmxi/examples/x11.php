<?php
	require_once("_header.php");

	# Получение и обработка данных формы
	if (count($_POST) > 0) {

		$response = $wmxi->X11(
			trim($_POST["passportwmid"]),     # 12 цифр
			intval($_POST["dict"]),           # 0/1
			intval($_POST["info"]),           # 0/1
			intval($_POST["mode"])            # 0/1
		);

		# изменяем кодировку для парсера, так как в этом интерфейсе результат возвращается в кирилице
		# Внимание! это необходимо только для интерфейса X11
		$parser->parser_encoding = "windows-1251";

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
	<title>X11</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=DOC_ENCODING;?>" />
	<meta name="author" content="DKameleon" />
	<meta name="site" content="http://my-tools.net/wmxi/" />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	Детальное описание параметров:
	<a href="http://passport.webmoney.ru/asp/infoXMLGetWMPassport.asp">http://passport.webmoney.ru/asp/infoXMLGetWMPassport.asp</a>
	<br />

	<form action="" method="post">

		<label>WM - идентификатор аттестата :</label>
		<input type="text" name="passportwmid" value="" />
		<br/>

		<label>отображение "опорного словаря":</label>
		<input type="text" name="dict" value="0" />
		<br/>

		<label>отображение персональных данных(паспортные данные + контактная информация) владельца аттестата:</label>
		<input type="text" name="info" value="1" />
		<br/>

		<label>проверка принадлежности WM идентификатора, подписавшего запрос, списку доверенных идентификаторов для проверяемого аттестата:</label>
		<input type="text" name="mode" value="0" />
		<br/>

		<input type="submit" value="проверить" />
		<br/>

	</form>

	<!--pre><?=htmlspecialchars(@$response, ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$structure, true), ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES);?></pre-->

	<? $item = @$structure["0"]["node"]["1"]["node"]; ?>
	<pre><!-- Читаем и отображаем элементы обработанного массива после получения ответа с сервера -->
		Регистратор: <b><?=htmlspecialchars(@$item["0"]["node"]["0"]["@"]["REGNICKNAME"], ENT_QUOTES);?></b>
		Ник: <b><?=htmlspecialchars(@$item["2"]["node"]["0"]["node"]["0"]["@"]["NICKNAME"], ENT_QUOTES);?></b>
		Инфо: <b><?=htmlspecialchars(@$item["2"]["node"]["0"]["node"]["0"]["@"]["INFOOPEN"], ENT_QUOTES);?></b>
		Город: <b><?=htmlspecialchars(@$item["2"]["node"]["0"]["node"]["0"]["@"]["CITY"], ENT_QUOTES);?></b>
		Страна: <b><?=htmlspecialchars(@$item["2"]["node"]["0"]["node"]["0"]["@"]["COUNTRY"], ENT_QUOTES);?></b>

		Код ошибки: <b><?=htmlspecialchars(@$structure["0"]["@"]["RETVAL"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
