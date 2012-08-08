<?php

spl_autoload_register(require dirname(__DIR__) . '/scripts/autoloader.php');

class MySeries {
    protected $i = 1;
    public function __construct($initial) { $this->i = (int) $initial; }
    public function getNext() { return $this->i++; }
}

//$ex = new MyExample();
//
//echo $ex->greeter1()->greet();
//
//echo $ex->greeter2()->greet();

$di = new ComponentHub\DiContainer(array(
    'params' => array(
        'comp1:class' => 'MySeries',
        'comp2:arg' => 3,
    ),
    'services' => array(
        'comp1' => function ($di) {
            /* @var ComponentHub\DiContainer $di */
            $class = $di->get('comp1:class');
            return new $class(1);
        },
    ),
));

echo $di->get('comp1')->getNext() . "<br>";

echo $di->get('comp1')->getNext() . "<br>"; // should be identical

$di->setSharedService('comp2', function ($di) {
    /* @var ComponentHub\DiContainer $di */
    return new MySeries($di->get('comp2:arg'));
});

echo $di->get('comp2')->getNext() . "<br>";

echo $di->get('comp2')->getNext() . "<br>"; // should increment