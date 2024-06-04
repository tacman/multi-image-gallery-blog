#!/usr/bin/env bash

bin/console survos:user:create  tacman@gmail.com tt --roles ROLE_USER
bin/console survos:user:create tt@survos.com tt --roles ROLE_ADMIN --roles ROLE_USER

