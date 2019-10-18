# -*- coding:utf-8 -*-
import os
import io
import sys
import json
from PIL import Image, ImageGrab
import urllib.request
from AppKit import NSPasteboard, NSArray, NSData, NSImage


class Workflows:
    def render_list(self, datas):
        json.dump(datas, sys.stdout)
        sys.stdout.flush()
        return

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

    # 写图片到剪贴板
    def save_image_to_clipboard(self, url):
        pb = NSPasteboard.generalPasteboard()
        pb.clearContents()

        if url.startswith('http'):
            img = Image.open(urllib.request.urlopen(url))
        else:
            img = Image.open(url)

        img_bytes = io.BytesIO()
        img.save(img_bytes, format='PNG')
        imgNsData = NSData.alloc().initWithBytes_length_(img_bytes.getvalue(), img_bytes.tell())
        imgNsImage = NSImage.alloc().initWithData_(imgNsData)
        array = NSArray.arrayWithObject_(imgNsImage)
        pb.writeObjects_(array)
        return

    # 写字符到剪贴板
    def save_text_to_clipboard(self, text):
        pb = NSPasteboard.generalPasteboard()
        pb.clearContents()
        a = NSArray.arrayWithObject_(text)
        pb.writeObjects_(a)
        return
