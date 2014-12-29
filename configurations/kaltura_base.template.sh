#!/bin/sh - 

alias allkaltlog='grep --color "ERR:\|PHP\|trace\|CRIT\|\[error\]" @LOG_DIR@/*.log @LOG_DIR@/batch/*.log'
alias kaltlog='tail -f @LOG_DIR@/log/*.log @LOG_DIR@/batch/*.log | grep -A 1 -B 1 --color "ERR:\|PHP\|trace\|CRIT\|\[error\]"'


