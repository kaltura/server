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
