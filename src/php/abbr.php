<?php
/**
 * 猜测简拼
 */

require_once __DIR__ . '/base.php';

class Abbr extends Base
{
    const API_ABBR = 'https://lab.magiconch.com/api/nbnhhsh/guess';

    public function guess(string $query)
    {
        $resp = $this->post(self::API_ABBR, [
            'text' => $query,
        ]);
        $list = $resp[0]['trans'] ?? [];
        if (empty($list)) {
            return [];
        }

        $result = [];
        foreach ($list as $item) {
            $result[] = [
                'title' => $item,
                'subtitle' => '',
                'arg' => $item,
                'variables' => [
                    'title' => $item,
                    'content' => $item,
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

        return $this->render($this->guess($query));
    }
}

(new Abbr())->run($argv);
