# AyDataFactory
An open source array processing engine, it is chainable and process your array in single line instead of using a number of functions to process your array.
## How good is it?
- No more undefined variable warning, return null as default
- No need a number functions to process your data, easy to maintenance
- Support plugin, you can process your data by different data type plugin
- Alternative of Array, it is an object that you can bind to any closure
- Expandable, High compatibility
- Shorten Code, Longer Life

## How to use?
```php
$dataset = new AyDataFactory($anyArray);
$dataset['hello'] = 'world'; // Define new value
$dataset('hello')->toupper(); // Make it Uppercase!
echo $dataset('hello')->get(function() {
    return 'HELLO ' . $this->get() . '!'; // print: HELLO WORLD!
});
```
