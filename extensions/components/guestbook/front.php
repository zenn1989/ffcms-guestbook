<?php

use engine\template;
use engine\extension;
use engine\system;
use engine\language;
use engine\database;
use engine\property;
use engine\router;
use engine\meta;

class components_guestbook_front extends engine\singleton {
    protected static $instance = null;

    public static function getInstance() {
        if(is_null(self::$instance))
            self::$instance = new self();
        return self::$instance;
    }

    public function make() {
        $params = array();
        $way = router::getInstance()->shiftUriArray();


        $page_index = (int)$way[0];
        $item_per_page = extension::getInstance()->getConfig('count_guestmsg_page', 'guestbook', extension::TYPE_COMPONENT, 'int');
        if($item_per_page < 1)
            $item_per_page = 10;
        $page_offset = $page_index * $item_per_page;

        $meta_title = language::getInstance()->get('guestbook_title');
        if($page_index > 0)
            $meta_title .= " - ".$page_index;
        meta::getInstance()->add('title', $meta_title);

        $params['cfg']['captcha_full'] = extension::getInstance()->getConfig('captcha_type', 'captcha', 'hooks') == "recaptcha" ? true : false;
        $params['captcha'] = extension::getInstance()->call(extension::TYPE_HOOK, 'captcha')->show();
        $used_language = language::getInstance()->getUseLanguage();

        if(system::getInstance()->post('submit')) {
            $message = system::getInstance()->nohtml(system::getInstance()->post('gusttext'));
            $name = system::getInstance()->nohtml(system::getInstance()->post('guestname'));
            $date = time();
            $ip = system::getInstance()->getRealIp();

            if(system::getInstance()->length($message) < 1)
                $params['notify']['smalltext'] = true;
            if(system::getInstance()->length($name) < 1)
                $params['notify']['smallname'] = true;
            if (!extension::getInstance()->call(extension::TYPE_HOOK, 'captcha')->validate(system::getInstance()->post('captcha')))
                $params['notify']['captcha_error'] = true;

            $stmt = database::getInstance()->con()->prepare("SELECT `date` FROM ".property::getInstance()->get('db_prefix')."_com_guestbook WHERE `ip` = ? ORDER BY `date` DESC LIMIT 1");
            $stmt->bindParam(1, $ip, \PDO::PARAM_STR);
            $stmt->execute();
            if($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $lastpost = $result['date'];
                $config_timer = extension::getInstance()->getConfig('timer_guestmsg_ip', 'guestbook', extension::TYPE_COMPONENT, 'int');
                if($config_timer < 1)
                    $config_timer = 300;
                if(($date - $lastpost) < $config_timer)
                    $params['notify']['spamdetect'] = true;
            }

            $stmt = null;

            if(sizeof($params['notify']) == 0) {
                $config_moderate = extension::getInstance()->getConfig('premoderate_guestmsg', 'guestbook', extension::TYPE_COMPONENT, 'int') == 1 ? 1 : 0;
                $stmt = database::getInstance()->con()->prepare("INSERT INTO ".property::getInstance()->get('db_prefix')."_com_guestbook (`text`, `lang`, `date`, `author`, `ip`, `moderate`) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bindParam(1, $message, \PDO::PARAM_STR);
                $stmt->bindParam(2, $used_language, \PDO::PARAM_STR);
                $stmt->bindParam(3, $date, \PDO::PARAM_INT);
                $stmt->bindParam(4, $name, \PDO::PARAM_STR);
                $stmt->bindParam(5, $ip, \PDO::PARAM_STR);
                $stmt->bindParam(6, $config_moderate, \PDO::PARAM_INT);
                $stmt->execute();
                $stmt = null;
                $params['notify']['add_success'] = true;
            }
        }

        $stmt = database::getInstance()->con()->prepare("SELECT * FROM ".property::getInstance()->get('db_prefix')."_com_guestbook WHERE lang = ? AND moderate = 0 ORDER BY `date` DESC LIMIT ?,?");
        $stmt->bindParam(1, $used_language, \PDO::PARAM_STR);
        $stmt->bindParam(2, $page_offset, \PDO::PARAM_INT);
        $stmt->bindParam(3, $item_per_page, \PDO::PARAM_INT);
        $stmt->execute();
        while($res = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $params['guestmsg'][] = array(
                'text' => str_replace("\n", "<br />", $res['text']),
                'name' => $res['author'],
                'date' => system::getInstance()->toDate($res['date'], 'd')
            );
        }

        $params['pagination'] = template::getInstance()->showFastPagination($page_index, $item_per_page, $this->totalCountGuestMsg($used_language), 'guestbook');

        $tpl = template::getInstance()->twigRender('components/guestbook/list.tpl', $params);
        template::getInstance()->set(template::TYPE_CONTENT, 'body', $tpl);
    }

    public function totalCountGuestMsg($lang) {
        $stmt = database::getInstance()->con()->prepare("SELECT COUNT(*) FROM ".property::getInstance()->get('db_prefix')."_com_guestbook WHERE `moderate` = 0 AND `lang` = ?");
        $stmt->bindParam(1, $lang, \PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetch();
        $stmt = null;
        return $res[0];
    }
}