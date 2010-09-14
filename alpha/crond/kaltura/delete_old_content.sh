#!/bin/bash

find @WEB_DIR@/content/uploads -ctime +7 -delete
find @WEB_DIR@/content@WEB_DIR@cam -ctime +7 -delete
find @WEB_DIR@/content/preconvert -ctime +7 -delete
find @WEB_DIR@/content/imports -ctime +7 -delete
find @WEB_DIR@/content/new_preconvert/ -ctime +7 -delete

find @WEB_DIR@/conversions/preconvert -ctime +7 -delete

find @WEB_DIR@/conversions/handled -ctime +7 -delete
find @WEB_DIR@/conversions/postconvert_res -ctime +7 -delete
find @WEB_DIR@/conversions/download_res -ctime +7 -delete
find @WEB_DIR@/conversions/preconvert_cmd -ctime +7 -delete
find @WEB_DIR@/conversions/preconvert_commercial_cmd -ctime +7 -delete
