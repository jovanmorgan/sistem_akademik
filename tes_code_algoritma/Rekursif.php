<?php
function reverseString($str)
{
    // Basis Rekursif: jika string kosong, kembalikan string kosong
    if (strlen($str) == 0) {
        return $str;
    } else {
        // Ambil karakter pertama dan tambahkan hasil balik dari sisa string
        return reverseString(substr($str, 1)) . $str[0];
    }
}

// Test
echo reverseString('INDONESIA') . "\n"; // AISENODNI
echo reverseString('LARAVEL') . "\n";   // LEVARAL