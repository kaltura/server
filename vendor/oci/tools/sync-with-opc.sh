#!/bin/bash

rsync -e "ssh -i $SSH_PRIVATE_KEY_FILE" -v -r composer.* phpunit.xml tools src tests opc@$INSTANCE_IP:oci-php-sdk
