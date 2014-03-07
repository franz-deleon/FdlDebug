FdlDebug - Alpha
================

FdlDebug is a super simple PHP debugger outputter with a twist. Kinda like var_dump() but more!

### Sneak peak on usage:
```php
\FdlDebug\Front::i()->pr("Hello Yo!"); // output: "Hello Yo!"
```
Yep, thats how simple it is to use. You just grab the `Front::i()` instance and call one of the Debug methods.  
Don't get fooled though it has more on its sleeves! But first, you need to install it.

### Requirements:
  * PHP 5.3
  * XDebug (optional)
  
### Installation:

#### Composer
If your project uses Composer, just add to **composer.json**:
```json
"require": {
    "franz-deleon/fdl-debug": "dev-develop"
}
```
or command line:
```
$> php composer.phar require franz-deleon/fdl-debug
```
#### GitHub
```
$> cd path/to/lib/fdldebug
$> git clone git@github.com:franz-deleon/FdlDebug.git .
```
#### Enabling for procedural projects
If you dont use Composer or use any autoloading, including the *Bootstrapper.php* file will autoload the library for you.
```php
include_once 'path/to/fdldebug/Bootstapper.php';

// call the instance
\FdlDebug\Front::i()->pr('wuzzup'); //outputs: "wuzzup"
```

## Conditions
Conditions as what I call it is the butter of FdlDebug. It basically adds features on how you call your prints or var_dumps via the Front instance.

#### 1. Boolean Condition
The Boolean Condition is a simple way of passing boolean in order to print your data.

For example, only print if the condition evaulates to *true*
```php
use \FdlDebug\Front as Fdbug;

$x = 5;
Fdbug::i()->condBoolean($x === 5)->pr('Yep its 5'); // outputs: Yep its 5
```
How about I want to only print if its even numbers
```php
use \FdlDebug\Front as Fdbug;

for ($x = 1; $x <= 5; ++$x) {
  Fdbug::i()->condBoolean($x % 2 === 0)->pr("$x is even"); 
}
// outputs: 
// 2 is even
// 4 is even
```

#### 2. Loop Range Condition
The Loop Range condition is useful when printing a range of data inside a loop.

For example, only print when the iteration of the loop is the 3rd to the end
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
`loopRange()` also accepts a length parameter if you only want to print certain length or ranges
```php
for ($x = 1; $x <= 5; ++$x) {
  Fdbug::i()->loopRange(2, 2)->pr($x);
}
// outputs:
// 2
// 3
```
