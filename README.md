<div align="center">

![alfred-workflow-nay](https://raw.githubusercontent.com/evercyan/cantor/master/resource/bb/bbfafaccf99d3c0baf17608c11b4b925.png)

alfred workflow utils, continue to upgrade.

[点我下载 nay.alfredworkflow](https://github.com/evercyan/alfred-workflow-nay/releases/download/v0.0.1/nay.alfredworkflow)

</div>

---

## 必读 QA

```
Q: 应用执行环境
A: 应用使用脚本语言有 python, php, shell 和 ruby 等, 均需要对应环境
    php: /usr/bin/php
    ruby: /usr/bin/ruby
    shell: /bin/bash
    python: /usr/local/bin/python3
```

```
若需要使用特殊功能, 需要安装相关依赖
1. brew install py3cairo pygobject3
2. 在 alfred workflow 列表中, 选中 nay 右键: Open in Terminal 进入应用目录
3. pip3 install -r requirements.txt
```

```
Q: 需要自行申请应用密钥的功能?
A: 百度翻译, 历史上的今天, ocr
```

```
Q: 安装后可直接使用的功能?
A: 除以下功能外:
    ocr: 文字识别
    img: 复制图片到剪贴板
    qr: 生成二维码
    dt: 斗图
    f: 百度翻译
    history: 历史上的今天
```

免责一下, 本应用仅 Mac 10.14+ 亲测, 快照镇楼~

![nay](https://raw.githubusercontent.com/evercyan/cantor/master/resource/cb/cb0096f41a97690ba792389eb0ca5be3.png)

---

## 功能列表

### 功能菜单
![nay-menu](https://raw.githubusercontent.com/evercyan/cantor/master/resource/f9/f9a090ee01542e0ce27e8bfa472a1551.png)

---

### 基础功能

一些常用的文本处理功能, 安装即可使用

#### urle: URL 编码
`http://baidu` => `http%3A//baidu`

#### urld: URL 解码
`http%3A//baidu` => `http://baidu`

#### jsons: JSON 收缩
```json
{
    "hello": "world"
}
```

=>

```json
{"hello": "world"}
```

#### jsone: JSON 扩张
```json
{"hello": "world"}
```

=>

```json
{
    "hello": "world"
}
```

#### unie: Unicode 编码
`你好` => `\u4f60\u597d`

#### unid: Unicode 解码
`\u4f60\u597d` => `你好`

#### half: 全角转半角
`。，【】？` => `.,[]?`

#### full: 半角转全角
`.,[]?` => `。，【】？`

#### t2d: 时间戳转日期
`1603097785` => `2020-10-19 16:56:25`

#### d2t: 日期转时间戳
`2020-10-19 16:56:25` => `1603097785`

#### now: 当前时间戳
`1603097785`

#### md5: 生成 md5
`111111` => `96e79218965eb72c92a549dd5a330112`

### 特殊功能

如写入图片内容到 `剪贴板` 等, 需要安装 python 的依赖库

```sh
brew install py3cairo pygobject3

pip3 install qrcode
pip3 install Pillow
pip3 install PyObjC
```

#### ocr: 文字识别
 
- 截图或者复制图片内容到 `剪贴板`
- alfred 输入框输入 `ocr` 并回车(会请求百度 ocr 文字识别进行解析, 并将返回的文本直接写入`剪贴板`)
- 直接粘贴使用识别后的文字

![nay-ocr](https://raw.githubusercontent.com/evercyan/cantor/master/resource/cc/cc64524642e5124c53faed8b8de5e6e6.png)

=> 

```
H evercyan/ alfred-workflow-nay a
<>code① Issues 8 Pull requests⊙ Actions國 Projects
```
需自行去 [百度云控制台](https://console.bce.baidu.com/ai/#/ai/ocr/overview/index) 申请, 替换掉应用配置中的 `bd_ocr_key` 和 `bd_ocr_secret` 

#### img: 复制图片到剪贴板
alfred 输入框输入 `img` `远程图片地址` 或 `本地图片地址`, 应用会自动读取图片内容并写入 `剪贴板`

#### qr: 生成二维码
alfred 输入框输入 `qr` `文本`, 应用会自动生成二维码图片, 并将图片内容写入 `剪贴板`

### 系统相关

#### ip: 显示 IP
![nay-ip](https://raw.githubusercontent.com/evercyan/cantor/master/resource/78/7852df1b4063f7f7e11d1c6db899850f.png)

#### kill: 强杀进程
![nay-kill](https://raw.githubusercontent.com/evercyan/cantor/master/resource/59/595e6d9de74a71b9b8b62c7695df4a34.png)

### Api 功能

调用外部 api 实现的一些功能

#### dt: 斗图
![nay-dt](https://raw.githubusercontent.com/evercyan/cantor/master/resource/02/02b69666c1eda159a61085bb9d198d6f.png)

此功能需要 python 的依赖库

#### abbr: 字母简写
![nay-abbr](https://raw.githubusercontent.com/evercyan/cantor/master/resource/2b/2ba5d9c79adb71ff2b46f8b30f861c4f.png)

#### f: 百度翻译
![nay-f](https://raw.githubusercontent.com/evercyan/cantor/master/resource/b7/b762c8f01cc29ec53e9bcd7f2b4bc9d5.png)

需自行去 [百度翻译开放平台](https://api.fanyi.baidu.com/) 申请通用翻译 API, 替换掉应用配置中的 `bd_translate_appid` 和 `bd_translate_secret`

#### history: 历史上的今天
![nay-history](https://raw.githubusercontent.com/evercyan/cantor/master/resource/0b/0be2ef2c03be6c93ead70e61b40a2dc8.png)

需自行去 [阿凡达数据](https://www.avatardata.cn/Docs/Api/4b396fc5-22f5-4c21-86d1-b5f5777e6744) 申请应用 key, 替换掉应用配置中的 `api_history_key`

---

## 链接

- [alfred-clipboard-ocr](https://github.com/oott123/alfred-clipboard-ocr)
