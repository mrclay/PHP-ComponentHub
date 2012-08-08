<?php

spl_autoload_register(require dirname(__DIR__) . '/scripts/autoloader.php');

class MyComponent {
    public function greet() { return "Hello"; }
}

/**
 * @method MyComponent greeter1()
 * @method MyComponent greeter2()
 */
class MyExample {
    protected $hub;
    public function __construct() {
        $this->hub = new ComponentHub\Hub();

        $this->hub->setComponent('greeter1', new MyComponent());

        $this->hub->registerComponentLoader('greeter2', function () {
            return new MyComponent();
        });
    }
    public function __call($name, $args) {
        return $this->hub->getComponent($name, $args);
    }
}


$ex = new MyExample();

echo $ex->greeter1()->greet();

echo $ex->greeter2()->greet();
