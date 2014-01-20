from ctypes.wintypes import BOOL
from ctypes import *
import win32process
import pywintypes
import win32event
import win32job
import win32con
import win32api
import sys
import os

#### Params

DESKTOP_NAME = 'UniqueDesktop-%s' % os.getpid()

commandLine = win32api.GetCommandLine()
splittedCommandLine = filter(lambda x: len(x) != 0, commandLine.split(' '))
if len(splittedCommandLine) < 3:
	print 'Usage:\n\tpython %s <child process command line>' % os.path.basename(sys.argv[0])
	sys.exit(10)
childCmdLine = ' '.join(splittedCommandLine[2:])
	
#### Windows API definitions

GENERIC_ALL = 0x10000000

class SECURITY_ATTRIBUTES(Structure):
    _fields_ = [("Length", c_ulong),
                ("SecDescriptor", c_void_p),
                ("InheritHandle", BOOL)]

#### Create a desktop

securityAttributes = SECURITY_ATTRIBUTES()
securityAttributes.Length = sizeof(securityAttributes)
securityAttributes.SecDescriptior = None
securityAttributes.InheritHandle = True

hDesktop = windll.user32.CreateDesktopA(DESKTOP_NAME, None, None, 0, GENERIC_ALL, securityAttributes)
if hDesktop == 0:
	print 'CreateDesktop failed, err=%s' % windll.kernel32.GetLastError()
	sys.exit(20)

#### Create a job

JOB_NAME = 'UniqueJob-%s' % os.getpid()

try:
	hJob = win32job.CreateJobObject(None, JOB_NAME)
except pywintypes.error, e:
	print 'CreateJobObject failed, err=%s' % e[0]
	sys.exit(25)

#### Create the process

startupInfo = win32process.STARTUPINFO()
startupInfo.lpDesktop = DESKTOP_NAME

try:
	processInfo = win32process.CreateProcess(
		None,
		childCmdLine,
		None,
		None,
		True,
		win32con.CREATE_SUSPENDED,
		None,
		None,
		startupInfo)
except pywintypes.error, e:
	print 'CreateProcess failed, err=%s' % e[0]
	sys.exit(30)

hProcess = processInfo[0]

#### Associate the process with the job

try:
	win32job.AssignProcessToJobObject(hJob, hProcess)
except pywintypes.error, e:
	print 'AssignProcessToJobObject failed, err=%s' % e[0]
	sys.exit(33)

#### Resume the process
	
hThread = processInfo[1]
if not win32process.ResumeThread(hThread):
	print 'ResumeThread failed, err=%s' % e[0]
	sys.exit(36)

win32api.CloseHandle(hThread)

#### Wait on the process and get the exit code

try:
	win32event.WaitForSingleObject(hProcess, win32event.INFINITE)
except pywintypes.error, e:
	print 'WaitForSingleObject failed, err=%s' % e[0]
	sys.exit(40)

exitCode = win32process.GetExitCodeProcess(hProcess)

#### Clean up

win32api.CloseHandle(hProcess)

win32job.TerminateJobObject(hJob, 0)

win32api.CloseHandle(hJob)

windll.user32.CloseDesktop(hDesktop)

sys.exit(exitCode)
