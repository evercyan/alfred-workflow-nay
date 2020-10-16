<?php
/**
 * 百度翻译
 */

require_once __DIR__ . '/base.php';

class Translate extends Base
{
    const API_TRANSLATE = 'http://api.fanyi.baidu.com/api/trans/vip/translate';

    public function baiduTranslate(string $query)
    {
        $from = 'zh';
        $to = 'en';
        if (preg_match('/^[a-zA-Z]+$/isU', $query)) {
            $from = 'en';
            $to = 'zh';
        }
        $param = [
            'q' => $query,
            'from' => $from,
            'to' => $to,
            'appid' => $_ENV['bd_translate_appid'] ?? '',
            'salt' => intval(microtime(true)),
        ];
        $param['sign'] = md5(sprintf(
            '%s%s%s%s',
            $param['appid'],
            $param['q'],
            $param['salt'],
            $_ENV['bd_translate_secret'] ?? ''
        ));
        $resp = $this->get(self::API_TRANSLATE . '?' . http_build_query($param));
        $list = json_decode($resp, true)['trans_result'] ?? [];
        if (empty($list)) {
            return [];
        }

        $result = [];
        foreach ($list as $item) {
            $result[] = [
                'title' => $item['dst'],
                'subtitle' => '',
                'arg' => $item['dst'],
                'variables' => [
                    'title' => $item['src'],
                    'content' => $item['dst'],
                ],
            ];
        }
        return $result;
    }

    public function run(array $argv)
    {
        $query = trim($argv[1] ?? '');
        if (empty($query)) {
            return;
        }

        return $this->render($this->baiduTranslate($query));
    }
}

(new Translate())->run($argv);
