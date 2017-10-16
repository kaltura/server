import ntsecuritycon
import win32security
import pywintypes
import win32print
import win32con
import win32api
import os.path
import ctypes
import time
import sys

def getDefaultPrinter():
    try:
        return win32print.GetDefaultPrinter()
    except RuntimeError:        # The default printer was not found.
        return None


TH32CS_SNAPPROCESS = 0x00000002
class PROCESSENTRY32(ctypes.Structure):
     _fields_ = [("dwSize", ctypes.c_ulong),
                 ("cntUsage", ctypes.c_ulong),
                 ("th32ProcessID", ctypes.c_ulong),
                 ("th32DefaultHeapID", ctypes.c_ulong),
                 ("th32ModuleID", ctypes.c_ulong),
                 ("cntThreads", ctypes.c_ulong),
                 ("th32ParentProcessID", ctypes.c_ulong),
                 ("pcPriClassBase", ctypes.c_ulong),
                 ("dwFlags", ctypes.c_ulong),
                 ("szExeFile", ctypes.c_char * 260)]

def getProcessList():
     # See http://msdn2.microsoft.com/en-us/library/ms686701.aspx
     CreateToolhelp32Snapshot = ctypes.windll.kernel32.\
                                CreateToolhelp32Snapshot
     Process32First = ctypes.windll.kernel32.Process32First
     Process32Next = ctypes.windll.kernel32.Process32Next
     CloseHandle = ctypes.windll.kernel32.CloseHandle
     hProcessSnap = CreateToolhelp32Snapshot(TH32CS_SNAPPROCESS, 0)
     pe32 = PROCESSENTRY32()
     pe32.dwSize = ctypes.sizeof(PROCESSENTRY32)
     if Process32First(hProcessSnap,
                       ctypes.byref(pe32)) == win32con.FALSE:
         print >> sys.stderr, "Failed getting first process."
         return
     while True:
         yield (pe32.szExeFile, pe32.th32ProcessID)
         if Process32Next(hProcessSnap, ctypes.byref(pe32)) == win32con.FALSE:
             break
     CloseHandle(hProcessSnap)

def adjustPrivilege(priv, enable = True):
    flags = ntsecuritycon.TOKEN_ADJUST_PRIVILEGES | ntsecuritycon.TOKEN_QUERY
    htoken = win32security.OpenProcessToken(win32api.GetCurrentProcess(), flags)
    id = win32security.LookupPrivilegeValue(None, priv)
    if enable:
        newPrivileges = [(id, ntsecuritycon.SE_PRIVILEGE_ENABLED)]
    else:
        newPrivileges = [(id, 0)]
    win32security.AdjustTokenPrivileges(htoken, 0, newPrivileges)
    win32api.CloseHandle(htoken)

def killProcess(processId):
    adjustPrivilege(ntsecuritycon.SE_DEBUG_NAME)
    try:
        handle = win32api.OpenProcess(win32con.PROCESS_TERMINATE, 0, processId)
        if handle:
            win32api.TerminateProcess(handle, 0)
            win32api.CloseHandle(handle)
    except pywintypes.error:
        pass


def killProcessesByName(exeNames):
    result = False
    for (exeName, processId) in getProcessList():
        if exeName.lower() in exeNames:
            print 'Killing %s %s' % (exeName, processId)
            killProcess(processId)
            result = True
    return result

def clearJobsQueue():
    printer = win32print.OpenPrinter('PDFCreator')
    jobs = win32print.EnumJobs(printer, 0, 100, 2)
    while len(jobs) > 0:
        for currentJob in jobs:
            print '\nDeleting print job with id [' + str(currentJob['JobId']) + ']'
            win32print.SetJob(printer, currentJob['JobId'], 0, None, win32print.JOB_CONTROL_DELETE)
        win32print.ClosePrinter(printer)
        time.sleep(.5)
        printer = win32print.OpenPrinter('PDFCreator')
        jobs = win32print.EnumJobs(printer, 0, 100, 2)
    win32print.ClosePrinter(printer)

if len(sys.argv) < 4:
    print 'wrong usage of this script. usage: %s {inputFile} {outFile} [--readOnly]' % os.path.dirname(__file__);
    sys.exit(1)

# build the command line
if sys.argv[-1] == '--readonly':
    readOnly = True
    inputFile = sys.argv[-3]
    outputFile = sys.argv[-2]
    commandParams = sys.argv[1:-3]
else:
    readOnly = False
    inputFile = sys.argv[-2]
    outputFile = sys.argv[-1]
    commandParams = sys.argv[1:-2]

# clean up any previous convert leftovers
if killProcessesByName(['powerpnt.exe', 'excel.exe', 'winword.exe', 'pdfcreator.exe', 'soffice.exe']):
    time.sleep(5)
clearCacheCmd = '%s /CLEARCACHE /NoStart' % ' '.join(commandParams)
print '\nclearing cache: %s' % clearCacheCmd
os.system(clearCacheCmd)

# make sure the default printer is set appropriately
PDF_CREATOR_PRINTER = 'PDFCreator'
if getDefaultPrinter() != PDF_CREATOR_PRINTER:
    print 'setting default printer to %s' % PDF_CREATOR_PRINTER
    win32print.SetDefaultPrinter(PDF_CREATOR_PRINTER)

# build the command line
commandParams.append('/NoStart')

inputFileExt = os.path.splitext(inputFile)[1].lower()
if readOnly and inputFileExt == '.pdf':
    commandParams.append('/IF"%s"' % inputFile)
    commandParams.append('/OF"%s"' % outputFile)
else:
    commandParams.append('/PF"%s"' % inputFile)

#make sure print queue is empty, if not delete existing jobs.
clearJobsQueue()

printer = win32print.OpenPrinter('PDFCreator')
command = ' '.join(commandParams)
    
# execute the command
print '\ncommand: %s' % command
os.system(command)

# wait until the printer queue becomes empty


while True:
    if len(win32print.EnumJobs(printer, 0, 1, 2)) == 0:
        break
    time.sleep(.5)
    