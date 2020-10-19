<?php
/**
 * 历史上的今天
 */

require_once __DIR__ . '/base.php';

class History extends Base
{
    const TITLE = '历史上的今天';
    const API = 'http://api.avatardata.cn/HistoryToday/LookUp?key=%s&yue=%s&ri=%s&type=1&page=1&rows=50';

    public function api(string $query)
    {
        $key = $_ENV['api_history_key'] ?? '';
        if (empty($key)) {
            return $this->renderError(self::TITLE, '请配置 api_history_key');
        }

        try {
            $objs = explode('-', $query);
            $month = intval($objs[0] ?? '');
            $day = intval($objs[1] ?? '');
            if ($month <= 0
                || $day <= 0
                || !strtotime(sprintf('%s-%s-%d', date('Y'), $month, $day))) {
                throw new Exception('invalid');
            }
        } catch (Exception $e) {
            $month = date('m');
            $day = date('d');
        }
        $url = sprintf(self::API, $key, $month, $day);
        $resp = $this->get($url);
        $list = json_decode($resp, true)['result'] ?? [];
        if (empty($list)) {
            return $this->renderError('无数据');
        }

        $result = [];
        foreach ($list as $item) {
            $date = sprintf('%s-%s-%s', $item['year'], $item['month'], $item['day']);
            $title = sprintf('%s %s', self::TITLE, $date);
            $result[] = [
                'title' => $item['title'],
                'subtitle' => $date,
                'arg' => $item['title'],
                'variables' => [
                    'title' => $title,
                    'content' => $item['title'],
                ],
            ];
        }
        return $this->renderList($result);
    }

    public function run(array $argv)
    {
        $this->api(trim($argv[1] ?? ''));
    }
}

(new History())->run($argv);
