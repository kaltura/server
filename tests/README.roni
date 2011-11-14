How to create a plugin test:
1.       Get PHPUnit 3.5.7 – use this to install: http://www.phpunit.de/manual/current/en/ but give him the version when you install phpunit.
2.       Find a plugin with CRUD actions – not annotation or cuePoint which we have already.
3.       Enable the plugin in the kConfLocal file.
4.       Go in the plugin folder to generator.ini – and remove the exclude from the unitTest section – if generator.ini file doesn’t exists come and ask me (use ';' in line beginning to comment out)
5.       cd /opt/kaltura/app/generator, run ./generate.sh unitTests
6.       Now that you got a plugin test generated, go to: app/tests/api/KalturaPlugins/PluginServiceName
7.       Now edit the .data to have your wanted data – replace abstract classes with real one, add values to must fields, put global data tokens where it fits.
8.       Add the plugin permission in the deployment script
9.       If you need any new data for the test (entryId, userId, …) than add it in the deployment script as well.
10.   Use phpunit to run the test see if you get failures or errors.
11.   Go again to the failure report / new data file and see that you approve all the failures.
12.   Replace global results with token (like partnerId)
13.   Until you get no failures go to 10
14.   At the end check the data file and commit only the data, config, .ini files. The tests are not checked in.
//Test QC Commit!