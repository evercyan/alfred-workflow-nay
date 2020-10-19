<?php
/**
 * nay 菜单
 */

require_once __DIR__ . '/base.php';

class Menu extends Base
{
    const MENU_LIST = [
        [
            'subtitle' => 'URL 编码',
            'title' => 'urle',
        ],
        [
            'subtitle' => 'URL 解码',
            'title' => 'urld',
        ],
        [
            'subtitle' => 'JSON 收缩',
            'title' => 'jsons',
        ],
        [
            'subtitle' => 'JSON 扩张',
            'title' => 'jsone',
        ],
        [
            'subtitle' => 'Unicode 编码',
            'title' => 'unie',
        ],
        [
            'subtitle' => 'Unicode 解码',
            'title' => 'unid',
        ],
        [
            'subtitle' => '全角转半角',
            'title' => 'half',
        ],
        [
            'subtitle' => '半角转全角',
            'title' => 'full',
        ],
        [
            'subtitle' => '时间戳转日期',
            'title' => 't2d',
        ],
        [
            'subtitle' => '日期转时间戳',
            'title' => 'd2t',
        ],
        [
            'subtitle' => '当前时间戳',
            'title' => 'now',
        ],
        [
            'subtitle' => '生成 md5',
            'title' => 'md5',
        ],
        [
            'subtitle' => '显示 IP',
            'title' => 'ip',
        ],
        [
            'subtitle' => '强杀进程',
            'title' => 'kill',
        ],
        [
            'subtitle' => '文字识别',
            'title' => 'ocr',
        ],
        [
            'subtitle' => '生成二维码',
            'title' => 'qr',
        ],
        [
            'subtitle' => '斗图',
            'title' => 'dt',
        ],
        [
            'subtitle' => '字母简写',
            'title' => 'abbr',
        ],
        [
            'subtitle' => '百度翻译',
            'title' => 'f',
        ],
        [
            'subtitle' => '历史上的今天',
            'title' => 'history',
        ],
    ];

    public function run(array $argv)
    {
        $query = trim($argv[1] ?? '');

        $result = [];
        foreach (self::MENU_LIST as $item) {
            if (!empty($query)
                && stripos($item['title'], $query) === false
                && stripos($item['subtitle'], $query) === false) {
                continue;
            }
            $item['arg'] = $item['title'];
            $result[] = $item;
        }

        return $this->renderList($result);
    }
}

(new Menu())->run($argv);
