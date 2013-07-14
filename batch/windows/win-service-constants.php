<?php

if(!function_exists('win32_create_service'))
	die('win32service extension missing');
	
if(!defined('WIN32_SERVER_ERROR_NORMAL'))
	define('WIN32_SERVER_ERROR_NORMAL', 0x00000001);

if(!defined('WIN32_SERVICE_WIN32_OWN_PROCESS')) //The service runs in its own process.
	define('WIN32_SERVICE_WIN32_OWN_PROCESS', 0x00000010);
if(!defined('WIN32_SERVICE_INTERACTIVE_PROCESS')) //The service can interact with the desktop. This option is not available on Windows Vista or later.
	define('WIN32_SERVICE_INTERACTIVE_PROCESS', 0x00000100);
if(!defined('WIN32_SERVICE_WIN32_OWN_PROCESS_INTERACTIVE')) //The service runs in its own process and can interact with the desktop. This option is not available on Windows Vista or later.
	define('WIN32_SERVICE_WIN32_OWN_PROCESS_INTERACTIVE', 0x00000110);

if(!defined('WIN32_SERVICE_CONTINUE_PENDING')) //The service continue is pending.
	define('WIN32_SERVICE_CONTINUE_PENDING', 0x00000005);
if(!defined('WIN32_SERVICE_PAUSE_PENDING')) //The service pause is pending.
	define('WIN32_SERVICE_PAUSE_PENDING', 0x00000006);
if(!defined('WIN32_SERVICE_PAUSED')) //The service is paused.
	define('WIN32_SERVICE_PAUSED', 0x00000007);
if(!defined('WIN32_SERVICE_RUNNING')) //The service is running.
	define('WIN32_SERVICE_RUNNING', 0x00000004);
if(!defined('WIN32_SERVICE_START_PENDING')) //The service is starting.
	define('WIN32_SERVICE_START_PENDING', 0x00000002);
if(!defined('WIN32_SERVICE_STOP_PENDING')) //The service is stopping.
	define('WIN32_SERVICE_STOP_PENDING', 0x00000003);
if(!defined('WIN32_SERVICE_STOPPED')) //The service is not running.
	define('WIN32_SERVICE_STOPPED', 0x00000001);

if(!defined('WIN32_SERVICE_CONTROL_CONTINUE')) //Notifies a paused service that it should resume.
	define('WIN32_SERVICE_CONTROL_CONTINUE', 0x00000003);
if(!defined('WIN32_SERVICE_CONTROL_INTERROGATE')) //Notifies a service that it should report its current status information to the service control manager.
	define('WIN32_SERVICE_CONTROL_INTERROGATE', 0x00000004);
if(!defined('WIN32_SERVICE_CONTROL_PAUSE')) //Notifies a service that it should pause.
	define('WIN32_SERVICE_CONTROL_PAUSE', 0x00000002);
if(!defined('WIN32_SERVICE_CONTROL_PRESHUTDOWN')) //Notifies a service that the system will be shutting down. A service that handles this notification blocks system shutdown until the service stops or the preshutdown time-out interval expires. This value is not supported by Windows Server 2003 and Windows XP/2000.
	define('WIN32_SERVICE_CONTROL_PRESHUTDOWN', 0x0000000F);
if(!defined('WIN32_SERVICE_CONTROL_SHUTDOWN')) //Notifies a service that the system is shutting down so the service can perform cleanup tasks. If a service accepts this control code, it must stop after it performs its cleanup tasks. After the SCM sends this control code, it will not send other control codes to the service.
	define('WIN32_SERVICE_CONTROL_SHUTDOWN', 0x00000005);
if(!defined('WIN32_SERVICE_CONTROL_STOP')) //Notifies a service that it should stop.
	define('WIN32_SERVICE_CONTROL_STOP', 0x00000001);

