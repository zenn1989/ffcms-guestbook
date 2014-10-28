<?php

use engine\template;
use engine\system;
use engine\extension;
use engine\csrf;
use engine\admin;
use engine\permission;
use engine\property;
use engine\database;
use engine\language;

class components_guestbook_back extends engine\singleton {

    const ITEM_PER_PAGE = 10;
    const SEARCH_PER_PAGE = 50;

    const FILTER_ALL = 0;
    const FILTER_MODERATE = 1;

	public function _update($from) {
		database::getInstance()->con()->query("UPDATE ".property::getInstance()->get('db_prefix')."_extensions SET `version` = '1.0.1', `compatable` = '2.0.4' WHERE `type` = 'components' AND dir = 'guestbook'");
	}
	
    public function _version() {
        return '1.0.1';
    }

    public function _compatable() {
        return '2.0.4';
    }

    public function _install() {
        $query = "CREATE TABLE IF NOT EXISTS `".property::getInstance()->get('db_prefix')."_com_guestbook` (
                  `id` int(12) NOT NULL AUTO_INCREMENT,
                  `text` text NOT NULL,
                  `lang` varchar(4) DEFAULT NULL,
                  `date` int(16) NOT NULL,
                  `author` varchar(128) NOT NULL,
                  `ip` varchar(16) NOT NULL,
                  `moderate` int(2) NOT NULL DEFAULT '0',
                  UNIQUE KEY `id` (`id`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
        database::getInstance()->con()->exec($query);
        $def_configs = 'a:3:{s:19:"count_guestmsg_page";s:2:"10";s:17:"timer_guestmsg_ip";s:3:"300";s:20:"premoderate_guestmsg";s:1:"1";}';
        $stmt = database::getInstance()->con()->prepare("UPDATE ".property::getInstance()->get('db_prefix')."_extensions SET `configs` = ? WHERE `type` = 'components' AND dir = 'guestbook'");
        $stmt->bindParam(1, $def_configs, \PDO::PARAM_STR);
        $stmt->execute();
        $lang = array(
            'ru' => array(
                'front' => array(
                    'guestbook_title' => 'Гостевая книга',
                    'guestbook_addmessage_btn' => 'Добавить сообщение',
                    'guestbook_writedby' => 'написал',
                    'guestbook_modal_title' => 'Написать сообщение',
                    'guestbook_modal_desc' => 'С помощью гостевой книги вы можете написать свое сообщение или отзыв, которое будет доступно для администрации сайта и других его посетителей.',
                    'guestbook_modal_name' => 'Ваше имя',
                    'guestbook_modal_captcha' => 'Проверка',
                    'guestbook_modal_send' => 'Отправить',
                    'guestbook_notify_nomsg' => 'Вы указали слишком короткое сообщение',
                    'guestbook_notify_noname' => 'Вы указали слишком короткое имя',
                    'guestbook_notify_captchaerr' => 'Вы ввели некоректные символы с защитного изображения',
                    'guestbook_notify_ipspam' => 'Вы слишком часто отправляете сообщения в гостевую книгу',
                    'guestbook_notify_success' => 'Ваше сообщение успешно добавлено и будет рассмотрено модератором'
                ),
                'back' => array(
                    'admin_components_guestbook.name' => 'Гостевая книга',
                    'admin_components_guestbook.desc' => 'Компонент реализующий функциональные возможности гостевой книги на сайте',
                    'admin_components_guestbook_manage_title' => 'Управление сообщениями',
                    'admin_components_guestbook_settings_title' => 'Настройки',
                    'admin_components_guestbook_delete_title' => 'Удаление сообщения',
                    'admin_components_guestbook_edit_uname' => 'Имя автора',
                    'admin_components_guestbook_edit_date' => 'Дата',
                    'admin_components_guestbook_edit_button_submit' => 'Сохранить',
                    'admin_components_guestbook_edit_title' => 'Редактирование сообщения',
                    'admin_components_guestbook_delete_button_submit' => 'Удалить',
                    'admin_components_guestbook_list_filter_title' => 'Фильтр',
                    'admin_components_guestbook_list_filter_all' => 'Все сообщения',
                    'admin_components_guestbook_list_filter_mod' => 'На модерации',
                    'admin_components_guestbook_list_th_author' => 'Автор',
                    'admin_components_guestbook_list_th_message' => 'Сообщение',
                    'admin_components_guestbook_list_th_date' => 'Дата',
                    'admin_components_guestbook_list_th_manage' => 'Управление',
                    'admin_components_guestbook_list_empty' => 'В гостевой книге еще нет сообщений',
                    'admin_components_guestbook_settings_label_pagecount_title' => 'Кол-во сообщений',
                    'admin_components_guestbook_settings_label_pagecount_desc' => 'Количество гостевых сообщений на 1 странице сайта',
                    'admin_components_guestbook_settings_label_delay_title' => 'Задержка отправки',
                    'admin_components_guestbook_settings_label_delay_desc' => 'Задержка между 2мя сообщениями в гостевую книгу с одинакового IP адреса в секундах',
                    'admin_components_guestbook_settings_label_premod_title' => 'Премодерация',
                    'admin_components_guestbook_settings_label_premod_desc' => 'Включить премодерацию сообщений от пользователей?'
                )
            ),
            'en' => array(
                'front' => array(
                    'guestbook_title' => 'Guest book',
                    'guestbook_addmessage_btn' => 'Add message',
                    'guestbook_writedby' => 'write',
                    'guestbook_modal_title' => 'Write message',
                    'guestbook_modal_desc' => 'On our guest book you can write message or review about our website and all users can read it',
                    'guestbook_modal_name' => 'Your name',
                    'guestbook_modal_captcha' => 'Captcha',
                    'guestbook_modal_send' => 'Send',
                    'guestbook_notify_nomsg' => 'Message is to short',
                    'guestbook_notify_noname' => 'Your name is to short',
                    'guestbook_notify_captchaerr' => 'Symbols from captcha image is wrong',
                    'guestbook_notify_ipspam' => 'You are to fast sending new message to book from this IP',
                    'guestbook_notify_success' => 'Your message are successful add to our book - moderator will check it'
                ),
                'back' => array(
                    'admin_components_guestbook.name' => 'Guest book',
                    'admin_components_guestbook.desc' => 'This component realise functions of guest book on your website',
                    'admin_components_guestbook_manage_title' => 'Manage messages',
                    'admin_components_guestbook_settings_title' => 'Settings',
                    'admin_components_guestbook_delete_title' => 'Delete message',
                    'admin_components_guestbook_edit_uname' => 'Author name',
                    'admin_components_guestbook_edit_date' => 'Date',
                    'admin_components_guestbook_edit_button_submit' => 'Save',
                    'admin_components_guestbook_edit_title' => 'Edit message',
                    'admin_components_guestbook_delete_button_submit' => 'Delete',
                    'admin_components_guestbook_list_filter_title' => 'Filter',
                    'admin_components_guestbook_list_filter_all' => 'All messages',
                    'admin_components_guestbook_list_filter_mod' => 'On moderate',
                    'admin_components_guestbook_list_th_author' => 'Author',
                    'admin_components_guestbook_list_th_message' => 'Message',
                    'admin_components_guestbook_list_th_date' => 'Date',
                    'admin_components_guestbook_list_th_manage' => 'Management',
                    'admin_components_guestbook_list_empty' => 'The guest book is empty',
                    'admin_components_guestbook_settings_label_pagecount_title' => 'Message count',
                    'admin_components_guestbook_settings_label_pagecount_desc' => 'Count of messages on 1 page website',
                    'admin_components_guestbook_settings_label_delay_title' => 'Send delay',
                    'admin_components_guestbook_settings_label_delay_desc' => 'Delay between 2 messages from 1 ip address in seconds',
                    'admin_components_guestbook_settings_label_premod_title' => 'Pre-moderation',
                    'admin_components_guestbook_settings_label_premod_desc' => 'Enable automatic pre-moderation for user messages?'
                )
            )
        );
        language::getInstance()->add($lang);
    }

    public function make() {
        $content = null;
        switch(system::getInstance()->get('make')) {
            case null:
            case 'list':
                $content = $this->viewListBook();
                break;
            case 'settings':
                $content = $this->viewSettings();
                break;
            case 'aprove':
                $this->viewHideAprove(true);
                break;
            case 'hide':
                $this->viewHideAprove(false);
                break;
            case 'edit':
                $content = $this->viewEditBook();
                break;
            case 'delete':
                $content = $this->viewDeleteBook();
                break;
        }
        return $content;
    }

    public function _accessData() {
        return array(
            'admin/components/guestbook',
            'admin/components/guestbook/list',
            'admin/components/guestbook/settings',
            'admin/components/guestbook/aprove',
            'admin/components/guestbook/hide',
            'admin/components/guestbook/edit',
            'admin/components/guestbook/delete',
        );
    }

    private function viewDeleteBook() {
        csrf::getInstance()->buildToken();
        $params = array();
        $params['extension']['title'] = admin::getInstance()->viewCurrentExtensionTitle();
        $item_id = (int)system::getInstance()->get('id');

        $stmt = database::getInstance()->con()->prepare("SELECT * FROM ".property::getInstance()->get('db_prefix')."_com_guestbook WHERE id = ?");
        $stmt->bindParam(1, $item_id, \PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->rowCount() != 1)
            system::getInstance()->redirect($_SERVER['PHP_SELF'] . '?object=components&action=guestbook');

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt = null;

        if(system::getInstance()->post('submit') && csrf::getInstance()->check()) {
            $stmt = database::getInstance()->con()->prepare("DELETE FROM ".property::getInstance()->get('db_prefix')."_com_guestbook WHERE id = ?");
            $stmt->bindParam(1, $item_id, \PDO::PARAM_INT);
            $stmt->execute();
            $stmt = null;
            system::getInstance()->redirect($_SERVER['PHP_SELF'] . '?object=components&action=guestbook');
        }

        $params['guestmsg'] = array(
            'text' => $result['text'],
            'name' => $result['author'],
            'ip' => $result['ip'],
            'date' => system::getInstance()->toDate($result['date'], 'h')
        );

        return template::getInstance()->twigRender('components/guestbook/delete.tpl', $params);
    }

    private function viewEditBook() {
        csrf::getInstance()->buildToken();
        $params = array();
        $params['extension']['title'] = admin::getInstance()->viewCurrentExtensionTitle();
        $item_id = (int)system::getInstance()->get('id');

        if(system::getInstance()->post('submit') && csrf::getInstance()->check()) {
            $g_msg = system::getInstance()->nohtml(system::getInstance()->post('gusttext'));
            $g_name = system::getInstance()->nohtml(system::getInstance()->post('guestname'));
            $g_date = system::getInstance()->toUnixTime(system::getInstance()->post('guestdate'));
            $stmt = database::getInstance()->con()->prepare("UPDATE ".property::getInstance()->get('db_prefix')."_com_guestbook SET `text` = ?, `author` = ?, `date` = ? WHERE id = ?");
            $stmt->bindParam(1, $g_msg, \PDO::PARAM_STR);
            $stmt->bindParam(2, $g_name, \PDO::PARAM_STR);
            $stmt->bindParam(3, $g_date, \PDO::PARAM_INT);
            $stmt->bindParam(4, $item_id, \PDO::PARAM_INT);
            $stmt->execute();
            $stmt = null;
            $params['notify']['save_success'] = true;
        }

        $stmt = database::getInstance()->con()->prepare("SELECT * FROM ".property::getInstance()->get('db_prefix')."_com_guestbook WHERE id = ?");
        $stmt->bindParam(1, $item_id, \PDO::PARAM_INT);
        $stmt->execute();

        if($stmt->rowCount() != 1)
            system::getInstance()->redirect($_SERVER['PHP_SELF'] . '?object=components&action=guestbook');

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt = null;

        $params['guestmsg'] = array(
            'text' => $result['text'],
            'name' => $result['author'],
            'ip' => $result['ip'],
            'date' => system::getInstance()->toDate($result['date'], 'h')
        );

        return template::getInstance()->twigRender('components/guestbook/edit.tpl', $params);
    }

    private function viewHideAprove($show = false) {
        $moderate = $show ? 0 : 1;
        $item_id = (int)system::getInstance()->get('id');
        $stmt = database::getInstance()->con()->prepare("UPDATE ".property::getInstance()->get('db_prefix')."_com_guestbook SET `moderate` = ? WHERE `id` = ?");
        $stmt->bindParam(1, $moderate, \PDO::PARAM_INT);
        $stmt->bindParam(2, $item_id, \PDO::PARAM_INT);
        $stmt->execute();
        $stmt = null;
        system::getInstance()->redirect($_SERVER['PHP_SELF'] . '?object=components&action=guestbook');
    }

    private function viewListBook() {
        csrf::getInstance()->buildToken();
        $params = array();
        $params['extension']['title'] = admin::getInstance()->viewCurrentExtensionTitle();

        if(system::getInstance()->post('deleteSelected') && csrf::getInstance()->check()) {
            if(permission::getInstance()->have('global/owner') || permission::getInstance()->have('admin/components/guestbook/delete')) {
                $toDelete = system::getInstance()->post('check_array');
                if(is_array($toDelete) && sizeof($toDelete) > 0) {
                    $listDelete = system::getInstance()->altimplode(',', $toDelete);
                    if(system::getInstance()->isIntList($listDelete)) {
                        database::getInstance()->con()->query("DELETE FROM ".property::getInstance()->get('db_prefix')."_com_guestbook WHERE id IN (".$listDelete.")");
                    }
                }
            }
        }

        $index_start = (int)system::getInstance()->get('index');
        $db_index = $index_start * self::ITEM_PER_PAGE;

        $filter = (int)system::getInstance()->get('filter');

        $stmt = null;
        if($filter === self::FILTER_MODERATE) { // 1
            $stmt = database::getInstance()->con()->prepare("SELECT * FROM ".property::getInstance()->get('db_prefix')."_com_guestbook WHERE `moderate` = 1 ORDER BY id DESC LIMIT ?,".self::ITEM_PER_PAGE);
            $stmt->bindParam(1, $db_index, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = database::getInstance()->con()->prepare("SELECT * FROM ".property::getInstance()->get('db_prefix')."_com_guestbook ORDER BY id DESC LIMIT ?,".self::ITEM_PER_PAGE);
            $stmt->bindParam(1, $db_index, PDO::PARAM_INT);
            $stmt->execute();
        }
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt = null;

        foreach($result as $row) {
            $params['guestmsg'][] = array(
                'text' => system::getInstance()->sentenceSub(system::getInstance()->stringInline($row['text']), 70),
                'id' => $row['id'],
                'author' => $row['author'],
                'date' => system::getInstance()->toDate($row['date'], 'd'),
                'ip' => $row['ip'],
                'moderate' => $row['moderate']
            );
        }
        $params['pagination'] = template::getInstance()->showFastPagination($index_start, self::ITEM_PER_PAGE, $this->getTotalGBCount($filter), '?object=components&action=guestbook&filter='.$filter.'&index=');
        return template::getInstance()->twigRender('components/guestbook/list.tpl', $params);
    }

    private function viewSettings() {
        csrf::getInstance()->buildToken();
        $params = array();
        if(system::getInstance()->post('submit')) {
            if(admin::getInstance()->saveExtensionConfigs() && csrf::getInstance()->check()) {
                $params['notify']['save_success'] = true;
            }
        }
        $params['extension']['title'] = admin::getInstance()->viewCurrentExtensionTitle();
        $params['config']['count_guestmsg_page'] = extension::getInstance()->getConfig('count_guestmsg_page', 'guestbook', extension::TYPE_COMPONENT, 'int');
        $params['config']['timer_guestmsg_ip'] = extension::getInstance()->getConfig('timer_guestmsg_ip', 'guestbook', extension::TYPE_COMPONENT, 'int');
        $params['config']['premoderate_guestmsg'] = extension::getInstance()->getConfig('premoderate_guestmsg', 'guestbook', extension::TYPE_COMPONENT, 'int');

        return template::getInstance()->twigRender('components/guestbook/settings.tpl', $params);
    }

    private function getTotalGBCount($filter) {
        $stmt = database::getInstance()->con()->prepare("SELECT COUNT(*) FROM ".property::getInstance()->get('db_prefix')."_com_guestbook WHERE `moderate` = ?");
        $stmt->bindParam(1, $filter, \PDO::PARAM_INT);
        $stmt->execute();
        $res = $stmt->fetch();
        $stmt = null;
        return $res[0];
    }
}