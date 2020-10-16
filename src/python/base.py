#!/usr/local/bin/python3
# -*- coding:utf-8 -*-
import sys
import json


class Base:

    # 输出列表
    def render_list(self, datas):
        json.dump(datas, sys.stdout)
        sys.stdout.flush()
        return

    # 输出详情
    def render_info(self, title, content):
        resp = {
            'alfredworkflow': {
                'arg': 'woooke',
                'config': {},
                'variables': {
                    'title': title,
                    'content': content,
                },
            }
        }
        json.dump(resp, sys.stdout)
        sys.stdout.flush()
        return