if(!defined('WIN32_SERVICE_ACCEPT_PAUSE_CONTINUE')) //The service can be paused and continued. This control code allows the service to receive WIN32_SERVICE_CONTROL_PAUSE and WIN32_SERVICE_CONTROL_CONTINUE notifications.
	define('WIN32_SERVICE_ACCEPT_PAUSE_CONTINUE', 0x00000002);
if(!defined('WIN32_SERVICE_ACCEPT_PRESHUTDOWN')) //The service can perform preshutdown tasks. This control code enables the service to receive WIN32_SERVICE_CONTROL_PRESHUTDOWN notifications. This value is not supported by Windows Server 2003 and Windows XP/2000.
	define('WIN32_SERVICE_ACCEPT_PRESHUTDOWN', 0x00000100);
if(!defined('WIN32_SERVICE_ACCEPT_SHUTDOWN')) //The service is notified when system shutdown occurs. This control code allows the service to receive WIN32_SERVICE_CONTROL_SHUTDOWN notifications.
	define('WIN32_SERVICE_ACCEPT_SHUTDOWN', 0x00000004);
if(!defined('WIN32_SERVICE_ACCEPT_STOP')) //The service can be stopped. This control code allows the service to receive WIN32_SERVICE_CONTROL_STOP notifications.
	define('WIN32_SERVICE_ACCEPT_STOP', 0x00000001);

if(!defined('WIN32_SERVICE_AUTO_START')) //A service started automatically by the service control manager during system startup.
	define('WIN32_SERVICE_AUTO_START', 0x00000002);
if(!defined('WIN32_SERVICE_DEMAND_START')) //A service started by the service control manager when a process calls the StartService function.
	define('WIN32_SERVICE_DEMAND_START', 0x00000003);
if(!defined('WIN32_SERVICE_DISABLED')) //A service that cannot be started. Attempts to start the service result in the error code WIN32_ERROR_SERVICE_DISABLED.
	define('WIN32_SERVICE_DISABLED', 0x00000004);

if(!defined('WIN32_SERVICE_ERROR_IGNORE')) //The startup program ignores the error and continues the startup operation.
	define('WIN32_SERVICE_ERROR_IGNORE', 0x00000000);
if(!defined('WIN32_SERVICE_ERROR_NORMAL')) //The startup program logs the error in the event log but continues the startup operation.
	define('WIN32_SERVICE_ERROR_NORMAL', 0x00000001);

if(!defined('WIN32_SERVICE_RUNS_IN_SYSTEM_PROCESS')) //The service runs in a system process that must always be running.
	define('WIN32_SERVICE_RUNS_IN_SYSTEM_PROCESS', 0x00000001);

if(!defined('WIN32_ERROR_ACCESS_DENIED')) //The handle to the SCM database does not have the appropriate access rights.
	define('WIN32_ERROR_ACCESS_DENIED', 0x00000005);
if(!defined('WIN32_ERROR_CIRCULAR_DEPENDENCY')) //A circular service dependency was specified.
	define('WIN32_ERROR_CIRCULAR_DEPENDENCY', 0x00000423);
if(!defined('WIN32_ERROR_DATABASE_DOES_NOT_EXIST')) //The specified database does not exist.
	define('WIN32_ERROR_DATABASE_DOES_NOT_EXIST', 0x00000429);
if(!defined('WIN32_ERROR_DEPENDENT_SERVICES_RUNNING')) //The service cannot be stopped because other running services are dependent on it.
	define('WIN32_ERROR_DEPENDENT_SERVICES_RUNNING', 0x0000041B);
if(!defined('WIN32_ERROR_DUPLICATE_SERVICE_NAME')) //The display name already exists in the service control manager database either as a service name or as another display name.
	define('WIN32_ERROR_DUPLICATE_SERVICE_NAME', 0x00000436);
