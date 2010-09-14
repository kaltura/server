#!/bin/bash

find /web/content/uploads -ctime +7 -delete
find /web/content/webcam -ctime +7 -delete
find /web/content/preconvert -ctime +7 -delete
find /web/content/imports -ctime +7 -delete
find /web/content/new_preconvert/ -ctime +7 -delete

find /web/conversions/preconvert -ctime +7 -delete

find /web/conversions/handled -ctime +7 -delete
find /web/conversions/postconvert_res -ctime +7 -delete
find /web/conversions/download_res -ctime +7 -delete
find /web/conversions/preconvert_cmd -ctime +7 -delete
find /web/conversions/preconvert_commercial_cmd -ctime +7 -delete
