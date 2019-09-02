<?php

// my shared server doesn't support PHP statistic functions (so, no stats_stat_innerproduct)

// check if each element in array is numeric (very improvable)
function arrayIsNumeric($array) {

    foreach($array as $value) {
         if (!(is_numeric($value))) {
              return false;
         } 
    }
    return true;
}

/**
 *
 * Calculates the dot product of two or more vectors. The dot product is the result of multiplying the individual numerical values in two or more vectors
 *
 * @param array $vector1 The first vector
 * @param array $vector2 The second vector
 * @param array ...$moreVectors Other eventual vectors
 * 
 * @throws ArgumentCountError if user passes less than two vectors
 * @throws TypeError if user doesn't pass an array
 * @throws LengthException if the length is not the same for every array OR if array contains less than 2 elements
 * @throws UnexpectedValueException if element in array is not numeric
 * 
 * @author https://github.com/n-gram-hub
 * 
 * @return float
 *
 */
function calculateDotProduct(array $vector1, array $vector2, array ...$moreVectors)
{

    // array comprising the function's argument list
    $v = func_get_args();

    // arguments count (if valid, vectors count)
    $vCount = count($v);

    // tests
    for ($i = 0; $i < $vCount; $i++) {
        
        // check if vectors have equal length
        if (count($v[0]) != count($v[$i])) {
            throw new LengthException("The array length is not the same");
        }

        // if there is at least one element for each array
        if (empty($v[$i])) {
            throw new LengthException("Each array must contain at least 1 element");
        }

        // any element in any array must be numeric
        if (!(arrayIsNumeric($v[$i]))) {
            throw new UnexpectedValueException("Each array element must be numeric");
        }
    }

    // length of a vector
    $singleVectorLength = count($v[0]);

    // initialize scalar
    $scalar = 0;

    // loop "horizontally"
    for ($i = 0; $i < $singleVectorLength; $i++) {

        // add, multiply "vertically"  :-)
        $scalar += array_product(array_column($v,$i));

    }

    // return scalar
    return floatval($scalar);
}

try {
    echo calculateDotProduct([1.01, 2, 3, 0], [1.0101, 2, 3, 0], [1.1212, 2, 3, 0]);
} catch (ArgumentCountError $e) {
    echo "ArgumentCountError: " . $e->getMessage();
} catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage();
} catch (LengthException $e) {
    echo "LengthException: " . $e->getMessage();
} catch (UnexpectedValueException $e) {
    echo "UnexpectedValueException: " . $e->getMessage();
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}
