<?php
/**
 * 输出 Nay 菜单
 */

require_once __DIR__ . '/base.php';

class Menu extends Base
{
    const MENU_LIST = [
        [
            'subtitle' => '显示 IP',
            'title' => 'ip',
        ],
        [
            'subtitle' => '识别截屏文字',
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
            'subtitle' => 'URL 编码',
            'title' => 'urle',
        ],
        [
            'subtitle' => 'URL 解码',
            'title' => 'urld',
        ],
        [
            'subtitle' => 'Json 格式化',
            'title' => 'jsone',
        ],
        [
            'subtitle' => 'Json 转换成单行',
            'title' => 'jsons',
        ],
        [
            'subtitle' => '生成 md5',
            'title' => 'md5',
        ],
        [
            'subtitle' => '日期转换为时间戳',
            'title' => 'd2t',
        ],
        [
            'subtitle' => '时间戳转换为日期',
            'title' => 't2d',
        ],
        [
            'subtitle' => '当前时间戳',
            'title' => 'time',
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
            $item['arg'] = $item['subtitle'];
            $result[] = $item;
        }

        return $this->render($result);
    }
}

(new Menu())->run($argv);
