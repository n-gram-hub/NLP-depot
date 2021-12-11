<?php

// this is a brute porting of two Python functions by 'mohit kumar 29' and 'AnkitRai01'
// cfr. https://www.geeksforgeeks.org/jaro-and-jaro-winkler-similarity/

// warning: untested

function Jaro(string $s1, string $s2)
{

    if ($s1 === $s2) {
        return 1.0;
    }

    $len1 = strlen($s1);
    $len2 = strlen($s2);

    $max_dist = floor(max($len1, $len2) / 2) - 1;

    $match = 0;

    $hash_s1 = array_fill(0, $len1, 0);
    $hash_s2 = array_fill(0, $len2, 0);

    for ($i = 0; $i < $len1; $i++) {

        for ($j = max(0, $i - $max_dist); $j < min($len2, $i + $max_dist + 1); $j++) {

            if ($s1[$i] == $s2[$j] && $hash_s2[$j] == 0) {
                $hash_s1[$i] = 1;
                $hash_s2[$j] = 1;
                $match += 1;
                break;
            }
        }
    }

    if ($match == 0) {
        return 0.0;
    }

    $t = $point = 0;

    for ($i = 0; $i < $len1; $i++) {

        if ($hash_s1[$i]) {

            while ($hash_s2[$point] == 0) {
                $point += 1;
            }

            if ($s1[$i] != $s2[$point]) {
                $t += 1;
            }

            $point += 1;
        }
    }

    $t = intval($t / 2);

    return ($match / $len1 + $match / $len2 + ($match - $t) / $match) / 3.0;
}

//

function JaroWinkler(string $s1, string $s2)
{

    $jaro_dist = Jaro($s1, $s2);

    if ($jaro_dist > 0.7) {

        $prefix = 0;

        $m = min(strlen($s1), strlen($s2));

        for ($i = 0; $i < $m; $i++) {

            if ($s1[$i] == $s2[$i]) {
                $prefix += 1;
            } else {
                break;
            };
        }

        $prefix = min(4, $prefix);

        $jaro_dist += 0.1 * $prefix * (1 - $jaro_dist);
    }

    return $jaro_dist;
}

echo JaroWinkler("trate", "trace");
