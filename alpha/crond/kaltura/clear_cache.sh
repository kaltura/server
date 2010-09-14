#!/bin/bash

nice -n 19 find /tmp -cmin +30 -name "cache*" -delete
