<?php
function bubbleSort($arr)
{
    $n = count($arr);
    // Looping untuk setiap elemen dalam array
    for ($i = 0; $i < $n - 1; $i++) {
        // Perbandingan antar elemen berturut-turut
        for ($j = 0; $j < $n - $i - 1; $j++) {
            if ($arr[$j] > $arr[$j + 1]) {
                // Tukar elemen jika dalam urutan yang salah
                $temp = $arr[$j];
                $arr[$j] = $arr[$j + 1];
                $arr[$j + 1] = $temp;
            }
        }
    }
    return $arr;
}

// Test
print_r(bubbleSort([1, 3, 2, 9, 5]));  // [1, 2, 3, 5, 9]
print_r(bubbleSort([3, 7, -3, 5, 9, 5])); // [-3, 3, 5, 5, 7, 9]