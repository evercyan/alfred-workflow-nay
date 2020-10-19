<?php
class Base
{
    public function renderList($result = '')
    {
        if (empty($result)) {
            return;
        }
        exit(json_encode([
            'items' => $result,
        ]));
    }

    public function renderInfo(string $title = '', string $content = '')
    {
        exit(json_encode([
            'alfredworkflow' => [
                'arg' => $title,
                'config' => (object) [],
                'variables' => [
                    'title' => $title,
                    'content' => $content,
                ],
            ],
        ]));
    }

    public function renderError(string $title = '', string $content = '')
    {
        $this->renderList([
            [
                'title' => $title,
                'subtitle' => $content,
                'arg' => $content,
                'variables' => [
                    'title' => $title,
                    'content' => $content,
                ],
            ],
        ]);
    }

    public function get(string $url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($ch);
    }

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
