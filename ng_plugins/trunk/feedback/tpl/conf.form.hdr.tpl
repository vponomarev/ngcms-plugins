<table border="0" cellspacing="1" cellpadding="1" class="content">
<tr>
<td colspan="2" width=100% class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8"><a href="?mod=extras" title="{l_extras}">{l_extras}</a> <b>=></b> <a href="?mod=extra-config&plugin=feedback">Настройка плагина feedback</a></td>
</tr>
</table>

[enabled]

<form method="post" action="">
<input type="hidden" name="mod" value="extra-config"/>
<input type="hidden" name="plugin" value="feedback"/>
<input type="hidden" name="action" value="saveform"/>

<table width="100%" border="0">
<tr>
<td colspan="5" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8">Редактирование формы "{name}"</td>
</tr>
<tr align="left" valign="top"><td class="contentRow" width="230"><b>Код формы / URL страницы:</b></td><td><input style="width: 30px; background: white;" type="text" name="id" value="{id}" disabled="disabled"/> <input style="width: 420px; background: white;" type="text" value="{url}" readonly="readonly" /></td><td rowspan="3" width="3" style="background-image: url({skins_url}/images/delim.png); background-repeat: repeat-y;"></td><td><input type="checkbox" name="active" value="1" {active_checked} /></td><td><b>Форма активна</b></td></tr>
<tr align="left" valign="top"><td class="contentRow" width="230"><b>ID / Название формы:</b><br><small><b>ID</b> - уникальный идентификатор</small></td><td><input style="width: 100px;" type="text" name="name" value="{name}"/> <input style="width: 350px;" type="text" name="title" value="{title}"/></td><td><input type="checkbox" name="jcheck" value="1" {jcheck_checked} /></td><td><b>Проверять ввод полей</b><br/><small>Включить JavaScript код для проверки заполнения полей</small></td></tr>
<tr align="left" valign="top"><td class="contentRow" width="230"><b>Описание формы:</b><br/><small>Выводится пользователю перед формой</small></td><td><textarea style="margin-left: 0px;" cols="72" rows="3" name="description">{description}</textarea></td><td><input type="checkbox" name="captcha" value="1" {captcha_checked} /></td><td>Использовать <i>captcha</i> :</b><br/><small>Требовать ввод проверочного кода для отправки запроса</small></td></tr>
<tr align="left" valign="top"><td class="contentRow" width="230"><b>Используемый шаблон:</b></td><td colspan="4"><select name="template">{template_options}</select></td></tr>
<tr align="left" valign="top">
 <td class="contentRow" width="230"><b>Email список рассылки:</b><br/><small>Список email адресов и групп пользователей, которым будут отправляться сообщения из данной формы.<br/><font color="red"><i>если создать только одну группу, то меню выбора получателей в форме отображаться не будет</i></font></small></td>
 <td colspan="4">
  <table>
   <thead>
    <tr><td>UID</td><td>Название группы</td><td>Список email адресов группы (через запятую)</td></tr>
   </thead>
   <tbody>
    {egroups}
   </tbody>
  </table>
 </td>  
</tr>
<tr><td colspan="6"><input type="submit" value="Сохранить"/></td></tr>
</table>
<hr/>
[/enabled]

<table width="100%">
<tr><td class="contentHead">ID поля</td><td class="contentHead">Наименование поля</td><td class="contentHead">Тип поля</td><td class="contentHead">Удалить</td></tr>
{entries}
<tr>
<td colspan="5" style="text-align: left; padding: 10px 10px 0 0;">
<a href="?mod=extra-config&plugin=feedback&action=row&form_id={id}">Добавить новое поле</a>
</td>
</tr>
</table>
</form>