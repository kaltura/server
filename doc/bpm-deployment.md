## Business Process Management Integration ##
Integration with Activiti BPM engine

#### Configuration ####

*plugins.ini*

Add the following line:

```
Integration		
ExampleIntegration
IntegrationEventNotifications
BpmEventNotificationIntegration
BusinessProcessNotification
ActivitiBusinessProcessNotification
```
*batch.ini*

Add the following lines under `[template]` section:
```
enabledWorkers.KAsyncIntegrate					= 1
enabledWorkers.KAsyncIntegrateCloser				= 1
```

Add the following lines as new sections:
```
[KAsyncIntegrate : JobHandlerWorker]
id													= 570
friendlyName										= Integrate
type												= KAsyncIntegrate
maximumExecutionTime								= 12000
scriptPath											= ../plugins/integration/batch/Integrate/KAsyncIntegrateExe.php

[KAsyncIntegrateCloser : JobHandlerWorker]
id													= 580
friendlyName										= Integrate Closer
type												= KAsyncIntegrateCloser
maximumExecutionTime								= 12000
scriptPath											= ../plugins/integration/batch/Integrate/KAsyncIntegrateCloserExe.php
params.maxTimeBeforeFail							= 1000000
```

#### Deployment Preparations ####
*NOTE: all paths here are relative to /opt/kaltura/app*

 - Reload configuration: `touch cache/base.reload` or, on a none production ENV, reload your Apache.
 - Clear cache: `rm -rf cache/*`.
 - Install plugins: `php deployment/base/scripts/installPlugins.php`.
 - Generate clients: 
   
```
# php generator/generate.php pojo
# php generator/generate.php bpmn
# cd /opt/kaltura/web/content/clientlibs/pojo
# mvn -Dmaven.test.skip=true package
# cd /opt/kaltura/web/content/clientlibs/bpmn
# ant
```
 - Restart batch: `/etc/init.d/kaltura-batch restart`.


#### Activiti Deployment Instructions ####

 - Install [Apache Tomcat 7](http://tomcat.apache.org/tomcat-7.0-doc/setup.html#Unix_daemon "Apache Tomcat 7")
 - Make sure $CATALINA_BASE is defined.
 - Install [Apache Ant](http://ant.apache.org/manual/installlist.html "Apache Ant")
 - Download [Activiti 5.17.0](https://github.com/Activiti/Activiti/releases/download/activiti-5.17.0/activiti-5.17.0.zip "Activiti 5.17.0")
 - Open zip: `unzip activiti-5.17.0.zip`
 - Copy WAR files: 
  - `cp activiti-5.17.0/wars/activiti-explorer.war $CATALINA_BASE/webapps/activiti-explorer##5.17.0.war`
  - `cp activiti-5.17.0/wars/activiti-rest.war $CATALINA_BASE/webapps/activiti-rest##5.17.0.war`
 - Restart Apache Tomcat.
 - Create DB **(replace tokens)**: `mysql -uroot -p`

		CREATE DATABASE activiti;
		GRANT INSERT,UPDATE,DELETE,SELECT,ALTER,CREATE,INDEX ON activiti.* TO '@DB1_USER@'@'%';
		FLUSH PRIVILEGES;

 - Edit **(replace tokens)** $CATALINA_BASE/webapps/**activiti-explorer**/WEB-INF/classes/db.properties

		jdbc.driver=com.mysql.jdbc.Driver
		jdbc.url=jdbc:mysql://@DB1_HOST@:@DB1_PORT@/activiti
		jdbc.username=@DB1_USER@
		jdbc.password=@DB1_PASS@

 - Edit **(replace tokens)** $CATALINA_BASE/webapps/**activiti-rest**/WEB-INF/classes/db.properties

		jdbc.driver=com.mysql.jdbc.Driver
		jdbc.url=jdbc:mysql://@DB1_HOST@:@DB1_PORT@/activiti
		jdbc.username=@DB1_USER@
		jdbc.password=@DB1_PASS@

 - Download [mysql jdbc connector 5.0.8](http://cdn.mysql.com/Downloads/Connector-J/mysql-connector-java-5.0.8.zip "mysql jdbc connector 5.0.8")
 - Open zip: `unzip mysql-connector-java-5.0.8.zip`
 - Copy the mysql jdbc connector: `cp mysql-connector-java-5.0.8/mysql-connector-java-5.0.8-bin.jar $CATALINA_BASE/lib/`
 - Restart Apache Tomcat.
 - Open your browser to validate installation **(replace tokens)**: http://@WWW_HOST@:8080/activiti-explorer/
	 - Username: kermit
	 - Password: kermit
 - Generate java pojo and bpmn clients **(replace tokens)**: `php @APP_DIR@/generator/generate.php pojo,bpmn`
 - Edit deployment configuration file **(replace tokens)**: `cp @WEB_DIR@/content/clientlibs/bpmn/deploy/src/activiti.cfg.template.xml @WEB_DIR@/content/clientlibs/bpmn/deploy/src/activiti.cfg.xml`
 - Deploy processes **(replace tokens)**:
	 - `cd @WEB_DIR@/content/clientlibs/bpmn`
	 - `ant`
 - Add Activiti server to Kaltura server using the API **(replace tokens)**: `php @APP_DIR@/tests/standAloneClient/exec.php @APP_DIR@/tests/standAloneClient/activitiServer.xml`
