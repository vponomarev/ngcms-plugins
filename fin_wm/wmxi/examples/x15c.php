<?php
require_once("_header.php");
# ѕолучение и обработка данных формы
if (count($_POST) > 0) {
	$response = $wmxi->X15c(
		trim($_POST["masterwmid"]),       # ¬ћ-идентификатор, которому идентификтор slavewmid данным запросом разрешает или запрещает управление своим кошельком slavepurse
		trim($_POST["slavewmid"]),        # ¬ћ-идентификатор, который довер€ет идентификтору masterwmid данным запросом управление своим кошельком slavepurse
		trim($_POST["purse"]),            # кошелек, принадлежащий идентификатору slavewmid на который устнавливаетс€ доверие
		intval($_POST["ainv"]),
		intval($_POST["atrans"]),
		intval($_POST["apurse"]),
		intval($_POST["atranshist"]),
		floatval($_POST["limit"]),
		floatval($_POST["daylimit"]),
		floatval($_POST["weeklimit"]),
		floatval($_POST["monthlimit"])
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
	<title>X15c</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?= DOC_ENCODING; ?>"/>
	<meta name="author" content="DKameleon"/>
	<meta name="site" content="http://my-tools.net/wmxi/"/>
	<link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
ƒетальное описание параметров:
<a href="https://wiki.webmoney.ru/wiki/show/%d0%98%d0%bd%d1%82%d0%b5%d1%80%d1%84%d0%b5%d0%b9%d1%81+X15">https://wiki.webmoney.ru/wiki/show/»нтерфейс+X15</a>
<br/>

<form action="" method="post">

	<label>master WMID:</label>
	<input type="text" name="masterwmid" value=""/>
	<br/>

	<label>slave WMID:</label>
	<input type="text" name="slavewmid" value=""/>
	<br/>

	<label>кошелек:</label>
	<input type="text" name="purse" value=""/>
	<br/>

	<label>inv:</label>
	<input type="text" name="ainv" value="1"/>
	<br/>

	<label>trans:</label>
	<input type="text" name="atrans" value="1"/>
	<br/>

	<label>purse:</label>
	<input type="text" name="apurse" value="1"/>
	<br/>

	<label>transhist:</label>
	<input type="text" name="atranshist" value="1"/>
	<br/>

	<label>суточный лимит:</label>
	<input type="text" name="limit" value="0"/>
	<br/>

	<label>дневной лимит:</label>
	<input type="text" name="daylimit" value="0"/>
	<br/>

	<label>недельный лимит:</label>
	<input type="text" name="weeklimit" value="0"/>
	<br/>

	<label>мес€чный лимит:</label>
	<input type="text" name="monthlimit" value="0"/>
	<br/>

	<input type="submit" value="установить доверие"/>
	<br/>

</form>

<!--pre><?= htmlspecialchars(@$response, ENT_QUOTES); ?></pre-->
<pre><?= htmlspecialchars(print_r(@$structure, true), ENT_QUOTES); ?></pre>
<!--pre><?= htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES); ?></pre-->

<pre><!-- „итаем и отображаем элементы обработанного массива после получени€ ответа с сервера -->
		 од ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		ќписание ошибки: <b><?= htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
