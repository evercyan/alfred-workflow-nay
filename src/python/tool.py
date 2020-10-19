#!/usr/local/bin/python3
# -*- coding:utf-8 -*-

import sys
import json
import hashlib
import time
import urllib.parse
from base import Base


class Tool:

    TITLE = {
        'jsons': 'JSON 收缩',
        'jsone': 'JSON 扩张',
        'unie': 'Unicode 编码',
        'unid': 'Unicode 解码',
        'urle': 'URL 编码',
        'urld': 'URL 解码',
        'half': '全角转半角',
        'full': '半角转全角',
        'time': '当前时间戳',
        't2d': '时间戳转日期',
        'd2t': '日期转时间戳',
        'md5': '生成 md5',
    }

    ANGLE = {
        "。": ".",
        "？": ".",
        "！": "!",
        "，": ",",
        "、": ",",
        "；": ";",
        "：": ":",
        "“": "\"",
        "”": "\"",
        "‘": "'",
        "（": "(",
        "）": ")",
        "《": "<",
        "》": ">",
        "〈": "<",
        "〉": ">",
        "【": "[",
        "】": "]",
        "～": "~",
    }

    def __init__(self):
        self.base = Base()
        return

    def run(self, argv):
        if len(argv) < 2:
            return False
        method = argv[1]
        query = argv[2]
        if hasattr(self, method) is False:
            return False

        result = getattr(self, method)(query)
        if type(result).__name__ != 'list':
            result = [result]

        list = []
        for item in result:
            title = self.TITLE[method]
            list.append({
                'title': title,
                'subtitle': item,
                'arg': item,
                'valid': True,
                'variables': {
                    'title': title,
                    'content': item,
                },
            })

        return self.base.render_list({
            'items': list,
        })

    # url_encode
    def urle(self, url):
        return urllib.parse.quote(url)

    # url_decode
    def urld(self, url):
        return urllib.parse.unquote(url)

    # json_expand
    def jsone(self, content):
        return json.dumps(
            json.loads(content),
            indent=4,
            sort_keys=True,
            ensure_ascii=False
        )

    # json_shrink
    def jsons(self, content):
        return json.dumps(
            json.loads(content),
            ensure_ascii=False
        )

    # md5
    def md5(self, text):
        return hashlib.md5(text.encode('utf8')).hexdigest()

    # date2time
    def d2t(self, text):
        return str(int(time.mktime(
            time.strptime(text, '%Y-%m-%d %H:%M:%S')
        )))

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
        for key in self.ANGLE:
            text = text.replace(key, self.ANGLE[key])
        return text

    # full_angle
    def full(self, text):
        for key in self.ANGLE:
            text = text.replace(self.ANGLE[key], key)
        return text


if __name__ == '__main__':
    sys.exit(Tool().run(sys.argv))
