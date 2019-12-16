<?php

/*Counter usage examples*/

// example 1, counter with arrayaccess

try{
    $c = new Counter();
}
catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage();
}  
catch (UnexpectedValueException $e) {
    echo "UnexpectedValueException: " . $e->getMessage();
}

$tokens = ['red', 'blue', 'red', 'green', 'blue', 'blue'];
$tokensCount = count($tokens);
for($i=0; $i<$tokensCount; $i++){
    $c[$tokens[$i]] += 1;
}

$c->printCounter(); // Array ( [red] => 2 [blue] => 3 [green] => 1 )


// example 2, counter initialized by string (converts the string to an array of character counts)

try{
    $c = new Counter("gallahad");
}
catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage();
}  
catch (UnexpectedValueException $e) {
    echo "UnexpectedValueException: " . $e->getMessage();
}
catch (Exception $e){
    echo $e->getMessage();
}

$c->printCounter(); // Array ( [g] => 1 [a] => 3 [l] => 2 [h] => 1 [d] => 1 )*/



// example 3, counter initialized by associative array

try{
    $c = new Counter(['red'=>4, 'blue'=>2]);
    //$c = new Counter(array('red'=>4, 'blue'=>array())); // UnexpectedValueException
    //$c = new Counter(true); // TypeError
}
catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage();
}  
catch (UnexpectedValueException $e) {
    echo "UnexpectedValueException: " . $e->getMessage();
}
catch (Exception $e){
    echo $e->getMessage();
}

$c->printCounter(); // Array ( [red] => 4 [blue] => 2 )



// example 4, counter inizialized by array. Count of a missing element is 0, setting a count to zero does not remove an element from a counter

try{
    $c = new Counter(['eggs', 'ham']);
}
catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage();
}  
catch (UnexpectedValueException $e) {
    echo "UnexpectedValueException: " . $e->getMessage();
}
catch (Exception $e){
    echo $e->getMessage();
}

print($c['bacon']); // 0
//$c['ham'] = 0;  
$c->printCounter(); // ( [eggs] => 1 [ham] => 0 )
// use unset to remove element
// unset($c['ham']);
//$c->printCounter();



// example 5, listElements (same as Python Counter list(elements()))

try{
    $c = new Counter(['a'=>4, 'b'=>2, 'c'=>0, 'd'=>-2]);
}
catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage();
}  
catch (UnexpectedValueException $e) {
    echo "UnexpectedValueException: " . $e->getMessage();
}
catch (Exception $e){
    echo $e->getMessage();
}

print_r($c->listElements()); // Array ( [0] => a [1] => a [2] => a [3] => a [4] => b [5] => b )*/



// example 6, mostCommon

try{
    $c = new Counter('abracadabra');
}
catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage();
}  
catch (UnexpectedValueException $e) {
    echo "UnexpectedValueException: " . $e->getMessage();
}
catch (Exception $e){
    echo $e->getMessage();
}

print_r($c->mostCommon(3));   // Array ( [a] => 5 [b] => 2 [r] => 2 )*/



// example 7, subtract (for some strange reason, you can have negative counts)

try{
    $c = new Counter(['a'=>4,'b'=>2,'c'=>0,'d'=>-2]);
}
catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage();
}  
catch (UnexpectedValueException $e) {
    echo "UnexpectedValueException: " . $e->getMessage();
}
catch (Exception $e){
    echo $e->getMessage();
}

$d = ['a'=>1,'b'=>2,'c'=>3,'d'=>4];   // Array ( [a] => 3 [b] => 0 [c] => -3 [d] => -6 )
//$d = ['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>10];   // Array ( [a] => 3 [b] => 0 [c] => -3 [d] => -6 [e] => -10)
//$d = ['d','a','b'];   // Array ( [a] => 3 [b] => 1 [c] => 0 [d] => -3 )
//$d = 'd';   // Array ( [a] => 4 [b] => 2 [c] => 0 [d] => -3 )
//$d = new Counter(['a'=>1,'b'=>1,'c'=>1,'d'=>1]);   // Array ( [a] => 3 [b] => 1 [c] => -1 [d] => -3 )
//$d = [];   // Array ( [a] => 4 [b] => 2 [c] => 0 [d] => -2 ) 
$c->subtract($d);
$c->printCounter();



// example 8, update

try{
    $c = new Counter(['a'=>4,'b'=>2,'c'=>0,'d'=>-2]);
}
catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage();
}  
catch (UnexpectedValueException $e) {
    echo "UnexpectedValueException: " . $e->getMessage();
}
catch (Exception $e){
    echo $e->getMessage();
}

$d = ['a'=>1,'b'=>1,'c'=>1,'d'=>1];   // Array ( [a] => 5 [b] => 3 [c] => 1 [d] => -1 ) 
//$d = ['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>10];   // Array ( [a] => 5 [b] => 4 [c] => 3 [d] => 2 [e] => 10 ) 
//$d = ['d','a','b'];   // Array ( [a] => 5 [b] => 3 [c] => 0 [d] => -1 ) 
//$d = 'd';   // Array ( [a] => 4 [b] => 2 [c] => 0 [d] => -1 )
//$d = new Counter(['a'=>1,'b'=>1,'c'=>1,'d'=>1]);   // Array ( [a] => 5 [b] => 3 [c] => 1 [d] => -1 )
//$d = [];   // Array ( [a] => 4 [b] => 2 [c] => 0 [d] => -2 ) 
$c->update($d);
$c->printCounter();

?>
