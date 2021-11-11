<tr>
    <th colspan="2">Управление публикацией новостей</th>
</tr>

{% if (flags.can_publish) %}
<tr>
    <td>
        Дата включения:<br />
        <small>( в формате ДД.ММ.ГГГГ ЧЧ:ММ )</small>
    </td>
    <td>
        <input type="text" name="nsched_activate" autocomplete="off" class="form-control" />
    </td>
</tr>
{% endif %}

{% if (flags.can_unpublish) %}
<tr>
    <td>
        Дата отключения:<br />
        <small>( в формате ДД.ММ.ГГГГ ЧЧ:ММ )</small>
    </td>
    <td>
        <input type="text" name="nsched_deactivate" autocomplete="off" class="form-control" />
    </td>
</tr>
{% endif %}

<script>
    $('[name="nsched_activate"]').datetimepicker({
        format: '{{ format_datetime }}',
    });

    $('[name="nsched_deactivate"]').datetimepicker({
        format: '{{ format_datetime }}',
    });
</script>
