#!/usr/local/bin/python3
# -*- coding:utf-8 -*-
import sys
import qrcode
import os


class Qr:
    IMG_PATH = '/tmp/qr.png'

    def __init__(self):
        return

    def generate(self, text):
        qr = qrcode.QRCode(version=2, error_correction=qrcode.constants.ERROR_CORRECT_L, box_size=12, border=1)
        qr.add_data(text)
        qr.make(fit=True)
        img = qr.make_image()
        img.save(self.IMG_PATH)
        return self.IMG_PATH


if __name__ == '__main__':
    qr = Qr()
    sys.exit(qr.generate('http://baidu.com'))
