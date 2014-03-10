FdlDebug
================
[![Build Status](https://travis-ci.org/franz-deleon/FdlDebug.png?branch=develop)](https://travis-ci.org/franz-deleon/FdlDebug)

FdlDebug is a super simple PHP debugger outputter with a twist. Kinda like var_dump() but more!

#### Sneak peak on usage:
```php
\FdlDebug\Front::i()->pr("Hello Yo!"); // output: "Hello Yo!"
```
Yep, thats how simple it is to use! You just grab the `Front::i()` instance and call one of the [Debug Methods](#debug-methods).  
Don't get fooled though it has more trick on its sleeves! :relieved:

But first, you need to install it.

## Requirements:
  * PHP 5.3
  * XDebug (optional)
  
## Installation:

#### Composer
If your project uses Composer, just add to **composer.json**:
```json
"require": {
    "franz-deleon/fdl-debug": "dev-develop"
}
```
or add using command line:
```sh
$> php composer.phar require franz-deleon/fdl-debug
```
#### GitHub
No Composer, no problemo. Use git.
```sh
$> cd path/to/lib/fdldebug
$> git clone git@github.com:franz-deleon/FdlDebug.git .
```
#### Enabling for procedural projects
If you dont use Composer or use any autoloading, including the **Bootstrapper.php** file will autoload the library for you.
```php
include_once 'path/to/fdldebug/Bootstrapper.php';

// call the instance
\FdlDebug\Front::i()->pr('wuzzup'); //outputs: "wuzzup"
```

## Conditions:
Conditions as what I call it is the butter of FdlDebug. It basically adds features on how you call your prints or var_dumps via the Front instance.

#### 1. Boolean Condition - condBoolean(bool $booleanExpression)
The Boolean Condition is a simple way of passing boolean expression to determine if your data should be printed.

For example, only print if the condition evaulates to *true*:
```php
use \FdlDebug\Front as Fdbug;

$x = rand(1, 10); // assume $x is 5
Fdbug::i()->condBoolean($x === 5)->pr('Yep its 5'); // outputs: Yep its 5
```
How about if I only want to print if the loop iteration is of even numbers.
```php
for ($x = 1; $x <= 5; ++$x) {
  Fdbug::i()->condBoolean($x % 2 === 0)->pr("$x is even"); 
}
// outputs: 
// 2 is even
// 4 is even
```

#### 2. Loop Range Condition - loopRange(int $offsetStart [, int $length])
The Loop Range condition is useful when printing a range of data inside a loop.

For example, only print when the iteration of the loop is the 3rd upto the end.
```php
use \FdlDebug\Front as Fdbug;

for ($x = 1; $x <= 5; ++$x) {
  Fdbug::i()->loopRange(3)->pr($x);
}
// outputs:
// 3
// 4
// 5
```
`loopRange($range [, int $length])` also accepts a length parameter if you only want to print certain length of ranges
```php
for ($x = 1; $x <= 5; ++$x) {
  Fdbug::i()->loopRange(2, 2)->pr($x);
}
// outputs:
// 2
// 3
```

#### 3. Loop From Condition - loopFrom(string $expression [, int $length])
The Loop From condition is a pretty dynamic condition designed if you dont know the count of your loop iterations.

For example, you want to print the end iteration of a mysql resource.
```php
use \FdlDebug\Front as Fdbug;

// asume the end outputs "123"
while ($row = mysql_fetch_assoc()) {
   Fdbug::i()->loopFrom('end')->pr($row['col']);
}
Fdbug::i()->loopFromFlush(); // you need to call loopFromFlush() at the end of the loop
// outputs:
// 123
```
When using `loopFrom()`, you need to call `loopFromFlush()` at the end of the loop in order to print the data.

How about if you only want to print the 3rd iteration from the 'end' of the loop?
```php
for ($x = 1; $x <= 10; ++$x) { // for simplicity, i am using a for loop
    Fdbug::i()->loopFrom('3rd from end')->pr($x);
}
Fdbug::i()->loopFromFlush();
// outputs:
// 8
// 9
// 10
```
Lets print the middle of the loop.
```php
for ($x = 1; $x <= 5; ++$x) {
    Fdbug::i()->loopFrom('middle', 1)->pr($x);
}
Fdbug::i()->loopFromFlush();
// outputs:
// 3
```
How about 2 iterations before the median/middle of 5?
```php
for ($x = 1; $x <= 5; ++$x) {
    Fdbug::i()->loopFrom('2 before median', 1)->pr($x);
}
Fdbug::i()->loopFromFlush();
// outputs:
// 1
```
The `loopFrom(string $expression)` accepts expression type statements so these type of statements are valid:  
"*first*", "*beginning*", "*start*", "*middle*", "median", "2 before middle", "2 after median",  
"*3rd from start*", "*4th from last*", "*5th from end*", "*end*", "*last*", ...  
  
I hope you get the groove :facepunch:

As you may have noticed, you can also pass a length variable to `loopFrom(strin $expression [, int $length])`
```php
for ($x = 1; $x <= 10; ++$x) {
    Fdbug::i()->loopFrom('4th from last', 2)->pr($x);
}
Fdbug::i()->loopFromFlush();
// outputs:
// 7
// 8
```
You can also use multiple `loopFrom` conditions (also true for other conditions) while in nested loops.
```php
$fdbug = Fdbug::i();
for ($i = 1; $i <= 5; $i++) {
    $fdbug->loopFrom('3rd from end', 1)->pr("3rd-from-end:" . $i);
    for ($x = 1; $x <= 5; $x++) {
        $fdbug->loopFrom('2nd from start', 1)->pr("2nd-from-start:" . $x);
    }
}
$fdbug->loopFromFlush(); // now flush everything!
// outputs:
// 3rd-from-end:3
// 2nd-from-start:2
```
#### Session Instance Condition
In development

#### Chaining Conditions
You can chain conditions if you want.
```php
use \FdlDebug\Front as Fdbug;

for ($x = 1; $x <= 10; ++$x) {
    Fdbug::i()
        ->condBoolean($x % 2 === 0)
        ->loopRange(3, 4)
        ->pr($x)
    ;
}
// outputs:
// 4
// 6
```

### Debug Methods

  * **pr(mixed $value)** - alias for printNow()
  * **printNow(mixed $value)** - prints something by passing the argument `$value` to the Writer
  * **printGlobal(string $globalType = null)** - prints data from php's global variables

  ```php
    \\ 'SERVER', 'GET', 'POST', 'FILES', 'REQUEST', 'SESSION', 'ENV', 'COOKIE'
    \FdlDebug\Front::i()->printGlobal('get');
    /** outputs
    outputs:
      array (size=28)
        'APPLICATION_ENV' => string 'local' (length=5)
        'WEB_ENV' => string 'local' (length=5)
        [...]
    */
  ```
  * **printBackTrace(void)** - prints a php back trace using the Writer
  * **printFiles(void)** - prints a file trace using the Writer

### XDebug Methods (extension)

  * **printXdebugTracedVar(string $search, bool $showVendor)** - prints a trace of the target variable `$search`. This method makes use of XDebug's tracing functionality by looking at `XDEBUG_TRACE=1`  

    Example output:
       
       ```php
       // in Bootstrap.php line 20 of some mvc framework, pretend below is written:
       $hello = "hello world";

       // Now pretend you are in XController.php of some mvc framework
       \FdlDebug\Front::i()->printXdebugTracedVar('hello');
       /** outputs:
       array (size=1)
         1 => 
            array (size=4)
              'file' => string '/someframework/Bootstrap.php' (length=54)
              'line' => string '20' (length=2)
              'var($hello) assignment' => 
                array (size=1)
                  0 => string '$hello = 'hello' (length=15)
              'initialization' => string '$hello = 'hello world'' (length=22)
       */
       ```

    *Please remember that you need to enable XDEBUG_TRACE. You can use some XDebug browser extensions to enable it or by passing XDEBUG_TRACE to `http://domain.com/?XDEBUG_TRACE=1`
