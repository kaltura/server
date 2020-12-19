from win32com.client import GetObject
import ntsecuritycon
import win32security
import win32process
import pywintypes
import win32gui
import win32con
import win32api
import copy
import time
import sys

REPORT_FILE = "c:/temp/killWindowsPopupsLog.txt"
MONITORED_PROCESSES = ['pdfcreator.exe']
USER_TIME_THRESHOLD_SEC = 90

def getProcessList():
    WMI = GetObject('winmgmts:')
    processes = WMI.InstancesOf('Win32_Process')
    for cur in processes:
        yield (cur.Properties_('Name').Value, cur.Properties_('ProcessId').Value)

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

def runProcessesCycle():
    for (exeName, processId) in getProcessList():
        if not exeName.lower() in MONITORED_PROCESSES:
            continue
            
        handle = win32api.OpenProcess(win32con.PROCESS_QUERY_INFORMATION | win32con.PROCESS_TERMINATE, 0, processId)
        if not handle:
            continue
            
        userTimeSec = win32process.GetProcessTimes(handle)['UserTime'] / 10000000
        if userTimeSec > USER_TIME_THRESHOLD_SEC:
            print '%s Killing process %s, pid=%s userTimeSec=%s' % (time.ctime(), exeName, processId, userTimeSec)
            win32api.TerminateProcess(handle, 0)
            
        win32api.CloseHandle(handle)

class WindowFinder:
    def __init__(self, matchStr):
        self.matchPath = []
        for curPart in matchStr.split('/'):
            conditions = []
            for condition in curPart.split(','):
                conditions.append(condition.split(':'))
            self.matchPath.append(conditions)

    @staticmethod
    def resolve(matchStr, parentWnd):
        finder = WindowFinder(matchStr)
        finder.findWindow(parentWnd)
        return finder.results

    def findWindow(self, parentWnd = None):
        self.results = []
        self.resultStr = []
        self.curPos = []
        if parentWnd == None:
            win32gui.EnumWindows(WindowFinder.staticFindCallback, self)
        else:
            win32gui.EnumChildWindows(parentWnd, WindowFinder.staticFindCallback, self)

    @staticmethod
    def staticFindCallback(hwnd, self):
        self.findCallback(hwnd)
        
    def findCallback(self, hwnd):
        if not win32gui.IsWindowVisible(hwnd) or not win32gui.IsWindowEnabled(hwnd):
            return
        if self.windowMatches(hwnd):
            self.curPos.append(hwnd)
            if len(self.curPos) >= len(self.matchPath):
                self.results.append(copy.deepcopy(self.curPos))
                self.resultStr.append(win32gui.GetClassName(hwnd) + ":"+ win32gui.GetWindowText(hwnd))
            else:
                try:
                    win32gui.EnumChildWindows(hwnd, WindowFinder.staticFindCallback, self)
                except pywintypes.error:
                    pass
            self.curPos.pop()
        return True

    def windowMatches(self, hwnd):
        details = {
            'text':win32gui.GetWindowText(hwnd),
            'class':win32gui.GetClassName(hwnd),
        }
        for condVar, condVal in self.matchPath[len(self.curPos)]:
            if not condVal in details[condVar]:
                return False
        return True        

def killProcess(processId):
    try:
        handle = win32api.OpenProcess(win32con.PROCESS_TERMINATE, 0, processId)
        if handle:
            win32api.TerminateProcess(handle, 0)
            win32api.CloseHandle(handle)
    except pywintypes.error:
        pass

def logResult(resultStr):
    f = open(REPORT_FILE, 'a')
    for curStr in resultStr:
        str = curStr.strip().replace('\n',' ')	
        f.write(str)
        f.write("\n")
    f.close()

