"use strict";

exports.getTests = function(Unit, options) {
    "use strict";

    var proto = require('proto')
    var Future = require('async-future')

    var indent = require("../indent")

    var CustomError = proto(Error, function(superclass) {
        this.name = 'CustomError'

        this.init = function(msg, properties) {
            superclass.call(this, msg)
            for(var n in properties) {
                this[n] = properties[n]
            }
        }
    })

    function stringTestResults(test) {
        if(test.type == 'group') {
            var results = '[ '+test.results.map(function(x) {
                return indent("  ",stringTestResults(x))
            }).join(",\n").trim()+"\n"
            +"]"

            var exceptionMessages = "["+test.exceptions.join(",")+"]"

            return  "{ type: "+test.type+",\n"
                   +"  name: "+test.name+",\n"
                   +"  results: \n"+indent("  ",results)+",\n"
                   +"  exceptions: "+exceptionMessages+",\n"
                   +"}"
        } else {
            return  "{ type: "+test.type+",\n"
                   +"  success: "+test.success+",\n"
                   +"  sourceLines: "+test.sourceLines+",\n"
                   +"  test: "+test.test+",\n"
                   +"  file: "+test.file+",\n"
                   +"  line: "+test.line+",\n"
                   +"  column: "+test.column+",\n"
                   +"}"
        }
    }

    var printTestOutput = options.printTestOutput
    var print = options.print

    function announce(name) {
        print('\n'+name+"\n")
    }


    var simpleSuccess, simpleFailure, simpleException, simpleExceptionNoTrace, simpleAsyncException, simpleTimeout, testGroups
    //*
    Future(true)
    .then(function() {

        announce("simple success")
        options.reset()
        simpleSuccess = Unit.test(function() {
            this.ok(true)
        })

        return printTestOutput(simpleSuccess, 'simpleSuccess')

    }) //*
    .then(function() {
        announce("simple failure")
        options.reset()

        simpleFailure = Unit.test(function() {
            this.ok(false)
        })
        return printTestOutput(simpleFailure, 'simpleFailure')

    }).then(function() {
        announce("simple exception")
        options.reset()
        simpleException = Unit.test(function() {
            throw Error("sync")
        })
        return printTestOutput(simpleException, 'simpleException')

    }).then(function() {
        announce("simple exception without stack trace")
        options.reset()
        simpleExceptionNoTrace = Unit.test(function() {
            throw "I think I'm an exception"
        })
        return printTestOutput(simpleExceptionNoTrace, 'simpleExceptionNoTrace')

    })
    .then(function() {
        announce("simple async exception")
        options.reset()
        var simpleAsyncExceptionFuture = new Future()
        simpleAsyncException = Unit.test(function(t) {
            this.count(1) // forces the test to wait to end
            setTimeout(function() {
                simpleAsyncExceptionFuture.return()
                setTimeout(function() {
                    t.ok(true) // don't time out
                }, 0)
                throw Error("Async")
            }, 0)
        })

        return simpleAsyncExceptionFuture

    }).then(function() {
        return printTestOutput(simpleAsyncException, 'simpleAsyncException')

    })
    .then(function() {
        announce("assert after tests are written")
        options.reset()
        var assertAfterDoneFuture = new Future()
        var assertAfterDone = Unit.test(function(t) {
            setTimeout(function() {
                t.ok(true)
                t.ok(false) // do two to make sure the warning is only printed once
                setTimeout(function() {
                    assertAfterDoneFuture.return()
                }, 50)
            }, 50)
        })

        printTestOutput(assertAfterDone, 'assertAfterDone')

        return assertAfterDoneFuture

    })
    .then(function() {
        announce("simple timeout / failed count")
        options.reset()
        simpleTimeout = Unit.test(function() {
            this.timeout(100)
            this.count(1)
        })
        return printTestOutput(simpleTimeout, 'simpleTimeout', 500)

    })
    .then(function() {
        announce("toString")
        options.reset()

        var futuresToWaitOn = []
        testGroups = Unit.test("Testing the Unit Tester", function() {
            this.timeout(5000)

            this.ok(true) // here to test to make sure these aren't being counted as tests alongside the this.tests

            this.test("Test Some Stuff", function() {
                this.test("assertSomething", function() {
                    this.ok(5 === 5)
                })
                this.test("shouldFail", function() {
                    this.ok(5 === 3, 'actual', 'expected')
                    this.eq(true, false)
                    this.log("test log")
                    this.count(2)
                })
                this.test("shouldThrowException", function() {
                    this.ok(true)
                    this.count(1)
                    throw new Error("Ahhhhh!")
                })
                this.test("should throw an asynchronous exception", function(t) {
                    this.count(1)
                    var f = new Future
                    futuresToWaitOn.push(f)
                    setTimeout(function() {
                        f.return()
                        t.ok(true)
                        throw Error("Asynchronous Ahhhhh!")
                    },0)
                })

                this.count(4)
            })
            this.test("SuccessfulTestGroup", function() {
                this.test("yay", function() {
                    this.eq(true, true)
                })
            })

            this.test("long before/after", function() {
                var x = 0
                this.before(function() {
                    for(var n=0; n<1000000; n++) {
                        x += x+1
                    }
                })

                this.test("one", function() {
                    this.ok(x === Infinity, x)
                })
            })

            this.test("logs", function() {

                var array = [1,'a',{a:'b', b:[1,2]}]
                var object = {some: 'object'}
                var error = Error('test')
                var customError = CustomError('testCustom', {a:1, b:'two', c:[1,2,3], d:{four:4}})

                this.log("string")
                this.log(object)
                this.log(array)
                this.log(error)
                this.log(customError)
                this.log('')
                this.log("string", object, array, error, customError)

                this.ok(false, "string")
                this.ok(false, object)
                this.ok(false, array)
                this.ok(false, error)
                this.ok(false, customError)

            })
        })


        return Future.all(futuresToWaitOn)

    }).then(function() {
        return testGroups.string()

    })
    .then(function(string) {
        print(string)	// returns plain text
        return printTestOutput(testGroups, 'testGroups', 400)

    }).then(function(){
        announce("Visually verify the following tests")

        var test = Unit.test('should print late-events warning', function(t) {
            setTimeout(function() {
                t.ok(true)
            },500)
        })

        return printTestOutput(test, 'should print late-events warning').then(function(){
            var f = new Future
            setTimeout(function() { // give the late event warning time to print cleanly (without being mucked up by the next text
                f.return()
            },800)
            return f
        })

    }).then(function() {
        var realTest = Unit.test("Testing basicFormatter (this should succeed)", function() {
            var formatBasic = require("../basicFormatter")
            this.timeout(5000)

            this.test("simple exception", function(t) {
                var simpleException2 = Unit.test(function() {
                    throw Error("sync")
                })

                this.count(10)
                formatBasic(simpleException2, false, {
                    group: function(name, totalDuration, testSuccesses, testFailures,
                                          assertSuccesses, assertFailures, exceptions,
                                          testResults, exceptionResults, nestingLevel) {

                        t.ok(name === undefined)
                        t.ok(testSuccesses === 0)
                        t.ok(testFailures === 0)
                        t.ok(assertSuccesses === 0)
                        t.ok(assertFailures === 0)
                        t.ok(exceptions === 1)
                        t.ok(testResults.length === 0)
                        t.ok(exceptionResults.length === 1)
                        t.ok(nestingLevel === 0)
                    },
                    assert: function(result, test) {
                        t.ok(false)
                    },
                    exception: function(e) {
                        t.ok(e.message === 'sync')
                    },
                    log: function(msg) {
                        t.ok(false)
                    }
                })
            })

            this.test("formatBasic", function(t) {
                this.count(4)
                formatBasic(testGroups, false, {
                    group: function(name, totalDuration, testSuccesses, testFailures,
                                          assertSuccesses, assertFailures, exceptions,
                                          testResults, exceptionResults, nestingLevel) {

                        if(name === "Testing the Unit Tester") {
                            t.test("Testing the Unit Tester", function(t) {
                                if(options.env === 'node') {
                                    var expectedExceptionResults = 0
                                } else {
                                    var expectedExceptionResults = 1
                                }

                                this.count(8)
                                t.ok(testSuccesses === 3, testSuccesses)
                                t.ok(testFailures === 2, testFailures)
                                t.ok(testResults.length === 5, testResults.length)
                                t.ok(exceptionResults.length === expectedExceptionResults, exceptionResults.length, expectedExceptionResults)

                                t.ok(assertSuccesses === 10, assertSuccesses)
                                t.ok(assertFailures === 7, assertFailures)
                                t.ok(exceptions === 2, exceptions)

                                t.ok(totalDuration !== undefined)
                            })

                        } else if(name === "Test Some Stuff") {
                            t.test("Test Some Stuff", function(t) {
                                if(options.env === 'node') {
                                    var successes = 2
                                    var failures = 3
                                } else {
                                    var successes = 3
                                    var failures = 2
                                }

                                t.ok(testSuccesses === successes, testSuccesses, successes)
                                t.ok(testFailures === failures, testFailures, failures)
                                t.ok(testResults.length === 5, testResults.length)
                                t.ok(exceptionResults.length === 0, exceptionResults.length)
                            })

                        } else if(name === "assertSomething") {
                            t.test("assertSomething", function(t) {
                                t.ok(testSuccesses === 1, testSuccesses)
                                t.ok(testFailures === 0, testFailures)
                                t.ok(testResults.length === 1, testResults.length)
                                t.ok(exceptionResults.length === 0, exceptionResults.length)
                            })

                        } else if(name === "shouldFail") {
                            t.test("shouldFail", function(t) {
                                t.ok(testSuccesses === 1, testSuccesses)
                                t.ok(testFailures === 2, testFailures)
                                t.ok(testResults.length === 4, testResults.length)
                                t.ok(exceptionResults.length === 0, exceptionResults.length)
                            })

                        } else if(name === "shouldThrowException") {

                        } else if(name === "should throw an asynchronous exception") {

                        } else if(name === "SuccessfulTestGroup") {

                        } else if(name === "long before/after") {

                        } else if(name === "one") {

                        } else if(name === "yay") {

                        } else if(name === "logs") {

                        } else {
                            t.ok(false, name)
                        }
                    },
                    assert: function(result, test) {
                        return result
                    },
                    exception: function(e) {
                        return e
                    },
                    log: function(msg) {
                        return msg
                    }
                })
            })

            this.test("nameless subtest", function() {
                options.reset()
                var test = Unit.test(function() {
                    this.test(function() {
                        this.ok(true)
                    })
                })
                printTestOutput(test, 'nameless subtest', 400)
            })

            this.test("default formats", function() {
                this.test('string exceptions', function(t) {
                    this.count(2)

                    options.reset()
                    var test = Unit.test("exception format", function() {
                        throw "strings aren't exceptions yo"
                    })

                    test.string().then(function(resultOutput) {
                        t.log(resultOutput)

                        t.ok(resultOutput.indexOf("strings aren't exceptions yo") !== -1)
                        t.ok(resultOutput.indexOf("t\n") === -1 || resultOutput.indexOf("g\n") === -1) // this tests for the case where a string is printed one character per line (which is not desirable)
                    }).done()
                })

                this.test('logging exceptions with custom properties', function(t) {
                    this.count(3)

                    options.reset()
                    var test = Unit.test("custom error", function() {
                        var customError = CustomError('testCustom', {a:1, b:'two', c:[1,2,3], d:{four:4}})
                        this.log(customError)
                    })

                    test.string().then(function(resultOutput) {
                        t.log(resultOutput)

                        t.ok(resultOutput.indexOf("testCustom") !== -1)
                        t.ok(resultOutput.indexOf("two") !== -1)
                        t.ok(resultOutput.indexOf("four") !== -1)
                    }).done()
                })
            })
        })

        console.log("")
        printTestOutput(realTest, 'realTest', 400)

    })
    .catch(function(e) {
        console.log(e.stack)
    }).done()
    //*/
}