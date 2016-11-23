<form action="?mod=extra-config&plugin=guestbook&action=update_field&id={{ field.id }}" method="POST" name="fieldForm">
  <fieldset class="admGroup">
    <legend class="title">{{ lang['gbconfig']['f_edit_title'] }} {{ field.name }}</legend>
    <table border="0" cellspacing="1" cellpadding="1" class="content">
      <tr class="contRow1">
        <td width="20%">{{ lang['gbconfig']['f_id'] }}</td>
        <td>{{ field.id }}</td>
      </tr>
      <tr class="contRow1">
        <td width="20%">{{ lang['gbconfig']['f_name'] }}</td>
        <td><input type="text" name="name" value="{{ field.name }}" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">{{ lang['gbconfig']['f_placeholder'] }}</td>
        <td><input type="text" name="placeholder" value="{{ field.placeholder }}" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">{{ lang['gbconfig']['f_default_value'] }}</td>
        <td><input type="text" name="default_value" value="{{ field.default_value }}" size="50" /></td>
      </tr>
      <tr class="contRow1">
        <td width="20%">{{ lang['gbconfig']['f_required'] }}</td>
        <td><input type="checkbox" name="required" value="{{ field.required }}" {% if field.required %}checked="checked"{% endif %}/></td>
      </tr>
      <tr class="contRow1">
        <td colspan=2 align="center">
          <input type="submit" class="button" value="{{ lang['gbconfig']['btn_edit_field'] }}">
        </td>
      </tr>
    </table>
  </fieldset>
</form>
