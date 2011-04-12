#!/bin/bash

nice -n 19 find /tmp -cmin +15 -name "cache*" -delete
