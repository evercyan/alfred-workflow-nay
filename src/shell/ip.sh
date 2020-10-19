#!/bin/bash

local_ip=$(ifconfig | grep -A 1 "en" | grep broadcast | cut -d " " -f 2 | tr "\\n" " ")
external_ip=$(curl --silent http://icanhazip.com)
cat<<EOF
{
    "items": [
        {
            "title": "内网 IP",
            "subtitle": "${local_ip}",
            "arg": "${local_ip}",
            "variables": {
                "title": "内网 IP",
                "content": "${local_ip}"
            }
        },
        {
            "title": "外网 IP",
            "subtitle": "${external_ip}",
            "arg": "${external_ip}",
            "variables": {
                "title": "外网 IP",
                "content": "${external_ip}"
            }
        }
    ]
}
EOF