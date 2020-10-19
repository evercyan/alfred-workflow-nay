<?php
/**
 * 百度翻译
 */

require_once __DIR__ . '/base.php';

class Translate extends Base
{
    const TITLE = '百度翻译';
    const API = 'http://api.fanyi.baidu.com/api/trans/vip/translate';

    public function api(string $query)
    {
        if (empty($_ENV['bd_translate_appid'])
            || empty($_ENV['bd_translate_secret'])) {
            return $this->renderError('请配置 bd_translate_appid 和 bd_translate_secret');
        }

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
        $resp = $this->get(self::API . '?' . http_build_query($param));
        $list = json_decode($resp, true)['trans_result'] ?? [];
        if (empty($list)) {
            return $this->renderError('无数据');
        }

        $result = [];
        foreach ($list as $item) {
            $result[] = [
                'title' => $item['dst'],
                'subtitle' => '',
                'arg' => $item['dst'],
                'variables' => [
                    'title' => sprintf('%s-%s', self::TITLE, $item['src']),
                    'content' => $item['dst'],
                ],
            ];
        }

        return $this->renderList($result);
    }

    public function run(array $argv)
    {
        $query = trim($argv[1] ?? '');
        if (empty($query)) {
            return;
        }
        $this->api($query);
    }
}

(new Translate())->run($argv);
