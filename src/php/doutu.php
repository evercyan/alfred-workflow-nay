<?php
/**
 * 搜索斗图
 */

require_once __DIR__ . '/base.php';

class Doutu extends Base
{
    const TITLE = '斗图';
    const API = 'https://www.doutula.com/search?keyword=%s';

    const REGEX_IMAGE = '/data-original\=\"([\s\S]*?)\"/';
    const REGEX_NAME = '/<p style=\"([\s\S]*?)<\/p>/';

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

    public function api(string $keyword)
    {
        $content = $this->get(sprintf(self::API, $keyword));
        preg_match_all(self::REGEX_IMAGE, $content, $image_list);
        preg_match_all(self::REGEX_NAME, $content, $name_list);
        if (empty($image_list)
            || empty($name_list)) {
            return $this->renderError('无数据');
        }

        $image_number = $_ENV['app_doutu_image_number'] ?? self::IMAGE_NUMBER;

        $datas = [];
        $image_paths = [];
        for ($i = 0; $i < count($image_list[1]); $i++) {
            if (empty($name_list[1][$i])) {
                continue;
            }
            if (count($datas) >= $image_number) {
                break;
            }
            $title = substr($name_list[1][$i], strpos($name_list[1][$i], '>') + 1);
            $image = $image_list[1][$i];
            $image_path = $this->saveImage($image);
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
            return $this->renderError('无数据');
        }

        $result = [];
        foreach ($datas as $item) {
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
