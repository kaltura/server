#!/bin/bash


KP=$(pgrep watch.daemon.sh)
if [[ "X$KP" != "X" ]]
      then
		kill -9 $KP
fi
    
