[![Build Status](https://api.travis-ci.org/DeSmart/laravel-layout.png)](https://travis-ci.org/DeSmart/laravel-layout)

# Installation
Add `desmart\laravel-layout` as a requirement to composer.json:

```json
{
  "require": {
    "desmart/laravel-layout": "1.2.*"
  }
}
```

Update your packages with `composer update` or install with `composer install`.

In *app/config/app.php* add:
* `'DeSmart\Layout\LayoutServiceProvider',` to providers 
* `'Layout'          => 'DeSmart\Layout\Facades\Layout',` to aliases.

# Overview

This package provides `DeSmart\Layout\Controller` class which works like normal page controller.  
However it can be used to describe complete page structure.

To do this simply define `$layout` which is basic page template, and `$structure` which is an array with section block definition. 

Section block is a list of callbacks (let's call them *actions*) which will be run and put in a defined place in template.

## Sample page template
```html
<!DOCTYPE html>
<html>
  <head></head>
  <body>
    <div class="container">
      <div class="main">{{ $main }}</div>
      <div class="right">{{ $right }}</div>
    </div>
  </body>
</html>
```

## Sample controller
```php
<?php
class SampleController extends \DeSmart\Layout\Controller {

  protected $layout = 'layouts.default';
  
  protected $structure = array(
    'main' => array(
      'FancyBanner@show',
      'TopStories@show',
    ),
    'right' => array(
      'Menu@showTopProducts',
    ),
  );
  
  public function showProducts() {
    $this->structure['main'] = array(
      'Products@showAll',
    );
    
    return $this->execute();
  }
  
  public function showOne() {
    $this->changeLayout('layouts.product');
    $this->structure['main'] = array(
      'Products@showOne',
    );
    
    return $this->execute();
  }

}
```

## Sample route
```php
<?php
Route::get('/', 'SampleController@execute');
Route::get('/products', 'SampleController@showProducts');
Route::get('/products/{product_id}', 'SampleController@showOne');
```

## Actions
Each action is a callback string which will be called during `DeSmart\Layout\Controller@execute` call.  
Every action can get params defined in route, just define them as function arguments (`public function showOne($product_id) {}`).

# Layout facade

This package provides `Layout` facade with method `dispatch`. 
It can be used to execute action directly in template.

```php
<header>{{ Layout::dispatch('HomeController@head') }}</header>
```

`dispatch()` can take array argument with named callback arguments:

```php
class FancyController {

  public function test($name, $title = 'sir. ') {}
  
}

<header> {{ Layout::dispatch('FancyController@test', array('name' => 'Hans')) }} </header>
```

Notice, that it takes care with default arguments.

# Warning

This package is provided *as is*. For now it's only a concept and the whole idea can change. 

Just treat it as early alpha version.
