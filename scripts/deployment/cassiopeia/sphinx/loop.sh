#!/bin/bash
MAINT=stoploop

while [[ ! -f $MAINT ]]
      do
	./loop_runone.sh 
      done