def runWindowsCycle():
    for matchStr, resultIdx, subMatchStr, action in CONFIG:
        finder = WindowFinder(matchStr)
        finder.findWindow()

        if subMatchStr != None:
            results = []
            for curResult in finder.results:
                results += WindowFinder.resolve(subMatchStr, curResult[resultIdx])
            resultIdx = -1
            
        results = map(lambda x: x[resultIdx], finder.results)

        logResult(finder.resultStr)

        if action[0] == ET_WINDOW_MESSAGE:
            _, wmMsg, wParam, lParam = action
            for curResult in results:
                hwnd = curResult
                print '%s Sending window message %s to window %s, text=%s, class=%s' % (time.ctime(), wmMsg, hwnd, win32gui.GetWindowText(hwnd), win32gui.GetClassName(hwnd))
                win32gui.SendMessage(hwnd, wmMsg, wParam, lParam)
        elif action[0] == ET_POST_WINDOW_MESSAGE:
            _, wmMsg, wParam, lParam = action
            for curResult in results:
                hwnd = curResult
                print '%s Posting window message %s to window %s, text=%s, class=%s' % (time.ctime(), wmMsg, hwnd, win32gui.GetWindowText(hwnd), win32gui.GetClassName(hwnd))
                win32gui.PostMessage(hwnd, wmMsg, wParam, lParam)
        elif action[0] == ET_KILL_PROCESS:
            for curResult in results:
                processId = win32process.GetWindowThreadProcessId(curResult)[1]
                print '%s Kill process id %s' % (time.ctime(), processId)
                killProcess(processId)
        if action[0] == ET_PUSH_BUTTON:
            _, buttonMatchStr = action
            for curResult in results:
                buttonHwnds = WindowFinder.resolve(buttonMatchStr, curResult)
                buttonHwnds = map(lambda x: x[0], buttonHwnds)
                for buttonHwnd in buttonHwnds:
                    buttonDlgId = win32gui.GetDlgCtrlID(buttonHwnd)
                    print '%s Pushing button id=%s, text=%s, class=%s, parentWindow=%s' % (time.ctime(), buttonHwnd, win32gui.GetWindowText(buttonHwnd), win32gui.GetClassName(buttonHwnd), buttonDlgId)
                    win32gui.SendMessage(curResult, win32con.WM_COMMAND, buttonDlgId, 0)
        if action[0] == ET_PRESS_KEY:
            _, theKey = action
            for curResult in results:
                hwnd = curResult
                print '%s Sending key %s to window %s, text=%s, class=%s' % (time.ctime(), theKey, hwnd, win32gui.GetWindowText(hwnd), win32gui.GetClassName(hwnd))
                win32gui.SendMessage(hwnd, win32con.WM_KEYDOWN, ord(theKey.upper()), 0x00310001)
                win32gui.SendMessage(hwnd, win32con.WM_CHAR, ord(theKey), 0x00310001)

def safeRunCycle():
    try:
        runWindowsCycle()
    except pywintypes.error:
        pass
    runProcessesCycle()

ET_WINDOW_MESSAGE = 1
ET_KILL_PROCESS = 2
ET_PUSH_BUTTON = 3
ET_PRESS_KEY = 4
ET_POST_WINDOW_MESSAGE = 5

