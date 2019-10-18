<?php
require_once __DIR__ . '/dt.php';
class Nay
{
    public function __construct()
    {
    }

    /**
     * 入口逻辑
     */
    public function run($params)
    {
        $func = $params[1] ?? '';
        $query = $params[2] ?? '';
        if (empty($func)
            || !method_exists($this, $func)) {
            return false;
        }
        $result = $this->$func($query);
        if (empty($result)) {
            return false;
        }

        exit($this->toxml($result));
        // exit(json_encode($result));
    }

    public function toxml($result)
    {
        $xml = '<?xml version="1.0"?>';
        $xml .= '<items>';
        foreach ($result as $item) {
            $xml .= '<item';
            if (!empty($item['arg'])) {
                $xml .= sprintf(' arg="%s"', $item['arg']);
            }
            $xml .= '>';
            if (!empty($item['title'])) {
                $xml .= sprintf('<title>%s</title>', $item['title']);
            }
            if (!empty($item['subtitle'])) {
                $xml .= sprintf('<subtitle>%s</subtitle>', $item['subtitle']);
            }
            if (!empty($item['icon'])) {
                $xml .= sprintf('<icon>%s</icon>', $item['icon']);
            }
            $xml .= '</item>';
        }
        $xml .= '</items>';
        return $xml;
    }

    const MENU = [
        [
            'title' => '显示 IP',
            'subtitle' => 'ip',
        ],
        [
            'title' => '识别截屏文字',
            'subtitle' => 'ocr',
        ],
        [
            'title' => '斗图一下',
            'subtitle' => 'dt',
        ],
        [
            'title' => '生成二维码',
            'subtitle' => 'qr',
        ],
        [
            'title' => 'URL 编码',
            'subtitle' => 'urle',
        ],
        [
            'title' => 'URL 解码',
            'subtitle' => 'urld',
        ],
        [
            'title' => 'Json 格式化',
            'subtitle' => 'jsone',
        ],
        [
            'title' => 'Json 转换成单行',
            'subtitle' => 'jsons',
        ],
        [
            'title' => '生成 md5',
            'subtitle' => 'md5',
        ],
        [
            'title' => '日期转换为时间戳',
            'subtitle' => 'd2t',
        ],
        [
            'title' => '时间戳转换为日期',
            'subtitle' => 't2d',
        ],
        [
            'title' => '当前时间戳',
            'subtitle' => 'time',
        ],
        [
            'title' => 'Unicode 编码',
            'subtitle' => 'unie',
        ],
        [
            'title' => 'Unicode 解码',
            'subtitle' => 'unid',
        ],
        [
            'title' => '全角转半角',
            'subtitle' => 'half',
        ],
        [
            'title' => '半角转全角',
            'subtitle' => 'full',
        ],
    ];
    /**
     * 帮助菜单
     */
    private function menu($query = '')
    {
        $result = [];
        foreach (self::MENU as $item) {
            if (!empty($query)
                && stripos($item['title'], $query) === false
                && stripos($item['subtitle'], $query) === false) {
                continue;
            }
            $item['arg'] = $item['subtitle'];
            $result[] = $item;
        }
        return $result;
    }

    /**
     * 斗图
     *
     * 以空格结尾才会进行搜索, 否则触发请求太多
     */
    private function dt($query = '')
    {
        if (!preg_match('/ $/', $query)) {
            return false;
        }
        $query = rtrim($query, '.');
        return (new Dt())->search($query);
    }
}

(new Nay())->run($argv);
