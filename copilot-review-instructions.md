# GitHub Copilot Code Review Instructions

## Project Overview
## Project Overview

The Kaltura Server repository implements the core PHP backend for the Kaltura media platform. 
It exposes versioned REST-style APIs (under `api_v3`) for media ingestion, metadata management, playback configuration and analytics. 
Internally it’s organized into modular components:

- **Core Services**: business logic for media workflows, user management, permissions
- **Batch Processing** (`batch`): scheduled jobs for transcoding, notifications, reporting
- **Storage Abstraction**: MySQL persistence, file‐system and CDN integrations, caching layers
- **Plugin Framework** (`plugins`): extension points for custom features without core changes
- **Admin Interfaces** (`admin_console`, `var_console`): web UIs for platform administration and diagnostics
- **UI Infra** (`ui_infra`): shared assets and helpers for Kaltura’s front-end consoles
- **Deployment & Infra** (`deployment`, `infra`): scripts and Docker definitions for automated provisioning

## Review Focus Areas

- **Security & Data Handling**
    - Input validation and sanitization (prevent SQL injection, XSS, CSRF)
    - Proper use of prepared statements and parameterized queries
    - Secure handling of credentials and secrets (no hard-coded passwords)

- **Performance & Scalability**
    - Avoidance of N+1 database query patterns
    - Efficient use of caching (Redis, Memcached) and PHP OPcache
    - Profiling hotspots and optimizing heavy loops or large data processing

- **Coding Standards & Conventions**
    - Adherence to PSR-12 (naming, formatting, file structure)
    - Consistent use of namespaces, autoloading via Composer
    - Clear, descriptive DocBlocks for classes and methods

- **Error Handling & Logging**
    - Uniform exception management (custom exceptions, try/catch where needed)
    - Structured logging (Monolog) with appropriate log levels using KalturaLogger
    - Graceful degradation and meaningful error messages to clients

- **Dependency Management & Security**
    - Up-to-date Composer dependencies; audit for known vulnerabilities
    - Minimal direct use of deprecated or unsafe PHP features
    - Vendor code isolation and sandboxing for third-party integrations

- **Test Coverage & Quality**
    - Comprehensive unit tests for critical business logic
    - Integration tests for API endpoints and database interactions
    - CI integration to enforce test pass and code coverage thresholds

## Coding Standards & Conventions
Detail naming conventions, syntax preferences, and style rules.
PHP Coding Convention Guidelines

A concise reference for maintaining consistent, readable, and maintainable PHP code.
    
    1. Naming Conventions
       - Functions: theBigBrownFox()
       - Members (properties/methods): theBigBrownFox
       - Variables: theBigBrownFox
       - Constants: THE_BIG_BROWN_FOX
       - Enums: THE_BIG_BROWN_FOX_ENUM
       - Files: TheBigBrownFox.php
       - Classes: TheBigBrownFox (PascalCase)
       - Exceptions: TheBigBrownFoxException
       - Plugin Names: start with a lowercase letter: thePluginName
    
       2. Indentation
       - Use tabs for indentation (equivalent to 4 spaces).
    
       3. Blank Lines
          Insert a blank line in these cases:
          1. Between class methods.
          2. Between class properties, methods, and constant sections.
          3. Between require/include statements and class definitions.
          4. Between logical parts of the code (e.g., setup, processing, output).
    
       4. Spacing
       - After commas:
         f1($arg1, $arg2, $arg3); // Good
         f1($arg1,$arg2,$arg3);    // Bad
    
       - Around operators:
         $val = 'hello' . 'world'; // Good
         $val ='hello'.'world';     // Bad
         $val='hello'.'world';      // Bad
    
       5. Classes
       - Prefer protected visibility over private, unless stricter encapsulation is required.
    
       6. Size Limits
       - Line length: < 80 characters. Break long lines to fit on one screen without horizontal scrolling.
       - Function length: < 50 lines. Break large functions to keep each fitting on one page without scrolling.
    
       7. Control Structures
       - Braces always, even for single statements:
         if ($value) {
         print('Yes');
         }
    
       - Indent inside braces for multi-line blocks:
         if (a != b) {
         print("boo");
         print("moo");
         }
    
       8. Constants and Literals
       - Avoid numeric literals in code:
         return ERR_FAIL; // Good
         return 100;      // Bad
    
       - Avoid string literals for constants:
         if ($a == CONST_PATH) { /* Good */ }
         if ($a == "PATH")   { /* Bad */ }
    
       9. File Organization
       - One class per file.
       - Filename matches class name:
         class MyClass {}
         // file: MyClass.php
    
       10. Strings
       - Use single quotes when no variable interpolation or escape sequences are needed:
         print 'regular text without processing'; // Good
         print "regular text without processing"; // Bad
         print "Hello $user";                   // Good when interpolation is needed


## Common Pitfalls to Avoid
- Failing to invalidate caches after media settings updates.
- Bypassing plugin registration events.
- Not sanitizing input in API modules.
- Ignoring rate limiting on API endpoints.
- Committing compiled JS or CSS into the repo.
- Access null properties without checks.

## Examples of Ideal Suggestions
Provide clear examples of good versus bad code snippets.

## Language and Framework Specific Considerations

- PHP 7.2 standards, avoid features from PHP 8+ unless explicitly required.

## Custom Prompts for Copilot Use
- **“Review only the diff”:**
  > “Focus exclusively on the changes in this PR diff. Do not comment on unrelated files or code that didn’t change.”

- **“Security audit”:**
  > “Scan the modified code for common PHP security issues (SQL injection, XSS, CSRF) and suggest fixes or mitigations.”

- **“Performance check”:**
  > “Identify any potential N+1 database queries, unbounded loops, or expensive operations introduced in these changes.”

- **“Error handling & logging”:**
  > “Verify that exceptions in new code paths are properly caught, logged (Monolog), and surfaced with meaningful messages.”

- **“Dependency review”:**
  > “Flag any new Composer dependencies and check for known vulnerabilities or unnecessary bloat.”

- **“Test suggestions”:**
  > “Recommend unit or integration tests that should accompany the new functionality, specifying key scenarios and edge cases.”

- **“Plugin integration”:**
  > “For changes under `plugins/`, confirm that `plugin.ini` and `Bootstrap.php` are correctly configured and named.”

- **“Config best practices”:**
  > “Ensure no credentials, URLs or file paths are hard-coded; all environment-specific settings should be in `app/config`.”

- **“Migration reminder”:**
  > “If the diff alters database schemas or data structures, remind to add and version-control a matching migration file.”

- **“Cache invalidation”:**
  > “Point out where cache layers (Redis, Memcached, CDN) need to be invalidated or updated alongside these code changes.”

