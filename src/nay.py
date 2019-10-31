#!/usr/bin/python
# encoding: utf-8

import sys
import json
import hashlib
import time
import urllib.parse
from workflows import Workflows
from i2c import I2C
from qr import Qr


class Nay:

    MENU = {
        'jsons': 'Json 转换成单行',
        'unie': 'Unicode 编码',
        'md5': '生成 md5',
        'full': '半角转全角',
        'unid': 'Unicode 解码',
        't2d': '时间戳转换为日期',
        'urle': 'URL 编码',
        'urld': 'URL 解码',
        'half': '全角转半角',
        'jsone': 'Json 格式化',
        'd2t': '日期转换为时间戳',
        'time': '当前时间戳',
        'si2c': '图片复制到剪切板',
        'qr': '生成二维码',
    }
    INFO_FUNCS = [
        'si2c',
        'qr',
    ]

    def __init__(self):
        self.i2c = I2C()
        self.wk = Workflows()
        pass

    def run(self, argv):
        if len(argv) < 2:
            return False

        func = argv[1]
        param = argv[2]
        if hasattr(self, func) is False:
            return False

        resps = getattr(self, func)(param)
        if func in self.INFO_FUNCS:
            return self.wk.render_info(self.MENU[func], resps)
        if type(resps).__name__ != 'list':
            resps = [resps]

        datas = {
            'items': [],
        }
        for resp in resps:
            datas['items'].append({
                'title': self.MENU[func],
                'subtitle': resp,
                'arg': resp,
                'valid': True,
                'variables': {
                    'title': self.MENU[func],
                    'content': resp,
                },
            })
        return self.wk.render_list(datas)

    # qrcode
    def qr(self, query):
        qr = Qr()
        img_path = qr.generate(query)
        self.i2c.save_image_to_clipboard(img_path)
        return img_path

    # save image 2 clipboard
    def si2c(self, url):
        self.i2c.save_image_to_clipboard(url)
        return url

    # url_encode
    def urle(self, url):
        return urllib.parse.quote(url)

    # url_decode
    def urld(self, url):
        return urllib.parse.unquote(url)

    # json_expand
    def jsone(self, content):
        return json.dumps(json.loads(content), indent=4, sort_keys=False, ensure_ascii=False)

    # json_shrink
    def jsons(self, content):
        return json.dumps(json.loads(content), ensure_ascii=False)

    # md5
    def md5(self, text):
        return hashlib.md5(text.encode('utf8')).hexdigest()

    # date2time
    def d2t(self, text):
        return str(int(time.mktime(time.strptime(text, '%Y-%m-%d %H:%M:%S'))))

    # time2date
    def t2d(self, text):
        return time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(int(text)))

    # timestamp
    def time(self, text):
        return str(int(time.time()))

    # unicode_encode
    def unie(self, text):
        return text.encode('unicode_escape').decode("utf-8")

    # unicode_decode
    def unid(self, text):
        return text.encode('utf8').decode('unicode_escape')

    # half_angle
    def half(self, text):
        return text.replace("。", ".").replace("？", "?").replace("！", "!").replace("，", ",").replace("、", ",").replace("；", ";").replace("：", ":").replace(
            "“", "\"").replace("”", "\"").replace("‘", "'").replace("（", "(").replace("）", ")").replace("《", "<").replace("》", ">").replace("〈", "<").replace(
                "〉", ">").replace("【", "[").replace("】", "]").replace("～", "~")

    # full_angle
    def full(self, text):
        return text.replace(".", "。").replace("?", "？").replace("!", "！").replace(",", "，").replace(",", "、").replace(";", "；").replace(":", "：").replace(
            "\"", "“").replace("\"", "”").replace("'", "‘").replace("(", "（").replace(")", "）").replace("<", "《").replace(">", "》").replace("<", "〈").replace(
                ">", "〉").replace("[", "【").replace("]", "】").replace("~", "～")


if __name__ == '__main__':
    nay = Nay()
    sys.exit(nay.run(sys.argv))
