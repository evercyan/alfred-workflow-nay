<?php
/**
 * PHP
 */

class Base
{
    /**
     * 数组转换成 xml
     *
     * @param array $array
     * @return string
     */
    public function toxml(array $array)
    {
        $xml = '<?xml version="1.0"?>';
        $xml .= '<items>';
        foreach ($array as $item) {
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
            if (!empty($item['variables'])) {
                $xml .= sprintf(
                    '<variables><title>%s</title><content>%s</content></variables>',
                    $item['variables']['title'],
                    $item['variables']['content']
                );
            }
            $xml .= '</item>';
        }
        $xml .= '</items>';
        return $xml;
    }

    /**
     * get 请求
     *
     * @param string $url
     * @return string
     */
    public function get(string $url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        return curl_exec($ch);
    }

    /**
     * post 请求
     *
     * @param string $url
     * @param array $param
     * @param array $header
     * @return array
     */
    public function post(string $url, array $param, array $header = [])
    {
        $process = curl_init($url);
        if (stripos($url, 'https://') !== false) {
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_SSL_VERIFYHOST, false);
        }
        $data = json_encode($param);
        $headers = array_merge([
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data),
        ], $header);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, $data);
        curl_setopt($process, CURLOPT_USERAGENT, 'Mozilla/4.0');
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($process);
        curl_close($process);
        if (is_string($result)) {
            $result = json_decode($result, true);
        }
        return $result;
    }
}

class Api extends Base
{
    const API_ABBR = 'https://lab.magiconch.com/api/nbnhhsh/guess';
    const API_TRANSLATE = 'http://api.fanyi.baidu.com/api/trans/vip/translate';

    public function __construct()
    {
    }

    /**
     * 好好说话
     *
     * @param string $query
     * @return array
     */
    public function abbr(string $query)
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

    /**
     * 百度翻译
     *
     * @param string $query
     * @return array
     */
    public function translate(string $query)
    {
        $flag = preg_match('/^[a-zA-Z]+$/isU', $query);
        $param = [
            'q' => $query,
            'from' => $flag ? 'en' : 'zh',
            'to' => $flag ? 'zh' : 'en',
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
}

class Dt extends Base
{
    const API_URL = 'https://www.doutula.com/search?keyword=%s';
    const REGEX_IMAGE = '/data-original\=\"([\s\S]*?)\"/';
    const REGEX_NAME = '/<p style=\"([\s\S]*?)<\/p>/';

    // 保留纪录阈值
    const IMAGE_COUNT_MAX = 9;

    // 临时图片存储路径
    const STORE_PATH = '/tmp/.nay';

    public function __construct()
    {
        if (!file_exists(self::STORE_PATH)) {
            @mkdir(self::STORE_PATH);
        }
    }

    /**
     * 搜索斗图
     *
     * @param string $keyword
     * @return array
     */
    public function search(string $keyword)
    {
        $content = $this->get(sprintf(self::API_URL, $keyword));
        preg_match_all(self::REGEX_IMAGE, $content, $image_list);
        preg_match_all(self::REGEX_NAME, $content, $name_list);
        if (empty($image_list) || empty($name_list)) {
            return [];
        }
        $datas = [];
        $image_paths = [];
        for ($i = 0; $i < count($image_list[1]); $i++) {
            if (empty($name_list[1][$i])) {
                continue;
            }
            if (count($datas) >= self::IMAGE_COUNT_MAX) {
                break;
            }
            $title = substr($name_list[1][$i], strpos($name_list[1][$i], '>') + 1);
            $image = $image_list[1][$i];
            // 处理图片下载
            $image_path = $this->getImagePath($image);
            if (in_array($image_path, $image_paths)) {
                continue;
            }
            $image_paths[] = $image_path;
            $datas[] = [
                'title' => $title,
                'image' => $image,
                'image_path' => $image_path,
            ];
        }
        if (empty($datas)) {
            return [];
        }

        $result = [];
        foreach ($datas as $item) {
            $result[] = [
                'title' => $item['title'],
                'subtitle' => '',
                'arg' => $item['image'],
                'icon' => $item['image_path'],
                'variables' => [
                    'title' => $item['title'],
                    'content' => $item['image'],
                ],
            ];
        }
        return $result;
    }

    public function getImagePath($image)
    {
        $file_path = sprintf('%s/%s.png', self::STORE_PATH, md5($image));
        if (!file_exists($file_path)) {
            file_put_contents($file_path, $this->get($image));
        }
        return $file_path;
    }
}

class Nay extends Base
{

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
        [
            'title' => '好好说话',
            'subtitle' => 'abbr',
        ],
        [
            'title' => '百度翻译',
            'subtitle' => 'f',
        ],
    ];

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
        if (empty($func) || !method_exists($this, $func)) {
            return false;
        }

        $result = $this->$func($query);
        if (empty($result)) {
            return false;
        }

        exit(json_encode([
            'items' => $result,
        ]));
        // exit($this->toxml($result));
    }

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
     * 以空格结尾才会进行搜索, 避免触发请求太多
     */
    private function dt($query = '')
    {
        if (!preg_match('/ $/', $query)) {
            return false;
        }
        $query = rtrim($query);
        return (new Dt())->search($query);
    }

    /**
     * 好好说话
     *
     * input: sb
     * output: ["那没事了", "xxxx"]
     * 以空格结尾才会进行搜索, 避免触发请求太多
     */
    private function abbr($query = '')
    {
        if (!preg_match('/ $/', $query)) {
            return false;
        }
        return (new Api())->abbr(trim($query));
    }

    /**
     * 百度翻译
     */
    private function f($query = '')
    {
        if (!preg_match('/ $/', $query)) {
            return false;
        }
        return (new Api())->translate(trim($query));
    }
}

(new Nay())->run($argv);
