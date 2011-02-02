cd PhpDocumentor
#phpdoc -o HTML:Smarty:default -t core -ti "Kaltura Server" -dn General -dc Global -ct readonly,insertonly,writeonly,service,action,clientgenerator,filter,serverOnly,dynamicType,requiresPermission -d ../alpha,../api_v3,../infra,../plugins
phpdoc -o HTML:frames:phphtmllib -t ..\batch -ti "Kaltura Server" -dn General -dc Global -ct readonly,insertonly,writeonly,service,action,clientgenerator,filter,serverOnly,dynamicType,requiresPermission -d ../../batch,../../infra,../../plugins -po General,api,Core,plugins*
#phpdoc -o HTML:Smarty:default -t admin -ti "Kaltura Server" -dn General -dc Global -ct readonly,insertonly,writeonly,service,action,clientgenerator,filter,serverOnly,dynamicType,requiresPermission -d ../,../admin_console,../infra,../plugins
#phpdoc -o HTML:Smarty:default -t docs -ti "Kaltura Server" -dn General -dc Global -ct readonly,insertonly,writeonly,service,action,clientgenerator,filter,serverOnly,dynamicType,requiresPermission -d ../alpha,../api_v3,../batch,../,../admin_console,../generator,../deployment,../infra,../plugins,../scripts,../tests

cd ..