CONFIG = [
    # error accessing file
    ('text:Microsoft Office Word/text:Word experienced an error trying to open the file.', 0, None, (ET_KILL_PROCESS,)),                                # Office 2007
    ('text:Microsoft Office PowerPoint/text:There was an error accessing', 0, None,                 (ET_WINDOW_MESSAGE, win32con.WM_CLOSE, 0, 0)),      # Office 2007
    ('text:Microsoft Office Excel/text:Excel cannot open the file', 0, None,                        (ET_KILL_PROCESS,)),                                # Office 2007
    ('text:Microsoft Word/text:Word cannot open the file', 0, None,                                 (ET_WINDOW_MESSAGE, win32con.WM_CLOSE, 0, 0)),      # Office 2010
    ('text:Microsoft PowerPoint/text:There was an error accessing', 0, None,                        (ET_WINDOW_MESSAGE, win32con.WM_CLOSE, 0, 0)),      # Office 2010
    ('text:Microsoft Excel/text:Excel cannot open the file', 0, None,                               (ET_KILL_PROCESS,)),                                # Office 2010

    # failed to start last time
    ('text:Microsoft Office Word/text:Word failed to start correctly last time', 0, None,               (ET_PUSH_BUTTON, 'text:No,class:Button')),   # Office 2007
    ('text:Microsoft Office PowerPoint/text:PowerPoint failed to start correctly last time', 0, None,   (ET_PUSH_BUTTON, 'text:No,class:Button')),   # Office 2007
    ('text:Microsoft Office Excel/text:Excel failed to start correctly last time', 0, None,             (ET_PUSH_BUTTON, 'text:No,class:Button')),   # Office 2007
    ('text:Microsoft Word/text:Word failed to start correctly last time', 0, None,                      (ET_PUSH_BUTTON, 'text:No,class:Button')),   # Office 2010
    ('text:Microsoft PowerPoint/text:PowerPoint failed to start correctly last time', 0, None,          (ET_PUSH_BUTTON, 'text:No,class:Button')),   # Office 2010
    ('text:Microsoft Excel/text:Excel failed to start correctly last time', 0, None,                    (ET_PUSH_BUTTON, 'text:No,class:Button')),   # Office 2010

    # caused a serious error the last time it was opened - continue ?
    ('text:Microsoft Office Word/text:caused a serious error the last time it was opened.  Would you like to continue opening it?', 0, None,        (ET_PUSH_BUTTON, 'text:Yes,class:Button')),   # Office 2007
    ('text:Microsoft Office PowerPoint/text:caused a serious error the last time it was opened.  Would you like to continue opening it?', 0, None,  (ET_PUSH_BUTTON, 'text:Yes,class:Button')),   # Office 2007
    ('text:Microsoft Office Excel/text:caused a serious error the last time it was opened.  Would you like to continue opening it?', 0, None,       (ET_PUSH_BUTTON, 'text:Yes,class:Button')),   # Office 2007
    ('text:Microsoft Word/text:caused a serious error the last time it was opened.  Would you like to continue opening it?', 0, None,               (ET_PUSH_BUTTON, 'text:Yes,class:Button')),   # Office 2010
    ('text:Microsoft PowerPoint/text:caused a serious error the last time it was opened.  Would you like to continue opening it?', 0, None,         (ET_PUSH_BUTTON, 'text:Yes,class:Button')),   # Office 2010
    ('text:Microsoft Excel/text:caused a serious error the last time it was opened.  Would you like to continue opening it?', 0, None,              (ET_PUSH_BUTTON, 'text:Yes,class:Button')),   # Office 2010

    # caused a serious error the last time it was opened - recover ?
    ('text:Microsoft Office Word/text:caused a serious error the last time it was opened.  You may continue opening it or perform data recovery', 0, None,          (ET_PUSH_BUTTON, 'text:Open,class:Button')),   # Office 2007
    ('text:Microsoft Office PowerPoint/text:caused a serious error the last time it was opened.  You may continue opening it or perform data recovery', 0, None,    (ET_PUSH_BUTTON, 'text:Open,class:Button')),   # Office 2007
    ('text:Microsoft Office Excel/text:caused a serious error the last time it was opened.  You may continue opening it or perform data recovery', 0, None,         (ET_PUSH_BUTTON, 'text:Open,class:Button')),   # Office 2007
    ('text:Microsoft Word/text:caused a serious error the last time it was opened.  You may continue opening it or perform data recovery', 0, None,                 (ET_PUSH_BUTTON, 'text:Open,class:Button')),   # Office 2010
    ('text:Microsoft PowerPoint/text:caused a serious error the last time it was opened.  You may continue opening it or perform data recovery', 0, None,           (ET_PUSH_BUTTON, 'text:Open,class:Button')),   # Office 2010
    ('text:Microsoft Excel/text:caused a serious error the last time it was opened.  You may continue opening it or perform data recovery', 0, None,                (ET_PUSH_BUTTON, 'text:Open,class:Button')),   # Office 2010    

    # PHP crashes
    ('text:CLI/text:Close the program,class:Button', 0, None, (ET_PUSH_BUTTON, 'text:Close the program,class:Button')),
    ('text:Windows PowerShell/text:Close the program,class:Button', 0, None, (ET_PUSH_BUTTON, 'text:Close the program,class:Button')),
    ('text:PDFCreator/text:Close the program,class:Button', 0, None, (ET_PUSH_BUTTON, 'text:Close the program,class:Button')),

    # opening a non office file as a doc/ppt/xls (not x)
    ('text:File Conversion -',                                                                          0, None, (ET_KILL_PROCESS,)),                                # Office 2010
    ('text:Microsoft Excel/text:is in a different format than specified by the file extension.',        0, None, (ET_KILL_PROCESS,)),                                # Office 2010
    ('text:Microsoft PowerPoint/text:can\'t open the type of file represented by',                      0, None, (ET_KILL_PROCESS,)),                                # Office 2010
    ('text:Microsoft Office Excel/text:is in a different format than specified by the file extension.', 0, None, (ET_KILL_PROCESS,)),                                # Office 2010
    ('text:Microsoft Office PowerPoint/text:can\'t open the type of file represented by',               0, None, (ET_KILL_PROCESS,)),                                # Office 2010

    # Excel recalculates formulas when opening files last saved by an earlier version of excel
    ('text:Microsoft Excel,class:NUIDialog/class:NetUIHWND',                                            1, None, (ET_PRESS_KEY, 'n')),          # Office 2010
    ('text:Microsoft Word,class:bosa_sdm_msword',													    0, None, (ET_KILL_PROCESS,)),     							 # Office 2010
    ('text:Microsoft PowerPoint/text:PowerPoint found a problem with content in',                      	0, None, (ET_KILL_PROCESS,)),                                # Office 2010
    ('text:Microsoft Excel/text:Verify that the file is not corrupted and is from a trusted source before opening the file.',  0, None, (ET_KILL_PROCESS,)),         # Office 2010

    ('text:OpenOffice.org 3.3,class:SALFRAME',                              0, None, (ET_PRESS_KEY, 'y')),

    ('text:OpenOffice.org 3.3,class:SALFRAME',                              0, None, (ET_WINDOW_MESSAGE, win32con.WM_ACTIVATE, 0x1, 0x0)),
    ('text:OpenOffice.org 3.3,class:SALFRAME',                              0, None, (ET_POST_WINDOW_MESSAGE, win32con.WM_CHAR, 0x1b, 0x10001)),		# press esc
    ('text:OpenOffice.org 3.3,class:SALSUBFRAME',    						0, None, (ET_PRESS_KEY, 'y')),

    ('text:Application Error/text:Click OK to close the application.',      0, None, (ET_PUSH_BUTTON, 'text:OK,class:Button')),
    ('text:Microsoft Word/text:Do you want to recover the contents of this document',      0, None, (ET_PUSH_BUTTON, 'text:No,class:Button')),

    # Margins outside pritable area
    ('text:Microsoft Word/text:are set outside the printable area of the page.  Do you want to continue?',  0, None, (ET_PUSH_BUTTON, 'text:Yes,class:Button')),
    ('text:Microsoft Word/text:The paper size options you select on the Page Layout tab and the Printers dialog box',  0, None, (ET_PUSH_BUTTON, 'text:Yes,class:Button')),
    ('text:Microsoft Word/text:Windows cannot print due to a problem with the current printer setup.',  0, None, (ET_PUSH_BUTTON, 'text:OK,class:Button')),
    ('text:Microsoft Word/text:Your margins are pretty small. Some of your content might be cut off when you print. Do you still want to print?',  0, None, (ET_PUSH_BUTTON, 'text:Yes,class:Button')),

    ('text:Microsoft Word/text:Your margins are pretty small. It\'s possible some of your content will be cut off when you print. Do you still want to print?',  0, None, (ET_PUSH_BUTTON, 'text:Yes,class:Button')),

    ('text:Microsoft Word/text:Do you want to update this document with the data from the linked files',  0, None, (ET_PUSH_BUTTON, 'text:No,class:Button')),    

    ('text:Microsoft Word/text:Word was unable to read this document',  0, None, (ET_PUSH_BUTTON, 'text:OK,class:Button')),    
    ('text:Microsoft Word/text:blocked by your File Block settings in the Trust Center',  0, None, (ET_PUSH_BUTTON, 'text:OK,class:Button')),    

    ('text:Microsoft PowerPoint/text:Some controls on this presentation can\'t be activated',  0, None, (ET_PUSH_BUTTON, 'text:OK,class:Button')),    
    ('text:Microsoft PowerPoint/text:does not contain any slides and can\'t be printed.',  0, None, (ET_PUSH_BUTTON, 'text:OK,class:Button')),    

    ('text:Microsoft Word/text:Office has detected a problem with this file',  0, None, (ET_PUSH_BUTTON, 'text:OK,class:Button')),    

    ('text:Microsoft Excel/text:Microsoft Excel did not find anything to print.',  0, None, (ET_KILL_PROCESS,)),    
    
    # Read only file
    ('text:Microsoft Word/text:should be opened as read-only unless changes to it need to be saved', 0, None,(ET_PUSH_BUTTON, 'text:Yes,class:Button')),   

	#curropted file
	('text:Microsoft Word/text:The file appears to be corrupted', 0, None,(ET_KILL_PROCESS,)),  
	('text:(Protected View) - Microsoft Word/class:OPH Previewer Window', 0, None,(ET_KILL_PROCESS,)),  
	
	# Command Encoder crash
	('text:CommandEncoder/text:Close the program,class:Button', 0, None, (ET_PUSH_BUTTON, 'text:Close the program,class:Button')),
]

if __name__ == '__main__':
    adjustPrivilege(ntsecuritycon.SE_DEBUG_NAME)

    _, iterCount, sleepTime = sys.argv
    if iterCount == 'infinite':
        while True:
            safeRunCycle()
            time.sleep(int(sleepTime))
    else:
        for i in xrange(int(iterCount)):
            safeRunCycle()
            time.sleep(int(sleepTime))
