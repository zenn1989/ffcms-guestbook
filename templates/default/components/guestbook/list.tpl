<ol class="breadcrumb">
    <li><a href="{{ system.url }}">{{ language.global_main }}</a></li>
    <li class="active">{{ language.guestbook_title }}</li>
</ol>
{% import 'macro/notify.tpl' as notifytpl %}
<h1>{{ language.guestbook_title }}</h1>
<hr />
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
            <a data-toggle="modal" data-target="#guestmsg" class="btn btn-success"><i class="fa fa-plus"></i> {{ language.guestbook_addmessage_btn }}</a>
        </div>
    </div>
</div>
{% if notify.smalltext %}
    {{ notifytpl.error(language.guestbook_notify_nomsg) }}
{% endif %}
{% if notify.smallname %}
    {{ notifytpl.error(language.guestbook_notify_noname) }}
{% endif %}
{% if notify.captcha_error %}
    {{ notifytpl.error(language.guestbook_notify_captchaerr) }}
{% endif %}
{% if notify.spamdetect %}
    {{ notifytpl.error(language.guestbook_notify_ipspam) }}
{% endif %}
{% if notify.add_success %}
    {{ notifytpl.success(language.guestbook_notify_success) }}
{% endif %}

{% for row in guestmsg %}
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">{{ row.name }} {{ language.guestbook_writedby }}({{ row.date }}):</div>
            <div class="panel-body">
                {{ row.text }}
            </div>
        </div>
    </div>
</div>
{% endfor %}

{{ pagination }}


<div class="modal fade modalguestmsg" tabindex="-1" role="dialog" id="guestmsg" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="modal-title">{{ language.guestbook_modal_title }}</h3>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <p>{{ language.guestbook_modal_desc }}</p>
                    <form class="form-horizontal" action="" method="post" role="form">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ language.guestbook_modal_name }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="guestname" placeholder="Ivan Petrov" class="form-control" required="required" />
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <textarea class="col-sm-10 form-control" name="gusttext" placeholder="Message..." rows="8" maxlength="3000" required="required"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">{{ language.guestbook_modal_captcha }}</label>
                            <div class="col-sm-9">
                                {% if cfg.captcha_full %}
                                    <script>
                                        var RecaptchaOptions = { theme : 'white' };
                                    </script>
                                    {{ captcha }}
                                {% else %}
                                    <img src="{{ captcha }}" id="captcha"/><a href="#captcha" onclick="document.getElementById('captcha').src='{{ captcha }}?'+Math.random();"><i class="fa fa-refresh"></i></a><br/>
                                    <input type="text" name="captcha" class="form-control" required="required">
                                {% endif %}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="submit" name="submit" value="{{ language.guestbook_modal_send }}" class="btn btn-success" />
                            </div>
                        </div>
                    </form>
                 </div>
            </div>
        </div>
    </div>
</div>