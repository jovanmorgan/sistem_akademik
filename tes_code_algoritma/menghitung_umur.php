<?php
function calculateAgeAndLeapYears($birthdate)
{
    $birth = new DateTime($birthdate);
    $now = new DateTime();
    $diff = $now->diff($birth); // Menghitung umur dalam tahun, bulan, dan hari

    // Menghitung tahun kabisat
    $leapYears = 0;
    for ($year = $birth->format('Y'); $year <= $now->format('Y'); $year++) {
        if (($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0)) {
            $leapYears++;
        }
    }

    return [
        'age' => $diff->y . ' tahun, ' . $diff->m . ' bulan, ' . $diff->d . ' hari',
        'leap_years' => $leapYears
    ];
}

// Test
$birthdate = '1990-12-08';  // Format: yyyy-mm-dd
$ageInfo = calculateAgeAndLeapYears($birthdate);
echo "Umur: " . $ageInfo['age'] . "\n"; // Contoh: Umur: 33 tahun, 0 bulan, 6 hari
echo "Jumlah tahun kabisat: " . $ageInfo['leap_years'] . "\n";
