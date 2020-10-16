#!/usr/local/bin/python3
# -*- coding:utf-8 -*-
import sys
import qrcode
from base import Base
from clipboard import Clipboard


class Qr:

    IMG_PATH = '/tmp/qr.png'

    def generate(self, text):
        qr = qrcode.QRCode(
            version=2,
            error_correction=qrcode.constants.ERROR_CORRECT_L,
            box_size=12,
            border=1
        )
        qr.add_data(text)
        qr.make(fit=True)
        img = qr.make_image()
        img.save(self.IMG_PATH)
        return

    def run(self, argv):
        if len(argv) < 1:
            return False
        text = argv[1]
        self.generate(text)
        Clipboard().image(self.IMG_PATH)
        Base().render_info("已复制到剪贴板", "二维码文本: " + text)
        return


if __name__ == '__main__':
    sys.exit(Qr().run(sys.argv))
