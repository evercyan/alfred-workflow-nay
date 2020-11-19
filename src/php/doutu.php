<?php
/**
 * 搜索斗图
 */

require_once __DIR__ . '/base.php';

class Doutu extends Base
{
    const TITLE = '斗图';

    // 列表图片数量
    const IMAGE_NUMBER = 9;
    // 临时图片存储
    const STORE_PATH = '/tmp/.nay';

    public function __construct()
    {
        if (!file_exists(self::STORE_PATH)) {
            @mkdir(self::STORE_PATH);
        }
    }

    // https://www.doutula.com
    private function doutula(string $keyword)
    {
        $content = $this->get(sprintf('https://doutula.com/search?keyword=%s', $keyword));
        preg_match_all('/data-original\=\"([\s\S]*?)\"/', $content, $image_list);
        return $image_list[1] ?? [];
    }

    // https://www.52doutu.cn
    private function doutu(string $keyword)
    {
        $content = $this->get(sprintf('https://www.52doutu.cn/search/%s', $keyword));
        preg_match_all('/data-original\=\"([\s\S]*?)\"/', $content, $image_list);
        return $image_list[1] ?? [];
    }

    public function api(string $keyword)
    {
        $image_list = $this->doutu($keyword);
        if (empty($image_list)) {
            return $this->renderError('无数据' . count($image_list));
        }

        // 限制显示图片数量
        $image_number = $_ENV['app_doutu_image_number'] ?? self::IMAGE_NUMBER;

        $list = [];
        foreach ($image_list as $image_url) {
            if (!preg_match('/^http/is', $image_url)) {
                continue;
            }
            if (count($list) >= $image_number) {
                break;
            }
            $image_path = $this->saveImage($image_url);
            $list[] = [
                'title' => $keyword,
                'image' => $image_url,
                'image_path' => $image_path,
            ];
        }

        if (empty($list)) {
            return $this->renderError('无数据');
        }

        $result = [];
        foreach ($list as $item) {
            $result[] = [
                'title' => $item['title'],
                'subtitle' => '',
                'arg' => $item['image'],
                'icon' => [
                    'path' => $item['image_path'],
                ],
                'variables' => [
                    'title' => sprintf('%s-%s', self::TITLE, $item['title']),
                    'content' => $item['image'],
                ],
            ];
        }

        return $this->renderList($result);
    }

    public function saveImage($image)
    {
        $file_path = sprintf('%s/%s.png', self::STORE_PATH, md5($image));
        if (!file_exists($file_path)) {
            file_put_contents($file_path, $this->get($image));
        }
        return $file_path;
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

(new Doutu())->run($argv);
