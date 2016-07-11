# PHProblemLogger

**Currently in Development**

This tool helps create a better log of the current environment when a problem happens in PHP - like an Exception or an Error.
Exceptions are handled by an exception handler - and errors are converted into a custom ErrorException and thrown.

## Installation Instructions

Install the latest version with

**coming soon**  
```bash
$ composer require aaronsaray/phproblemlogger
```

## Documentation

### Getting Started

This library will add on to the existing uncaught exception and error handler using a PSR-3 compatible logging instance
for output of the current environment.  The configuration of this logger is *additive* meaning that out of the box nothing
is logged during a problem.  You must configure filters to indicate what you'd like to be logged.

All uncaught exceptions are processed using this library.  Error types of E_ALL | E_STRICT are turned into a custom Exception and processed.

To use this tool, have an instance of a PSR-3 logger available and at least one filter callable ready.  

**Important** If you have your own exception handling in place (like a redirect or a nice view), create a new instance of
PHProblemLogger **after** you've defined your handler.  PHProblemLogger keeps a reference to the previous handler and 
will call it after it after processing finishes.  This does **not** apply to the error handler.  This is overwritten and 
errors are converted into an `ErrorException`.

### An Example

In this example, we want to log `$_SERVER` and `$_COOKIE` variables to our log file whenever there is an exception.

```php
use AaronSaray\PHProblemLogger\Handler as Handler;
use AaronSaray\PHProblemLogger\Handler as HandlerFilter;

$monolog = $monolog; // this is an instance of a PSR-3 logger interface

$problemHandler = new Handler($monolog);
$problemHandler
    ->server(function(array $payload) {
        return $payload;
    })
    ->cookie(HandlerFilter::all());
```

To create the new instance of the problem handler, create a new instance of the class and pass in a `LoggerInterface` class as the first
parameter of the constructor.  This will automatically add PHPProblemHandler to the stack of error and exception handlers 
in your application.  Then, for each portion of the runtime environment that we want, we pass a callable function to it.

First, in order to get all `$_SERVER` variables, we call ->server() with a closure that takes a payload of what is the `$_SERVER`
variable and allows us to edit it.  In this case, we just return the entire array.

Next, when filtering the `$_COOKIE` super global, we call the helper function `all()` of the HandlerFilter.  
This is essentially returns a function that is the same as the closure we wrote.  It's just used to save time.

Now, any exception or error will write to the logger with the entire contents of the `$_SERVER` and the `$_COOKIE` variables.  
Remember, since we didn't add any other filters, items like `$_GET` and `$_POST` will not be logged.

### Filter Functions

Filter functions require a PHP Callable to be passed as their only parameter.  This callable will receive an array of the payload
variable.  The return type should be an array or null.  `NULL` indicates that this portion should not be logged.  You may 
return the entire array unaltered, alter it, or completely replace it (not recommended).  

You may want to alter the values when there are instances that deal with secure values, like a credit card number in a `$_POST`
or database connection variables in your `$_ENV`.

`Handler::session` - access to `$_SESSION`

`Handler::get` - access to `$_GET`

`Handler::post` - access to `$_POST`

`Handler::cookie` - access to `$_COOKIE`

`Handler::environment` - access to `$_ENV`

`Handler::server` - access to `$_SERVER`

`Handler::application` - empty array to add custom application values to

### Built-in Filter Callables

To save time, there are two helper methods that return filter callables.

`HandlerFilter::all` - returns a filter that returns the entire payload unaltered

`HandlerFilter::none` - returns a filter that returns `null`, making sure that the variable is not logged

### Cookbooks

For the following cookbooks, we're assuming that the `$handler` variable is an instance of this library with a valid
logger injected.

**Log `$_SERVER` only if running from web server**

```php
$handler->server(function(array $payload) {
  return php_sapi_name() != 'cli' ? $payload : null;
});
```

**Mask a credit card number in `$_POST` by the key 'cc_num'** 

```php
$handler->post(function(array $payload) {
  if (array_key_exists('cc_num', $payload)) {
    $payload['cc_num'] = str_pad(substr($payload['cc_num'], -4), strlen($payload['cc_num']), '*', STR_PAD_LEFT);
  }
  return $payload;
});
```

**Conditionally do not log `$_SESSION` based on an application choice**

```php
use AaronSaray\PHProblemLogger\Handler as HandlerFilter;

$handler->session(HandlerFilter::all());

if (someFunctionIsTrue()) {
  $handler->session(HandlerFilter::none());
}
```

**Log user information from your session without a complex application / DI solution**

```php
$handler->application(function(array $payload) {
  if (isset($_SESSION['user'])) {
    $payload['user'] = $_SESSION['user'];
  }
  return $payload;
});
```

**Log detailed user information within an application with Dependency Injection**

```php
class MyUserErrorFilter
{
  protected $authenticationProvider;
  
  public function __construct($authenticationProvider)
  { 
    $this->authenticationProvider = $authenticationProvider;
  }
  
  public function __invoke(array $payload)
  {
    if ($this->authenticationProvider->isLoggedIn()) {
       $payload['authenticationInfo'] = $this->authenticationProvider->getAuthenticationInfo();
    }
    return $payload;
  }
}

$handler->application(new MyUserErrorFilter($yourAuthenticationProviderInstance));
```

**Add additional useful information like memory usage**

```php
$handler->application(function(array $payload) {
  $payload['memory_usage'] = memory_get_usage();
  $payload['pid'] = getmypid();
  $payload['resource_usage'] = getrusage(); 
  return $payload;
});
```

**Use PHProblemLogger exception logging tools even in caught exception**

*Keep in mind: if you have your own exception handler defined before PHProblemLogger, this will kick off yours as well.*

```php

try {
  somethingCausesException();
}
catch (\Exception $e) {
  // some custom programming here
  $handler->handleException($e);
}
```

### Troubleshooting

**Session variables are not being logged.**  
Make sure that you are calling `session_start()` somewhere before the error happens.  This library will not attempt to start a session 
just to report on the content of it.

**My own custom exception handler is not firing**  
Make sure that you've made a new instance of `Handler` **after** you've defined your custom exception handler.  If you're using
any other sort of exception handler queueing system, this may not work.

**I am not seeing any of the logs from PHProblemLogger in the output**  
This could be happening for a number of reasons.  First, note that the log level of `ERROR` is being used to log the 
exceptions and errors.  Verify that your log writer is configured to write that log level.  Second, you may want to verify 
that PHProblemHandler's exception handler hasn't been removed.  When you call `set_exception_handler` it returns the previous
exception handler.  Use a debugger to validate that the exception handler is still set.  A quick and dirty solution is to use 
the following code to verify this: `var_dump(set_exception_handler(function(){}));` - which should return a callable that
reflects the PHProblemHandler function of `Handler::handleException`.

## About

There are a lot of systems out there to handle the display of errors - and some more complex solutions (like Zend Server) to
handle gathering the entire environment during an error condition.  However, there is nothing really in-between that - something
that logs errors and the runtime environment - without relying on a larger, enterprise-level solution or third-party.  

I was having some weird errors that I really needed to know more about the environment to troubleshoot, so I decided to add 
something like this to my application.  I then created it as an open source project - hopefully it will help you out!

### Requirements

 - PHP 5.4+
 
### Bugs and Feature Requests

Bugs and feature request are tracked on [GitHub](https://github.com/aaronsaray/phproblemlogger/issues)

Run tests by executing `composer tests` in the root of the project.

### Author

Aaron Saray - <http://aaronsaray.com>

### License

This library is licensed under the MIT License - see the [LICENSE](LICENSE) file for details
