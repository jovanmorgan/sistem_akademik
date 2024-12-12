<?php
function isPrime($n)
{
    // Bilangan kurang dari 2 bukan prima
    if ($n <= 1) {
        return false;
    }

    // Periksa pembagi hingga akar dari n
    for ($i = 2; $i * $i <= $n; $i++) {
        if ($n % $i == 0) {
            return false; // Jika ditemukan pembagi, berarti bukan prima
        }
    }

    return true; // Bilangan prima
}

function findPrimesInArray($arr)
{
    $primes = [];
    foreach ($arr as $num) {
        if (isPrime($num)) {
            $primes[] = $num;
        }
    }
    return $primes;
}

// Test
print_r(findPrimesInArray([1, 3, 5, 7, 11, 15])); // [3, 5, 7, 11]
print_r(findPrimesInArray([8, 10, 12, 14])); // []