if(!defined('WIN32_ERROR_FAILED_SERVICE_CONTROLLER_CONNECT')) //This error is returned if the program is being run as a console application rather than as a service. If the program will be run as a console application for debugging purposes, structure it such that service-specific code is not called.
	define('WIN32_ERROR_FAILED_SERVICE_CONTROLLER_CONNECT', 0x00000427);
if(!defined('WIN32_ERROR_INSUFFICIENT_BUFFER')) //The buffer is too small for the service status structure. Nothing was written to the structure.
	define('WIN32_ERROR_INSUFFICIENT_BUFFER', 0x0000007A);
if(!defined('WIN32_ERROR_INVALID_DATA')) //The specified service status structure is invalid.
	define('WIN32_ERROR_INVALID_DATA', 0x0000000D);
if(!defined('WIN32_ERROR_INVALID_HANDLE')) //The handle to the specified service control manager database is invalid.
	define('WIN32_ERROR_INVALID_HANDLE', 0x00000006);
if(!defined('WIN32_ERROR_INVALID_LEVEL')) //The InfoLevel parameter contains an unsupported value.
	define('WIN32_ERROR_INVALID_LEVEL', 0x0000007C);
if(!defined('WIN32_ERROR_INVALID_NAME')) //The specified service name is invalid.
	define('WIN32_ERROR_INVALID_NAME', 0x0000007B);
if(!defined('WIN32_ERROR_INVALID_PARAMETER')) //A parameter that was specified is invalid.
	define('WIN32_ERROR_INVALID_PARAMETER', 0x00000057);
if(!defined('WIN32_ERROR_INVALID_SERVICE_ACCOUNT')) //The user account name specified in the user parameter does not exist. See win32_create_service().
	define('WIN32_ERROR_INVALID_SERVICE_ACCOUNT', 0x00000421);
if(!defined('WIN32_ERROR_INVALID_SERVICE_CONTROL')) //The requested control code is not valid, or it is unacceptable to the service.
	define('WIN32_ERROR_INVALID_SERVICE_CONTROL', 0x0000041C);
if(!defined('WIN32_ERROR_PATH_NOT_FOUND')) //The service binary file could not be found.
	define('WIN32_ERROR_PATH_NOT_FOUND', 0x00000003);
if(!defined('WIN32_ERROR_SERVICE_ALREADY_RUNNING')) //An instance of the service is already running.
	define('WIN32_ERROR_SERVICE_ALREADY_RUNNING', 0x00000420);
if(!defined('WIN32_ERROR_SERVICE_CANNOT_ACCEPT_CTRL')) //The requested control code cannot be sent to the service because the state of the service is WIN32_SERVICE_STOPPED, WIN32_SERVICE_START_PENDING, or WIN32_SERVICE_STOP_PENDING.
	define('WIN32_ERROR_SERVICE_CANNOT_ACCEPT_CTRL', 0x00000425);
if(!defined('WIN32_ERROR_SERVICE_DATABASE_LOCKED')) //The database is locked.
	define('WIN32_ERROR_SERVICE_DATABASE_LOCKED', 0x0000041F);
if(!defined('WIN32_ERROR_SERVICE_DEPENDENCY_DELETED')) //The service depends on a service that does not exist or has been marked for deletion.
	define('WIN32_ERROR_SERVICE_DEPENDENCY_DELETED', 0x00000433);
if(!defined('WIN32_ERROR_SERVICE_DEPENDENCY_FAIL')) //The service depends on another service that has failed to start.
	define('WIN32_ERROR_SERVICE_DEPENDENCY_FAIL', 0x0000042C);
if(!defined('WIN32_ERROR_SERVICE_DISABLED')) //The service has been disabled.
	define('WIN32_ERROR_SERVICE_DISABLED', 0x00000422);
if(!defined('WIN32_ERROR_SERVICE_DOES_NOT_EXIST')) //The specified service does not exist as an installed service.
	define('WIN32_ERROR_SERVICE_DOES_NOT_EXIST', 0x00000424);
