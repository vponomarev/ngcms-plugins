<form action="?mod=extra-config&plugin=guestbook&action=insert_field" method="POST" name="fieldForm">
  <fieldset class="admGroup">
    <legend class="title">{{ lang['gbconfig']['f_add_title'] }}</legend>
    <table border="0" cellspacing="1" cellpadding="1" class="content">
      <tr class="contRow1">
        <td width="20%">{{ lang['gbconfig']['f_id'] }}</td>
        <td><input type="text" name="id" size="50"></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">{{ lang['gbconfig']['f_name'] }}</td>
        <td><input type="text" name="name" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">{{ lang['gbconfig']['f_placeholder'] }}</td>
        <td><input type="text" name="placeholder" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">{{ lang['gbconfig']['f_default_value'] }}</td>
        <td><input type="text" name="default_value" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">{{ lang['gbconfig']['f_required'] }}</td>
        <td><input type="checkbox" name="required" /></td>
      </tr>
      <tr class="contRow1">
        <td colspan=2 align="center">
          <input type="submit" class="button" value="{{ lang['gbconfig']['btn_add_field'] }}">
        </td>
      </tr>
    </table>
  </fieldset>
</form>
