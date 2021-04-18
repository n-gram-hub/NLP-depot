<?php

require 'Counter.php';
require 'calculateCosineSimilarity.php';

// sentences were very lazily taken from https://www.machinelearningplus.com/nlp/cosine-similarity/
// this is just a simple test, corpora should be way bigger
$trumpDoc = strtolower("Mr. Trump became president after winning the political election. Though he lost the support of some republican friends, Trump is friends with President Putin");
$electionDoc = strtolower("President Trump says Putin had no political interference is the election outcome. He says it was a witchhunt by political parties. He claimed President Putin is a friend who had nothing to do with the election");
$putinDoc = strtolower("Post elections, Vladimir Putin became President of Russia. President Putin had served as the Prime Minister earlier in his political career");

// stopwords manually removed, multiple occurrences retyped on purpose
$corpus = [
    ['election', 'friends', 'friends', 'lost', 'mr', 'political', 'president', 'president', 'putin', 'republican', 'support', 'trump', 'trump', 'winning'],
    ['claimed', 'election', 'election', 'friend', 'interference', 'outcome', 'parties', 'political', 'political', 'president', 'president', 'putin', 'putin', 'says', 'says', 'trump', 'witchhunt'],
    ['career', 'earlier', 'elections', 'minister', 'political', 'post', 'president', 'president', 'prime', 'putin', 'putin', 'russia', 'served', 'vladimir']
];

// manual IDF
$idf = array('career' => log(3/1), 'claimed' => log(3/1), 'earlier' => log(3/1), 'election' => log(3/2), 'elections' => log(3/1), 'friend' => log(3/1), 'friends' => log(3/1), 'interference' => log(3/1), 'lost' => log(3/1), 'minister' => log(3/1), 'mr' => log(3/1), 'outcome' => log(3/1), 'parties' => log(3/1), 'political' => log(3/3), 'post' => log(3/1), 'president' => log(3/3), 'prime' => log(3/1), 'putin' => log(3/3), 'republican' => log(3/1), 'russia' => log(3/1), 'says' => log(3/1), 'served' => log(3/1), 'support' => log(3/1), 'trump' => log(3/2), 'vladimir' => log(3/1), 'winning' => log(3/1), 'witchhunt' => log(3/1));

// create instances of Counter
try {
    $c0 = new Counter($corpus[0]);
    $c1 = new Counter($corpus[1]);
    $c2 = new Counter($corpus[2]);
    $counters = [$c0, $c1, $c2];
} catch (TypeError $e) {
    echo "TypeError: " . $e->getMessage();
} catch (UnexpectedValueException $e) {
    echo "UnexpectedValueException: " . $e->getMessage();
}

$zero_vector = array('career' => 0, 'claimed' => 0, 'earlier' => 0, 'election' => 0, 'elections' => 0, 'friend' => 0, 'friends' => 0, 'interference' => 0, 'lost' => 0, 'minister' => 0, 'mr' => 0, 'outcome' => 0, 'parties' => 0, 'political' => 0, 'post' => 0, 'president' => 0, 'prime' => 0, 'putin' => 0, 'republican' => 0, 'russia' => 0, 'says' => 0, 'served' => 0, 'support' => 0, 'trump' => 0, 'vladimir' => 0, 'winning' => 0, 'witchhunt' => 0);

$doc_vectors = [];

for ($i=0; $i<count($counters); $i++){

    $z = $zero_vector;

    $counter = $counters[$i]->getCounter();
    $counterLen = count($counter);

    foreach ($counter as $key => $value){
        $tf = $value / $counterLen;
        $z[$key] = $tf * $idf[$key];
    }

    $doc_vectors[] = array_values($z);
    
}

echo "<pre>";
print_r($doc_vectors);
echo "</pre>";

// trump/trump=1
//$tt = calculateCosineSimilarity($doc_vectors[0], $doc_vectors[0]);
// trump/election
$te = calculateCosineSimilarity($doc_vectors[0], $doc_vectors[1]);
// trump/putin
$tp = calculateCosineSimilarity($doc_vectors[0], $doc_vectors[2]);
// election/election=1
//$ee = calculateCosineSimilarity($doc_vectors[1], $doc_vectors[1]);
// election/putin
$ep = calculateCosineSimilarity($doc_vectors[1], $doc_vectors[2]);
// putin/putin=1
//$pp = calculateCosineSimilarity($doc_vectors[2], $doc_vectors[2]);

echo "<pre>";
//print("Similarity 1st - 1st doc: ".$tt.PHP_EOL);
print("Similarity 1st - 2nd doc: ".$te.PHP_EOL);
print("Similarity 1st - 3rd doc: ".$tp.PHP_EOL);
//print("Similarity 2nd - 2nd doc: ".$ee.PHP_EOL);
print("Similarity 2nd - 3rd doc: ".$ep.PHP_EOL);
//print("Similarity 3rd - 3rd doc: ".$pp.PHP_EOL);
echo "</pre>";

?>
