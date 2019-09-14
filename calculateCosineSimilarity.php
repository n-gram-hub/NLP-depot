<?php

// https://github.com/n-gram-hub/dotProduct/blob/master/calculateDotProduct.php
require 'calculateDotProduct.php';


function pow_callback($e){
    return pow($e, 2);
}


function range_callback($e){
    return $e >= -1 && $e <= 1 ? 1 : 0;
}


function elementIsOutOfRange($vector){
    return array_product(array_map('range_callback', $vector)) == 0;
}

/**
 *
 * Calculates the cosine similarity of two vectors. 
 *
 * @param array $vector1 The first vector
 * @param array $vector2 The second vector
 * 
 * @author https://github.com/n-gram-hub
 * 
 * @return float
 *
 */
function calculateCosineSimilarity(array $vector1, array $vector2){

    // calculate dot product
    try {
        $dotProduct = calculateDotProduct($vector1, $vector2);
    } catch (TypeError $e) {
        echo "TypeError: " . $e->getMessage();
    } catch (LengthException $e) {
        echo "LengthException: " . $e->getMessage();
    } catch (UnexpectedValueException $e) {
        echo "UnexpectedValueException: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage();
    }
    
    // check if any element falls within the -1/1 range
    if(elementIsOutOfRange($vector1) || elementIsOutOfRange($vector2)){
        throw new RangeException("Numbers must fall between -1.0 and 1.0");
    }
    
    // norms
    $v1n = sqrt(array_sum(array_map('pow_callback', $vector1)));
    $v2n = sqrt(array_sum(array_map('pow_callback', $vector2)));

    return $dotProduct / ($v1n * $v2n);
}


try{
  echo calculateCosineSimilarity([0.1,1.0,0.22,0,1],[0.1,0.1,1,0,1]);
} catch (RangeException $e){
    echo "Exception: " . $e->getMessage();
} catch (ArgumentCountError $e) {
    echo "ArgumentCountError: " . $e->getMessage();
} catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage();
}
