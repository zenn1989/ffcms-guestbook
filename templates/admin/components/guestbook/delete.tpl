{% import 'macro/notify.tpl' as notifytpl %}
<h1>{{ extension.title }}<small>{{ language.admin_components_guestbook_delete_title }}</small></h1>
<hr />
{% include 'components/guestbook/menu_include.tpl' %}
<form class="form-horizontal" action="" method="post" role="form">
    <input type="hidden" name="csrf_token" value="{{ system.csrf_token }}" />
    <div class="form-group">
        <label class="col-sm-3 control-label">{{ language.admin_components_guestbook_edit_uname }}</label>
        <div class="col-sm-9">
            <input type="text" name="guestname" class="form-control" value="{{ guestmsg.name }}" disabled />
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <textarea class="col-sm-10 form-control" name="gusttext" rows="8" disabled>{{ guestmsg.text }}</textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">IP</label>
        <div class="col-sm-9">
            <input type="text" value="{{ guestmsg.ip }}" name="guestip" class="form-control" disabled />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label">{{ language.admin_components_guestbook_edit_date }}</label>
        <div class="col-sm-9">
            <input name="guestdate" type="text" value="{{ guestmsg.date }}" class="form-control" disabled />
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input type="submit" name="submit" value="{{ language.admin_components_guestbook_delete_button_submit }}" class="btn btn-danger" />
        </div>
    </div>
</form>