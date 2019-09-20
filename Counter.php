<?php

function isAssociativeArray($a)
{
    return is_array($a) && array_diff_key($a, array_keys(array_keys($a)));
}

/**
 * Counter class is an informal porting of the Python collections.Counter class
 * https://docs.python.org/3/library/collections.html#collections.Counter
 * 
 * Example usages below, not much tested, use with care
 * In same cases behavior or API can be different from the original
 * 
 * Useful for basic frequency dictionary usage
 * 
 * @author https://github.com/n-gram-hub
 * @version $Revision: 0.1.0 $
 * 
 * 
 */
class Counter implements ArrayAccess
{

    private $counter = [];
    
    /*
    * 
    * @param string, array $mixed
    *
    * @throws TypeError if $mixed is not string or array. Please, God of PHP, invent constructor overloading
    * @throws UnexpectedValueException if array doens't contain valid elements
    * 
    */
    public function __construct($mixed = [])
    {

        $args = func_get_args();
        $argsLength = count($args);

        // no argument provided, return empty counter
        if ($argsLength == 0) {
            return $this->counter;
        }

        if ($argsLength > 0) {

            // if user passed associative array
            if (isAssociativeArray($mixed)) {

                // if keys are strings or ints && values are ints
                if ($this->keysAreValid(array_keys($mixed)) && $this->valuesAreValid(array_values($mixed))) {

                    $a = [];
                    $negativeCounts = [];

                    foreach ($mixed as $k => $value) {
                        
                        if($value>0){

                            for ($i = 0; $i < $value; $i++) {
                                $a[] = $k;
                            }

                        }else{
                            
                            $negativeCounts[$k]=$value;
                            
                        }

                    }
                    
                    // for some reason Python's Counter lets you add negative counts
                    // since array_count_values can't do it, we need to merge them
                    $this->counter = array_merge(array_count_values($a),$negativeCounts);

                } else {
                    throw new UnexpectedValueException("Each key must be string or integer, each value must be integer");
                }

            // if is plain array
            } elseif (is_array($mixed)) {

                // if elements are valid
                if ($this->arrayIsValid($mixed)) {

                    $this->counter = array_count_values($mixed);

                } else {
                    throw new UnexpectedValueException("Each element must be string or integer");
                }
            // if is string
            } elseif (is_string($mixed)) {
                
                // split
                $this->counter = array_count_values(str_split($mixed));
            }
            // is completely other type
            else {
                throw new TypeError("You can pass strings, arrays and associative arrays");
            }
        }
    }
    

    // return counter
    public function getCounter(){
        return $this->counter;
    }    


    // array access stuff
    public function offsetSet($offset, $value)
    {
        
        if ((is_string($offset)||is_numeric($offset)) && is_int($value)) {

            $this->counter[$offset] = $value;
        
        } 
    }


    public function offsetExists($offset)
    {
        return isset($this->counter[$offset]);
    }


    public function offsetUnset($offset)
    {
        unset($this->counter[$offset]);
    }


    public function offsetGet($offset)
    {
        return isset($this->counter[$offset]) ? $this->counter[$offset] : 0;
    }


    public function printCounter()
    {
        print_r($this->counter);
    }
    

    // callbacks
    private function validElement_callback($n)
    {
        return is_int($n) || is_string($n);
    }


    private function validValue_callback($n)
    {
        return is_int($n);
    }


    private function elementToKeyValue_callback($n)
    {
        return $n = 1;
    }


    private function keysAreValid($array)
    {
        return array_product(array_map(array($this, 'validElement_callback'), $array)) == 1;
    }


    private function valuesAreValid($array)
    {
        return array_product(array_map(array($this, 'validValue_callback'), $array)) == 1;
    }


    private function arrayIsValid($array)
    {
        return array_product(array_map(array($this, 'validElement_callback'), $array)) == 1;
    }
    

    // Python's Counter most_common
    public function mostCommon(int $n = 1)
    {

        $counterLength = count($this->counter);

        if ($counterLength > 0) {

            if ($n < 1 || $n > $counterLength) {
                $n = $counterLength;
            }

            arsort($this->counter);

            return array_slice($this->counter, 0, $n);
        }

        return $this->counter;
    }
    

    // validation of mixed type for subtract and update methods
    // $m can be string, array of strings/ints, associative arrays of counts and Counters
    private function validateMixedType($m){

        if (is_string($m) || (is_array($m) && !isAssociativeArray($m))) {

            $m = array_count_values(is_string($m) ? str_split($m) : $m);

        } elseif (isAssociativeArray($m) || $m instanceof Counter) {
            
            $m = $m instanceof Counter ? $m->getCounter() : $m;

            if (!$this->valuesAreValid(array_values($m))) {
                throw new UnexpectedValueException("Each value must be integer");
            }

        } else {

            throw new TypeError("You can pass strings, arrays and associative arrays");

        }

        if (!$this->keysAreValid(array_keys($m))) {
            throw new UnexpectedValueException("Each key must be string or integer");
        }

        return $m;
    }


    // Python's Counter subtract
    public function subtract($mixedType)
    {

        try{
            $assoc = $this->validateMixedType($mixedType);
        }
        catch (TypeError $e) {
            echo "TypeError: " . $e->getMessage();
        }  
        catch (UnexpectedValueException $e) {
            echo "UnexpectedValueException: " . $e->getMessage();
        }

        foreach ($assoc as $k => $v) {
            
            if (isset($this->counter[$k])) {
                
                $this->counter[$k] -= $v;
               
            }else{
                $this->counter[$k] = 0-$v;
            }
        }

        return $this->counter;
    }


    // Python's counter update
    public function update($mixedType)
    {
        
        try{
            $assoc = $this->validateMixedType($mixedType);
        }
        catch (TypeError $e) {
            echo "TypeError: " . $e->getMessage();
        }  
        catch (UnexpectedValueException $e) {
            echo "UnexpectedValueException: " . $e->getMessage();
        }

        foreach ($assoc as $k => $v) {

            if (isset($this->counter[$k])) {
                $this->counter[$k] += $v;
            } else {
                $this->counter[$k] = $v;
            }
        }

        return $this->counter;
    }


    // same as Python Counter list(elements())
    public function listElements()
    {

        $elements = [];

        foreach ($this->counter as $k => $v) {

            if ($v > 0) {

                for ($i = 0; $i < $v; $i++) {
                    $elements[] = $k;
                }
            }
        }

        return $elements;
    }


    // not in the original Counter API, maybe useful
    public function clear(){
       $this->counter = [];
    }


    public function sum(){
        return array_sum($this->counter);
    }

}


// usage examples
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

$c->printCounter(); // Array ( [g] => 1 [a] => 3 [l] => 2 [h] => 1 [d] => 1 )



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



// example 4, count of a missing element is 0, setting a count to zero does not remove an element from a counter

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
$c->printCounter();



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

print_r($c->listElements()); // Array ( [0] => a [1] => a [2] => a [3] => a [4] => b [5] => b )



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

print_r($c->mostCommon(3));   // Array ( [a] => 5 [b] => 2 [r] => 2 )



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



//example 8, update

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
