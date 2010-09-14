#!/bin/bash
if [ -z "$2" ]          # Must specify a filename to convert.
then
  echo "usage: $0 <start_revision> <update_version>"
  exit $E_NOARGS
fi

start_revision=$1
update_version=$2

output_file_name="server_update_${update_version}.txt"
echo "Creating file: ${output_file_name}"
temp_output_file_name="${output_file_name}.raw"
`svn log -r $start_revision:HEAD -v > $temp_output_file_name`
grep "\*" $temp_output_file_name > $output_file_name
echo "
----------------------------------- raw svn log -----------------------------------
" >> $output_file_name
cat $temp_output_file_name >> $output_file_name