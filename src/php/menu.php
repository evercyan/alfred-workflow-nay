<?php
/**
 * 输出 nay 菜单
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
            'title' => 'time',
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
            'subtitle' => '文字识别',
            'title' => 'ocr',
        ],
        [
            'subtitle' => '斗图一下',
            'title' => 'dt',
        ],
        [
            'subtitle' => '生成二维码',
            'title' => 'qr',
        ],
        [
            'subtitle' => '好好说话',
            'title' => 'abbr',
        ],
        [
            'subtitle' => '百度翻译',
            'title' => 'f',
        ],
    ];

    public function run(array $argv)
    {
        $query = $argv[1] ?? '';

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

        return $this->render($result);
    }
}

(new Menu())->run($argv);
