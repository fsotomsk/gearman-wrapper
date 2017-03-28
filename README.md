## Gearman worker and client wrapper library

* require php >= 5.6, 7
* php-pecl-gearman
* gearman-job-server

## Usage

Test.php
```php
  class Test
  {
      public function ping($ping)
      {
          return "$ping:pong";
      }
  }
```

test-worker.php (cli)
```php
$t = new \Deep\Gearman\Worker(new Test(), ['localhost']);
$t->run();
```

test-client.php
```php
$t = new \Deep\Gearman\Client(new Test(), ['localhost']);
echo $t->ping('hello');
```
