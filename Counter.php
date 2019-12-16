<?php

//ini_set('display_startup_errors', 1);
//ini_set('display_errors', 1);
//error_reporting(-1);

trait Utils
{

    public static function isAssociativeArray($a)
    {
        return is_array($a) && array_diff_key($a, array_keys(array_keys($a)));
    }
    
}

/**
 * Counter class is an informal porting of the Python collections.Counter class
 * https://docs.python.org/3/library/collections.html#collections.Counter
 * 
 * Example usages below, not much tested, use with care
 * In some cases behavior or API slightly differ from the original
 * 
 * Useful as a starting point for TF-IDF
 * 
 * @author https://github.com/n-gram-hub
 * @version $Revision: 0.1.0 $
 * 
 * 
 */
class Counter implements ArrayAccess
{
    use Utils;
    private $counter = [];
    
    /*
    * 
    * @param string, array $mixed
    *
    * @throws TypeError if $mixed is not string or array. Please, God of PHP, invent constructor overloading
    * @throws UnexpectedValueException if array doesn't contain valid elements
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
            if (Utils::isAssociativeArray($mixed)) {

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

            // if $mixed is plain array
            } elseif (is_array($mixed)) {

                // if elements are valid
                if ($this->arrayIsValid($mixed)) {

                    $this->counter = array_count_values($mixed);

                } else {
                    throw new UnexpectedValueException("Each element must be string or integer");
                }
            // if $mixed is string
            } elseif (is_string($mixed)) {
                
                // split
                $this->counter = array_count_values(str_split($mixed));
            }
            // if $mixed is completely another type
            else {
                throw new TypeError("You can pass strings, arrays and associative arrays");
            }
        }
    }
    

    // return counter
    public function getCounter(){
        return $this->counter;
    }    


    // ArrayAccess stuff
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
        return $this->keysAreValid($array);
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

        if (is_string($m) || (is_array($m) && !Utils::isAssociativeArray($m))) {

            $m = array_count_values(is_string($m) ? str_split($m) : $m);

        } elseif (Utils::isAssociativeArray($m) || $m instanceof Counter) {
            
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