if(!defined('WIN32_ERROR_SERVICE_EXISTS')) //The specified service already exists in this database.
	define('WIN32_ERROR_SERVICE_EXISTS', 0x00000431);
if(!defined('WIN32_ERROR_SERVICE_LOGON_FAILED')) //The service did not start due to a logon failure. This error occurs if the service is configured to run under an account that does not have the "Log on as a service" right.
	define('WIN32_ERROR_SERVICE_LOGON_FAILED', 0x0000042D);
if(!defined('WIN32_ERROR_SERVICE_MARKED_FOR_DELETE')) //The specified service has already been marked for deletion.
	define('WIN32_ERROR_SERVICE_MARKED_FOR_DELETE', 0x00000430);
if(!defined('WIN32_ERROR_SERVICE_NO_THREAD')) //A thread could not be created for the service.
	define('WIN32_ERROR_SERVICE_NO_THREAD', 0x0000041E);
if(!defined('WIN32_ERROR_SERVICE_NOT_ACTIVE')) //The service has not been started.
	define('WIN32_ERROR_SERVICE_NOT_ACTIVE', 0x00000426);
if(!defined('WIN32_ERROR_SERVICE_REQUEST_TIMEOUT')) //The process for the service was started, but it did not call StartServiceCtrlDispatcher, or the thread that called StartServiceCtrlDispatcher may be blocked in a control handler function.
	define('WIN32_ERROR_SERVICE_REQUEST_TIMEOUT', 0x0000041D);
if(!defined('WIN32_ERROR_SHUTDOWN_IN_PROGRESS')) //The system is shutting down; this function cannot be called.
	define('WIN32_ERROR_SHUTDOWN_IN_PROGRESS', 0x0000045B);
if(!defined('WIN32_NO_ERROR')) //No error.
	define('WIN32_NO_ERROR', 0x00000000);

if(!defined('WIN32_ABOVE_NORMAL_PRIORITY_CLASS')) //Process that has priority above WIN32_NORMAL_PRIORITY_CLASS but below WIN32_HIGH_PRIORITY_CLASS.
	define('WIN32_ABOVE_NORMAL_PRIORITY_CLASS', 0x00008000);
if(!defined('WIN32_BELOW_NORMAL_PRIORITY_CLASS')) //Process that has priority above WIN32_IDLE_PRIORITY_CLASS but below WIN32_NORMAL_PRIORITY_CLASS.
	define('WIN32_BELOW_NORMAL_PRIORITY_CLASS', 0x00004000);
if(!defined('WIN32_HIGH_PRIORITY_CLASS')) //Process that performs time-critical tasks that must be executed immediately. The threads of the process preempt the threads of normal or idle priority class processes. An example is the Task List, which must respond quickly when called by the user, regardless of the load on the operating system. Use extreme care when using the high-priority class, because a high-priority class application can use nearly all available CPU time.
	define('WIN32_HIGH_PRIORITY_CLASS', 0x00000080);
if(!defined('WIN32_IDLE_PRIORITY_CLASS')) //Process whose threads run only when the system is idle. The threads of the process are preempted by the threads of any process running in a higher priority class. An example is a screen saver. The idle-priority class is inherited by child processes.
	define('WIN32_IDLE_PRIORITY_CLASS', 0x00000040);
if(!defined('WIN32_NORMAL_PRIORITY_CLASS')) //Process with no special scheduling needs.
	define('WIN32_NORMAL_PRIORITY_CLASS', 0x00000020);
if(!defined('WIN32_REALTIME_PRIORITY_CLASS')) //Process that has the highest possible priority. The threads of the process preempt the threads of all other processes, including operating system processes performing important tasks. For example, a real-time process that executes for more than a very brief interval can cause disk caches not to flush or cause the mouse to be unresponsive.
	define('WIN32_REALTIME_PRIORITY_CLASS', 0x00000100);
