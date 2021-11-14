#!/bin/bash

rsync --update -e "ssh -i $SSH_PRIVATE_KEY_FILE" -v --exclude 'composer.json' --exclude 'composer.lock' opc@$INSTANCE_IP:oci-php-sdk/* .
