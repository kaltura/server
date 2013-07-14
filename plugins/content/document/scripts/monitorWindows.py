import pywintypes
import win32gui
import win32con
import copy
import time

def listCallback(hwnd, ctx):
    if not win32gui.IsWindowVisible(hwnd) or not win32gui.IsWindowEnabled(hwnd):
        return
    childCtx = {}
    try:
        win32gui.EnumChildWindows(hwnd, listCallback, childCtx)
    except pywintypes.error:
        pass
    ctx[hwnd] = (childCtx, win32gui.GetWindowText(hwnd), win32gui.GetClassName(hwnd))

def getWindows():
    result = {}
    win32gui.EnumWindows(listCallback, result)
    return result

def printWindowTree(indent, children):
    for subChildren, curText, curClass in children.values():
        print '\t' * indent + 'txt:%s cls:%s' % (curText, curClass)
        printWindowTree(indent + 1, subChildren)

def printSingleDiff(diffType, stack):
    print '%s %s' % (time.strftime('%Y-%m-%d %H:%M:%S'), diffType)
    for curIndex in xrange(len(stack)):
        curChildren, curText, curClass = stack[curIndex]
        print '\t' * (curIndex + 1) + 'txt:%s cls:%s' % (curText, curClass)

    printWindowTree(len(stack) + 1, stack[-1][0])

def printDiff(stack, prevStatus, newStatus):
    for hwnd in prevStatus:
        stack.append(prevStatus[hwnd])
        if newStatus.has_key(hwnd):
            printDiff(stack, prevStatus[hwnd][0], newStatus[hwnd][0])
        else:
            printSingleDiff('Removed', stack)
        stack.pop()
    for hwnd in newStatus:
        if not prevStatus.has_key(hwnd):
            stack.append(newStatus[hwnd])
            printSingleDiff('Added', stack)
            stack.pop()

prevStatus = getWindows()
while True:
    time.sleep(30)
    try:
        newStatus = getWindows()
    except pywintypes.error:
        continue
    printDiff([], prevStatus, newStatus)
    prevStatus = newStatus
