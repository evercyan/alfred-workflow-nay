<?php
/**
 * 斗图
 * https://www.doutula.com
 */

class Dt
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
    public function search($keyword)
    {
        $content = $this->get(sprintf(self::API_URL, $keyword));
        preg_match_all(self::REGEX_IMAGE, $content, $image_list);
        preg_match_all(self::REGEX_NAME, $content, $name_list);
        if (empty($image_list) || empty($name_list)) {
            return false;
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
            return false;
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

    private function get($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        return curl_exec($ch);
    }
}
