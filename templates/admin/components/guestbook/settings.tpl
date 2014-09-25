{% import 'macro/settings.tpl' as settingstpl %}
{% import 'macro/notify.tpl' as notifytpl %}
<h1>{{ extension.title }}<small>{{ language.admin_components_guestbook_settings_title }}</small></h1>
<hr />
{% include 'components/guestbook/menu_include.tpl' %}
{% if notify.save_success %}
    {{ notifytpl.success(language.admin_extension_config_update_success) }}
{% endif %}
<form action="" method="post" class="form-horizontal" role="form">
    <input type="hidden" name="csrf_token" value="{{ system.csrf_token }}" />
    <fieldset>
        {{ settingstpl.textgroup('count_guestmsg_page', config.count_guestmsg_page, language.admin_components_guestbook_settings_label_pagecount_title, language.admin_components_guestbook_settings_label_pagecount_desc) }}
        {{ settingstpl.textgroup('timer_guestmsg_ip', config.timer_guestmsg_ip, language.admin_components_guestbook_settings_label_delay_title, language.admin_components_guestbook_settings_label_delay_desc) }}
        {{ settingstpl.selectYNgroup('premoderate_guestmsg', config.premoderate_guestmsg, language.admin_components_guestbook_settings_label_premod_title, language.admin_components_guestbook_settings_label_premod_desc, _context) }}
        <input type="submit" name="submit" value="{{ language.admin_extension_save_button }}" class="btn btn-success"/>
    </fieldset>
</form>