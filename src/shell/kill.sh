#!/bin/bash

kill -9 $1

cat<<EOF
{
    "alfredworkflow": {
        "arg": "$1",
        "config": {},
        "variables": {
            "title": "强杀进程",
            "content": "$1"
        }
    }
}
EOF