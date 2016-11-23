<form action="/engine/admin.php?mod=extra-config&plugin=guestbook&action=modify" method="post" name="check_messages">
  <table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
    <tr class="contHead" align="left">
      <td width="5%">{{ lang['gbconfig']['message_id'] }}</td>
      <td width="15%">{{ lang['gbconfig']['message_date'] }}</td>
      <td width="20%">{{ lang['gbconfig']['message_content'] }}</td>
      <td width="20%">{{ lang['gbconfig']['message_answer'] }}</td>
      <td width="10%">{{ lang['gbconfig']['message_ip'] }}</td>
      <td width="10%">{{ lang['gbconfig']['message_status'] }}</td>
      <td colspan="2" width="5%">{{ lang['gbconfig']['message_action'] }}</td>
      <td width="5%">
        <input class="check" type="checkbox" name="master_box" onclick="javascript:check_uncheck_all(check_messages)" />
      </td>
    </tr>
    {% for entry in entries %}
    <tr align="left">
      <td width="5%" class="contentEntry1">{{ entry.id }}</td>
      <td width="15%" class="contentEntry1">{{ entry.postdate|date("d.m.Y H:i:s") }}</td>
      <td width="20%" class="contentEntry1">{{ entry.message }}</td>
      <td width="20%" class="contentEntry1">{{ entry.answer }}</td>
      <td width="10%" class="contentEntry1">{{ entry.ip }}</td>
      <td width="10%" class="contentEntry1">{% if entry.status == '1' %}{{ lang['gbconfig']['message_active'] }}{% elseif entry.status == '0' %}{{ lang['gbconfig']['message_inactive'] }}{% endif %}</td>
      <td nowrap style="text-align: center;">
        <a href="?mod=extra-config&plugin=guestbook&action=edit_message&id={{ entry.id }}" title="{{ lang['gbconfig']['message_edit'] }}">
          <img src="{{ skins_url }}/images/add_edit.png" alt="EDIT" width="12" height="12" />
        </a>
      </td>
      <td nowrap style="text-align: center;">
        <a onclick="return confirm('{{ lang['gbconfig']['message_confirm'] }}');" href="?mod=extra-config&plugin=guestbook&action=delete_message&id={{ entry.id }}" title="{{ lang['gbconfig']['message_delete'] }}">
          <img src="{{ skins_url }}/images/delete.gif" alt="DEL" width="12" height="12" />
        </a>
      </td>
      <td width="5%" class="contentEntry1">
        <input name="selected_message[]" value="{{ entry.id }}" class="check" type="checkbox" />
      </td>
    </tr>
    {% else %}
    <tr align="left">
      <td colspan="10" class="contentEntry1">{{ lang['gbconfig']['message_noent'] }}</td>
    </tr>
    {% endfor %}
    <tr>
      <td width="100%" colspan="10">&nbsp;</td>
    </tr>
    <tr align="center">
      <td colspan="10" class="contentEdit" align="right" valign="top">
      <div style="text-align: left;">
        <span>{{ lang['gbconfig']['message_options'] }}</span>
        <select name="subaction" style="font: 12px Verdana, Courier, Arial; width: 230px;">
          <option value="">{{ lang['gbconfig']['message_opt_default'] }}</option>
          <option value="mass_approve">{{ lang['gbconfig']['message_opt_activate'] }}</option>
          <option value="mass_forbidden">{{ lang['gbconfig']['message_opt_deactivate'] }}</option>
          <option value="" style="background-color: #E0E0E0;" disabled="disabled">{{ lang['gbconfig']['message_opt_separator'] }}</option>
          <option value="mass_delete">{{ lang['gbconfig']['message_opt_delete'] }}</option>
        </select>
        <input type="submit" value="{{ lang['gbconfig']['message_opt_submit'] }}" class="button" />
      </div>
      </td>
    </tr>
    <tr>
      <td width="100%" colspan="10">&nbsp;</td>
    </tr>
    <tr>
      <td align="center" colspan="10" class="contentHead">{{ pagesss }}</td>
    </tr>
  </table>
</form>
