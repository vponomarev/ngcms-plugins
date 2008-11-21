<?php
	require_once("_header.php");

	# ѕолучение и обработка данных формы
	if (count($_POST) > 0) {

		$response = $wmxi->X5(
			intval($_POST["wmtranid"]),    # целое число без знака
			trim($_POST["pcode"])          # произвольна€ строка от 1 до 255 символов; пробелы в начале или конце не допускаютс€
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
	<title>X5</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=DOC_ENCODING;?>" />
	<meta name="author" content="DKameleon" />
	<meta name="site" content="http://my-tools.net/wmxi/" />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	ƒетальное описание параметров:
	<a href="http://webmoney.ru/rus/developers/interfaces/xml/codeprotect/index.shtml">http://webmoney.ru/rus/developers/interfaces/xml/codeprotect/index.shtml</a>
	<br />

	<form action="" method="post">

		<label>уникальный номер платежа в системе учета WebMoney:</label>
		<input type="text" name="wmtranid" value="0" />
		<br/>

		<label>код протекции сделки:</label>
		<input type="text" name="pcode" value="0" />
		<br/>

		<input type="submit" value="ввести код" />
		<br/>

	</form>

	<!--pre><?=htmlspecialchars(@$response, ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$structure, true), ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES);?></pre-->

	<pre><!-- „итаем и отображаем элементы обработанного массива после получени€ ответа с сервера -->
		“ип платежа: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["opertype"], ENT_QUOTES); ?></b>
		»зменЄн: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["dateupd"], ENT_QUOTES); ?></b>

		 од ошибки: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		ќписание ошибки: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
