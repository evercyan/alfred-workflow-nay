<?php
class Base
{
    public function render($result)
    {
        if (empty($result)) {
            return;
        }
        exit(json_encode([
            'items' => $result,
        ]));
    }

    public function toXml(array $array)
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
