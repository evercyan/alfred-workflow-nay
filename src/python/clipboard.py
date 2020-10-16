#!/usr/local/bin/python3
# -*- coding:utf-8 -*-

import io
import sys
import urllib.request
from PIL import Image
from AppKit import NSPasteboard, NSArray, NSData, NSImage
from base import Base


class Clipboard:

    # 写图片到剪贴板
    def image_to_clipboard(self, url):
        pb = NSPasteboard.generalPasteboard()
        pb.clearContents()
        if url.startswith('http'):
            img = Image.open(urllib.request.urlopen(url))
        else:
            img = Image.open(url)

        img_bytes = io.BytesIO()
        img.save(img_bytes, format='PNG')
        imgNsData = NSData.alloc().initWithBytes_length_(
            img_bytes.getvalue(), img_bytes.tell())
        imgNsImage = NSImage.alloc().initWithData_(imgNsData)
        array = NSArray.arrayWithObject_(imgNsImage)
        pb.writeObjects_(array)
        return

    # 写字符到剪贴板
    def text_to_clipboard(self, text):
        pb = NSPasteboard.generalPasteboard()
        pb.clearContents()
        a = NSArray.arrayWithObject_(text)
        pb.writeObjects_(a)
        return

    def run(self, argv):
        if len(argv) < 2:
            return False

        method = argv[1]
        query = argv[2]
        if hasattr(self, method) is False:
            return False
        getattr(self, method)(query)
        Base().render_info("复制到剪贴板", query)
        return


if __name__ == '__main__':
    sys.exit((Clipboard()).run(sys.argv))
