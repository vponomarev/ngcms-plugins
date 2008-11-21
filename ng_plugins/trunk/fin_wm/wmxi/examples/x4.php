<?php
	require_once("_header.php");

	# ��������� � ��������� ������ �����
	if (count($_POST) > 0) {

		$response = $wmxi->X4(
			$_POST["purse"],               # ����� �������� ��� ������ �� ������� �������� ����������� ����
			intval($_POST["wminvid"]),     # ����� ����� > 0
			intval($_POST["orderid"]),     # ����� ����� � ������� ����� ��������; ����� ����� ����� ��� �����
			trim($_POST["datestart"]),     # �������� ��:��:��
			trim($_POST["datefinish"])     # �������� ��:��:��
		);

		# ��������������� ����� ������� � ���������. ������� ���������:
		# - XML-����� �������
		# - ���������, ������������ �� �����. �� ��������� ������������ UTF-8
		$structure = $parser->Parse($response, DOC_ENCODING);

		# ����������� ������� ��������� � ����� ������� ��� �������.
		# �� ������������� ��������� ����� �������������� � � �����������, ���� �� ��������
		# ��������� ���������� ����� (��������, ������ ����������)
		# ���� ���������� � ���������� XML-����� ������ ���, �� ������ �������� �����
		# ���������� � false - � ����� ������ ��������� ������ ����� ����������
		$transformed = $parser->Reindex($structure, false);

	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>X4</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=DOC_ENCODING;?>" />
	<meta name="author" content="DKameleon" />
	<meta name="site" content="http://my-tools.net/wmxi/" />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	��������� �������� ����������:
	<a href="http://webmoney.ru/rus/developers/interfaces/xml/invhistory/index.shtml">http://webmoney.ru/rus/developers/interfaces/xml/invhistory/index.shtml</a>
	<br />

	<form action="" method="post">

		<label>����� �������� ��� ������ �� ������� �������� ����������� ���� :</label>
		<input type="text" name="purse" value="" />
		<br/>

		<label>����� ����� (� ������� WebMoney):</label>
		<input type="text" name="wminvid" value="0" />
		<br/>

		<label>����� �����:</label>
		<input type="text" name="orderid" value="0" />
		<br/>

		<label>����������� ����� � ���� �������� �����:</label>
		<input type="text" name="datestart" value="20070418 18:00:00" />
		<br/>

		<label>������������ ����� � ���� �������� �����:</label>
		<input type="text" name="datefinish" value="20070420 18:00:00" />
		<br/>

		<input type="submit" value="�������� ������" />
		<br/>

	</form>

	<!--pre><?=htmlspecialchars(@$response, ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$structure, true), ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES);?></pre-->

	<pre><!-- ������ � ���������� �������� ������������� ������� ����� ��������� ������ � ������� -->
		<?
			$items = @$structure["0"]["node"]["1"]["node"];
			$items = is_array($items) ? $items : array();
			foreach($items as $k => $v) {
				$vv = $parser->Reindex($v["node"], true);
		?>

			<b>� <?=$k;?></b>
			������ ��������: <b><?=htmlspecialchars(@$vv["storepurse"], ENT_QUOTES); ?></b>
			������������� �����: <b><?=htmlspecialchars(@$vv["customerwmid"], ENT_QUOTES); ?></b>
			������ ��������������: <b><?=htmlspecialchars(@$vv["customerpurse"], ENT_QUOTES); ?></b>
			�����: <b><?=htmlspecialchars(@$vv["amount"], ENT_QUOTES); ?></b>
			�����: <b><?=htmlspecialchars(@$vv["address"], ENT_QUOTES); ?></b>
			��������: <b><?=htmlspecialchars(@$vv["desc"], ENT_QUOTES); ?></b>
			������: <b><?=htmlspecialchars(@$vv["state"], ENT_QUOTES); ?></b>
			������: <b><?=htmlspecialchars(@$vv["datecrt"], ENT_QUOTES); ?></b>
			������: <b><?=htmlspecialchars(@$vv["dateupd"], ENT_QUOTES); ?></b>
		<? } ?>

		��� ������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		�������� ������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
