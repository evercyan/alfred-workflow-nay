<?php
/**
 * 字母简写
 */

require_once __DIR__ . '/base.php';

class Abbr extends Base
{
    const TITLE = '字母简写';
    const API = 'https://lab.magiconch.com/api/nbnhhsh/guess';

    public function api(string $query)
    {
        $resp = $this->post(self::API, [
            'text' => $query,
        ]);
        $list = $resp[0]['trans'] ?? [];
        if (empty($list)) {
            return $this->renderError('无数据');
        }

        $result = [];
        foreach ($list as $item) {
            $result[] = [
                'title' => $item,
                'subtitle' => '',
                'arg' => $item,
                'variables' => [
                    'title' => sprintf('%s-%s', self::TITLE, $query),
                    'content' => $item,
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

(new Abbr())->run($argv);
