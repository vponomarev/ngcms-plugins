<form method="post" action="">
  <fieldset class="admGroup">
    <legend class="title">{{ lang['gbconfig']['settings_title'] }}</legend>
    <table width="100%" border="0" class="content">
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_usmilies'] }}</td>
        <td class="contentEntry2" valign="top">
          <select name="usmilies">
            <option value="1" {% if usmilies == '1' %}selected{% endif %}>{{ lang['gbconfig']['settings_yes'] }}</option>
            <option value="0" {% if usmilies == '0' %}selected{% endif %}>{{ lang['gbconfig']['settings_no'] }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_bbcodes'] }}</td>
        <td class="contentEntry2" valign="top">
          <select name="ubbcodes">
            <option value="1" {% if ubbcodes == '1' %}selected{% endif %}>{{ lang['gbconfig']['settings_yes'] }}</option>
            <option value="0" {% if ubbcodes == '0' %}selected{% endif %}>{{ lang['gbconfig']['settings_no'] }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_minlength'] }}</td>
        <td class="contentEntry2" valign="top"><input name="minlength" type="text" size="10" value="{{ minlength }}" /></td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_maxlength'] }}<br /><small>{{ lang['gbconfig']['settings_max_descr'] }}</small></td>
        <td class="contentEntry2" valign="top"><input name="maxlength" type="text" size="10" value="{{ maxlength }}" /></td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_perpage'] }}</td>
        <td class="contentEntry2" valign="top"><input name="perpage" type="text" size="10" value="{{ perpage }}" /></td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_order'] }}</td>
        <td class="contentEntry2" valign="top">
          <select name="order">
            <option value="DESC" {% if order == 'DESC' %}selected{% endif %}>{{ lang['gbconfig']['settings_order_desc'] }}</option>
            <option value="ASC" {% if order == 'ASC' %}selected{% endif %}>{{ lang['gbconfig']['settings_order_asc'] }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_date'] }}</td>
        <td class="contentEntry2" valign="top"><input name="date" type="text" size="10" value="{{ date }}" /></td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_url'] }}</td>
        <td class="contentEntry2" valign="top">
          <select name="url">
            <option value="0" {% if url == '0' %}selected{% endif %}>{{ lang['gbconfig']['settings_no'] }}</option>
            <option value="1" {% if url == '1' %}selected{% endif %}>{{ lang['gbconfig']['settings_yes'] }}</option>
          </select>
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset class="admGroup">
    <legend class="title">{{ lang['gbconfig']['settings_access'] }}</legend>
    <table width="100%" border="0" class="content">
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_anonymous'] }}</td>
        <td class="contentEntry2" valign="top">
          <select name="guests">
            <option value="1" {% if guests == '1' %}selected{% endif %}>{{ lang['gbconfig']['settings_yes'] }}</option>
            <option value="0" {% if guests == '0' %}selected{% endif %}>{{ lang['gbconfig']['settings_no'] }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_captcha'] }}</td>
        <td class="contentEntry2" valign="top">
          <select name="ecaptcha">
            <option value="1" {% if ecaptcha == '1' %}selected{% endif %}>{{ lang['gbconfig']['settings_yes'] }}</option>
            <option value="0" {% if ecaptcha == '0' %}selected{% endif %}>{{ lang['gbconfig']['settings_no'] }}</option>
          </select>
        </td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_public_key'] }}</td>
        <td class="contentEntry2" valign="top"><input name="public_key" type="text" size="100" value="{{ public_key }}" /></td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_private_key'] }}</td>
        <td class="contentEntry2" valign="top"><input name="private_key" type="text" size="100" value="{{ private_key }}" /></td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_approve'] }}</td>
        <td class="contentEntry2" valign="top">
          <select name="approve_msg">
            <option value="1" {% if approve_msg == '1' %}selected{% endif %}>{{ lang['gbconfig']['settings_yes'] }}</option>
            <option value="0" {% if approve_msg == '0' %}selected{% endif %}>{{ lang['gbconfig']['settings_no'] }}</option>
          </select>
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset class="admGroup">
    <legend class="title">{{ lang['gbconfig']['settings_admin'] }}</legend>
    <table width="100%" border="0" class="content">
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_perpage'] }}</td>
        <td class="contentEntry2" valign="top"><input name="admin_count" type="text" size="10" value="{{ admin_count }}" /></td>
      </tr>
      <tr>
        <td class="contentEntry1" valign="top">{{ lang['gbconfig']['settings_email'] }}</td>
        <td class="contentEntry2" valign="top"><input name="send_email" type="text" size="100" value="{{ send_email }}" /></td>
      </tr>
    </table>
  </fieldset>

  <table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td width="100%" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td width="100%" colspan="2" class="contentEdit" align="center">
        <input name="submit" type="submit"  value="{{ lang['gbconfig']['settings_save'] }}" class="button" />
      </td>
    </tr>
  </table>
</form>
