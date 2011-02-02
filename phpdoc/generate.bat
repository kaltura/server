cd PhpDocumentor
#phpdoc -o HTML:frames/Extjs:default -t ..\core -ti "Kaltura Server" -dn General -dc Global -ct readonly,insertonly,writeonly,service,action,clientgenerator,filter,serverOnly,dynamicType,requiresPermission -d ../../alpha,../../api_v3,../../infra,../../plugins
phpdoc -o HTML:frames/Extjs:default -t ..\batch -ti "Kaltura Server" -dn General -dc Global -ct readonly,insertonly,writeonly,service,action,clientgenerator,filter,serverOnly,dynamicType,requiresPermission -d ../../batch,../../infra,../../plugins
#phpdoc -o HTML:Smarty:default -t ..\admin -ti "Kaltura Server" -dn General -dc Global -ct readonly,insertonly,writeonly,service,action,clientgenerator,filter,serverOnly,dynamicType,requiresPermission -d ../../,../../admin_console,../../infra,../../plugins
#phpdoc -o HTML:Smarty:default -t ..\docs -ti "Kaltura Server" -dn General -dc Global -ct readonly,insertonly,writeonly,service,action,clientgenerator,filter,serverOnly,dynamicType,requiresPermission -d ../../alpha,../../api_v3,../../batch,../../,../../admin_console,../../generator,../../deployment,../../infra,../../plugins,../../scripts,../../tests

#HTML:frames:phphtmllib
#HTML:frames:earthli
#HTML:frames:default
#HTML:frames:l0l33t
#HTML:frames:phpdoc.de
#HTML:frames:phphtmllib
#HTML:frames:DOM/default
#HTML:frames:DOM/l0l33t
#HTML:frames:DOM/phpdoc.de
#HTML:frames:DOM/earthli
#HTML:frames:DOM/phphtmllib
#HTML:frames:phpedit
#HTML:Smarty:default
#HTML:Smarty:PHP
#HTML:Smarty:HandS

cd ..