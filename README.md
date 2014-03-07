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

#### 1. Boolean Condition
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

#### 2. Loop Range Condition
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

#### 3. Loop From Condition
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
The Loop From condition internally uses output buffering to collect data and calculate the condition. Therefore, you need to call `loopFromFlush()` at the end of the loop in order to print the data.

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
The `loopFrom(string $expression)` accepts expression type statements so these type of statements are valid:  
"*first*", "*beginning*", "*start*", "*3rd from start*", "*4th from last*", "*5th from end*", "*end*", "*last*", ...
I hope you get the groove :facepunch:

You can also pass a length variable just like `loopRange($expression [, int $length])`
```php
for ($x = 1; $x <= 10; ++$x) {
    Fdbug::i()->loopFrom('4th from last', 2)->pr($x);
}
Fdbug::i()->loopFromFlush();
// outputs:
// 7
// 8
```
You can also use multiple `loopFrom` conditions (also true for other conditions) while using nested loops.
```php
for ($i = 1; $i <= 5; $i++) {
    $this->Front->loopFrom('3rd from end', 1)->pr("3rd-from-end:" . $i);
    for ($x = 1; $x <= 5; $x++) {
        $this->Front->loopFrom('2nd from start', 1)->pr("2nd-from-start:" . $x);
    }
}
$this->Front->loopFromFlush(); // now flush everything!
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

todo
