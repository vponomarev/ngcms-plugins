<?php
	require_once("_header.php");

	# ��������� � ��������� ������ �����
	if (count($_POST) > 0) {

		$response = $wmxi->X6(
			$_POST["receiverwmid"],                            # 12 ����
			trim($_POST["msgsubj"]),                           # ������������ ������ �� 1 �� 255 ��������; ������� � ������ ��� ����� � �������� ����� �� �����������
			trim(str_replace("\r", "", $_POST["msgtext"]))     # ������������ ������ �� 1 �� 1024 ��������; ������� � ������ ��� ����� �� �����������
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
		$transformed = $parser->Reindex($structure, true);

	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>X6</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=DOC_ENCODING;?>" />
	<meta name="author" content="DKameleon" />
	<meta name="site" content="http://my-tools.net/wmxi/" />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	��������� �������� ����������:
	<a href="http://webmoney.ru/rus/developers/interfaces/xml/wmmail/index.shtml">http://webmoney.ru/rus/developers/interfaces/xml/wmmail/index.shtml</a>
	<br />

	<form action="" method="post">

		<label>WM-������������� ���������� ���������:</label>
		<input type="text" name="receiverwmid" value="" />
		<br/>

		<label>���� ���������:</label>
		<input type="text" name="msgsubj" value="������������ X6 wmxi" />
		<br/>

		<label>����� ���������:</label>
		<textarea name="msgtext" rows="5" cols="40">������������ ��������������
�������������� ���������</textarea>
		<br/>

		<input type="submit" value="��������� ���������" />
		<br/>

	</form>

	<!--pre><?=htmlspecialchars(@$response, ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$structure, true), ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES);?></pre-->

	<pre><!-- ������ � ���������� �������� ������������� ������� ����� ��������� ������ � ������� -->
		����������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["message"]["receiverwmid"], ENT_QUOTES); ?></b>
		����: <b><?=htmlspecialchars(@$transformed["w3s.response"]["message"]["msgsubj"], ENT_QUOTES); ?></b>
		�����: <b><?=htmlspecialchars(@$transformed["w3s.response"]["message"]["msgtext"], ENT_QUOTES); ?></b>
		�������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["message"]["datecrt"], ENT_QUOTES); ?></b>

		��� ������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		�������� ������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
