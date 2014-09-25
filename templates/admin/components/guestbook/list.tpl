{% import 'macro/scriptdata.tpl' as scriptdata %}
{% import 'macro/notify.tpl' as notifytpl %}
<h1>{{ extension.title }}<small>{{ language.admin_components_guestbook_manage_title }}</small></h1>
<hr />
{% include 'components/guestbook/menu_include.tpl' %}
<div class="row">
    <div class="col-lg-12">
        <div class="pull-left">
            <div class="btn-group">
                <button type="button" class="btn btn-default">{{ language.admin_components_guestbook_list_filter_title }}</button>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="?object=components&action=guestbook&make=list&filter=0">{{ language.admin_components_guestbook_list_filter_all }}</a></li>
                    <li><a href="?object=components&action=guestbook&make=list&filter=1">{{ language.admin_components_guestbook_list_filter_mod }}</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
{% if guestmsg %}
    <form action="" method="post" onsubmit="return confirm('{{ language.admin_onsubmit_warning }}');">
        <table class="table table-bordered table-responsive">
            <thead>
            <tr>
                <th>ID</th>
                <th>{{ language.admin_components_guestbook_list_th_author }}</th>
                <th>{{ language.admin_components_guestbook_list_th_message }}</th>
                <th>{{ language.admin_components_guestbook_list_th_date }}</th>
                <th>{{ language.admin_components_guestbook_list_th_manage }}</th>
            </tr>
            </thead>
            <tbody>
            {% for row in guestmsg %}
                <tr>
                    <td><input type="checkbox" name="check_array[]" class="check_array" value="{{ row.id }}"/> {{ row.id }}</td>
                    <td>{% if row.moderate %}<i class="fa fa-eye-slash"></i> {% endif %} {{ row.author }}[{{ row.ip }}]</td>
                    <td>{{ row.text|escape }}</td>
                    <td>{{ row.date }}</td>
                    <td class="text-center">
                        <a href="?object=components&action=guestbook&make=edit&id={{ row.id }}" title="Edit"><i class="fa fa-pencil-square-o fa-lg"></i></a>
                        <a href="?object=components&action=guestbook&make=delete&id={{ row.id }}" title="Delete"><i class="fa fa-trash-o fa-lg"></i></a>
                        {% if row.moderate %}
                            <a href="?object=components&action=guestbook&make=aprove&id={{ row.id }}" title="Aprove"><i class="fa fa-check fa-lg"></i></a>
                        {% else %}
                            <a href="?object=components&action=guestbook&make=hide&id={{ row.id }}" title="hide"><i class="fa fa-lock fa-lg"></i></a>
                        {% endif %}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
        <a id="checkAll" class="btn btn-default">{{ language.admin_checkbox_all }}</a>
        <input type="hidden" name="csrf_token" value="{{ system.csrf_token }}" />
        <input type="submit" name="deleteSelected" value="{{ language.admin_checkbox_delselect }}" class="btn btn-danger" />
        {{ scriptdata.checkjs('#checkAll', '.check_array') }}
    </form>
    {% if search.value|length < 1 %}
        {{ pagination }}
    {% endif %}
{% else %}
    {{ notifytpl.warning(language.admin_components_guestbook_list_empty) }}
{% endif %}