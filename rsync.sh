#!/bin/bash
root=$(cd "$(dirname "$0")"; pwd)
rsync -avz -e ssh $root l@wanshi.org:/data/phpweb/ --exclude=*.log --exclude=$root/.git/*
