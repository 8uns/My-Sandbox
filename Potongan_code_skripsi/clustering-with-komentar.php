<?php
// Fungsi untuk melakukan Systematic Random Sampling.
// Ini digunakan untuk menentukan titik awal klaster (centroid) secara acak.
function getRandoms($N, $j_cluster) {
    // Inisialisasi array untuk menampung anggota setiap cluster.
    $kelompok1 = array();
    $kelompok2 = array();
    $kelompok3 = array();
    $kelompok4 = array();
    $kelompok5 = array();
    $kelompok6 = array();

    // Menghitung ukuran sampel (n) dan interval (K).
    $n = round($N / $j_cluster);
    $interval = round($N / $n);
    $K = $interval;
    $nilaiAwal = rand(1, $interval); // Menentukan nilai awal acak.

    // Logika untuk Systematic Random Sampling.
    // Kode ini secara berulang mengambil sampel dari data dengan interval yang telah ditentukan.
    // Data yang tidak termasuk sampel akan ditampung di array sementara.
    if ($j_cluster >= 2 && $j_cluster <= 6) {
        $i1 = 1;
        $i2 = 1;
        for ($i = 1; $i <= $N; $i++) {
            if ($i == $nilaiAwal && count($kelompok1) < (int) $n) {
                $kelompok1[$i1] = $i;
                $nilaiAwal += $interval;
                $i1 += 1;
            } else {
                $tampung[$i2] = $i;
                $i2 += 1;
            }
        }
        if ($j_cluster >= 3) {
            $jt = count($tampung); $j=1; $j1=1; $j2=1; $j3=1;
            do { if ($j == $j2 && count($kelompok2) < (int) $n) {$kelompok2[$j1] = $tampung[$j]; $j1 += 1; $j2 += $interval;} else {$tampung2[$j3] = $tampung[$j]; $j3 += 1;} $j += 1; } while ($j <= $jt);
        }
        if ($j_cluster >= 4) {
            $kt = count($tampung2); $k=1; $k1=1; $k2=1; $k3=1;
            do { if ($k == $k2 && count($kelompok3) < (int) $n) {$kelompok3[$k1] = $tampung2[$k]; $k1 += 1; $k2 += $interval;} else {$tampung3[$k3] = $tampung2[$k]; $k3 += 1;} $k += 1; } while ($k <= $kt);
        }
        if ($j_cluster >= 5) {
            $lt = count($tampung3); $l=1; $l1=1; $l2=1; $l3=1;
            do { if ($l == $l2 && count($kelompok4) < (int) $n) {$kelompok4[$l1] = $tampung3[$l]; $l1 += 1; $l2 += $interval;} else {$tampung4[$l3] = $tampung3[$l]; $l3 += 1;} $l += 1; } while ($l <= $lt);
        }
        if ($j_cluster == 6) {
            $mt = count($tampung4); $m=1; $m1=1; $m2=1; $m3=1;
            do { if ($m == $m2 && count($kelompok5) < (int) $n) {$kelompok5[$m1] = $tampung4[$m]; $m1 += 1; $m2 += $interval;} else {$kelompok6[$m3] = $tampung4[$m]; $m3 += 1;} $m += 1; } while ($m <= $mt);
        }
    } else {
        echo "<br>Error : Argumen terakhir pada function 'randoms()' hanya dibatasi 2 - 6 !<br>";
    }

    if ($j_cluster == 2) {$kelompok2 = $tampung;} elseif ($j_cluster == 3) {$kelompok3 = $tampung2;} elseif ($j_cluster == 4) {$kelompok4 = $tampung3;} elseif ($j_cluster == 5) {$kelompok5 = $tampung4;}

    // Mengembalikan nilai-nilai yang telah dihitung.
    return array($K, $N, $n, $kelompok1, $kelompok2, $kelompok3, $kelompok4, $kelompok5, $kelompok6);
}

// Fungsi untuk menghitung titik pusat (centroid) baru.
// Fungsi ini menghitung rata-rata dari semua atribut data yang termasuk dalam satu cluster.
function getCentroids($cluster = false, $count, $query) {
    $centroid1 = $centroid2 = $centroid3 = $centroid4 = $centroid5 = $centroid6 = 0;
    $atribut1='id_k1'; $atribut2='id_k2'; $atribut3='id_k3'; $atribut4='id_k4'; $atribut5='luas_lr'; $atribut6='id_k6';
    $i = 1;
    $j = 1;
    // Iterasi melalui data untuk menjumlahkan nilai atribut.
    while ($row = mysqli_fetch_assoc($query)) {
        if (isset($cluster[$j])) {
            if ($i == $cluster[$j]) {
                $centroid1 += $row[$atribut1];
                $centroid2 += $row[$atribut2];
                $centroid3 += $row[$atribut3];
                $centroid4 += $row[$atribut4];
                $centroid5 += $row[$atribut5];
                $centroid6 += $row[$atribut6];
                $j += 1;
            }
        }
        $i++;
    }
    
    // Menghitung rata-rata (centroid).
    $centroid1 = number_format(1 / $count * $centroid1, 5);
    $centroid2 = number_format(1 / $count * $centroid2, 5);
    $centroid3 = number_format(1 / $count * $centroid3, 5);
    $centroid4 = number_format(1 / $count * $centroid4, 5);
    $centroid5 = number_format(1 / $count * $centroid5, 5);
    $centroid6 = number_format(1 / $count * $centroid6, 5);
    
    return array($centroid1, $centroid2, $centroid3, $centroid4, $centroid5, $centroid6);
}

// Fungsi untuk menghitung jarak antara setiap data dengan centroid menggunakan Manhattan Distance.
function getDistance($query, $centroid1, $centroid2, $centroid3, $centroid4, $centroid5, $centroid6) {
    $atribut[1] = "id_k1"; $atribut[2] = "id_k2"; $atribut[3] = "id_k3"; $atribut[4] = "id_k4"; $atribut[5] = "luas_lr"; $atribut[6] = "id_k6";
    $distance = array();
    $i = 1;
    while ($row = mysqli_fetch_assoc($query)) {
        // Rumus Manhattan Distance: |x1-y1| + |x2-y2| + ...
        $distance[$i] = (abs($row[$atribut[1]] - $centroid1)) + (abs($row[$atribut[2]] - $centroid2)) + (abs($row[$atribut[3]] - $centroid3)) + (abs($row[$atribut[4]] - $centroid4)) + (abs($row[$atribut[5]] - $centroid5)) + (abs($row[$atribut[6]] - $centroid6));
        $i++;
    }
    return $distance;
}

// Fungsi untuk menghitung nilai 'a' dari Silhouette Coefficient.
// 'a' adalah jarak rata-rata sebuah data ke semua data lain di dalam klaster yang sama.
function getA($query, $cluster) {
    $i = 1;
    while ($row = mysqli_fetch_assoc($query)) {
        for ($x = 1; $x <= 6; $x++) {
            if (in_array($row['id_pend'], $cluster[$x])) {
                $getData[$i] = array('id' => $i, 'k1' => $row['id_k1'], 'k2' => $row['id_k2'], 'k3' => $row['id_k3'], 'k4' => $row['id_k4'], 'k5' => $row['luas_lr'], 'k6' => $row['id_k6'], 'cluster_id' => $x);
                break;
            }
        }
        $i++;
    }

    $a = array(); $dibagi = array();
    for ($i = 1; $i <= count($getData); $i++) {
        $a[$i] = 0; $dibagi[$i] = 0;
        for ($k = 1; $k <= 6; $k++) {
            if ($getData[$i]['cluster_id'] == $k) {
                for ($j = 1; $j <= count($getData); $j++) {
                    if ($getData[$j]['cluster_id'] == $k && $getData[$j]['id'] != $getData[$i]['id']) {
                        // Menghitung jarak Euclidean antar data.
                        $a[$i] += sqrt(pow(($getData[$i]['k1'] - $getData[$j]['k1']), 2) + pow(($getData[$i]['k2'] - $getData[$j]['k2']), 2) + pow(($getData[$i]['k3'] - $getData[$j]['k3']), 2) + pow(($getData[$i]['k4'] - $getData[$j]['k4']), 2) + pow(($getData[$i]['k5'] - $getData[$j]['k5']), 2) + pow(($getData[$i]['k6'] - $getData[$j]['k6']), 2));
                        $dibagi[$i] = $dibagi[$i] + 1;
                    }
                }
            }
        }
        if ($dibagi[$i] != 0) { $a[$i] = $a[$i] / $dibagi[$i]; }
    }
    return $a;
}

// Fungsi untuk menghitung nilai 'b' dari Silhouette Coefficient.
// 'b' adalah jarak rata-rata sebuah data ke semua data di klaster tetangga terdekat.
function getB($query, $cluster) {
    $i = 1;
    while ($row = mysqli_fetch_assoc($query)) {
        for ($x = 1; $x <= 6; $x++) {
            if (in_array($row['id_pend'], $cluster[$x])) {
                $getData[$i] = array('k1' => $row['id_k1'], 'k2' => $row['id_k2'], 'k3' => $row['id_k3'], 'k4' => $row['id_k4'], 'k5' => $row['luas_lr'], 'k6' => $row['id_k6'], 'cluster_id' => $x);
                break;
            }
        }
        $i++;
    }
    $minB = array();
    for ($i = 1; $i <= count($getData); $i++) {
        $getValueB = array();
        for ($j = 1; $j <= count($getData); $j++) {
            $dibagi[$j] = 0;
            if ($getData[$i]['cluster_id'] != $getData[$j]['cluster_id']) {
                $tempValueB = 0;
                for ($k = 1; $k <= count($getData); $k++) {
                    if ($getData[$j]['cluster_id'] == $getData[$k]['cluster_id']) {
                        $tempValueB += (sqrt(pow(($getData[$i]['k1'] - $getData[$k]['k1']), 2) + pow(($getData[$i]['k2'] - $getData[$k]['k2']), 2) + pow(($getData[$i]['k3'] - $getData[$k]['k3']), 2) + pow(($getData[$i]['k4'] - $getData[$k]['k4']), 2) + pow(($getData[$i]['k5'] - $getData[$k]['k5']), 2) + pow(($getData[$i]['k6'] - $getData[$k]['k6']), 2)));
                        $dibagi[$j]++;
                    }
                }
                if ($dibagi[$j] != 0) {
                    $getValueB[$j] = $tempValueB / $dibagi[$j];
                }
            }
        }
        if (!empty($getValueB)) {
            $minB[$i] = min($getValueB);
        } else {
            $minB[$i] = 9999; // Nilai besar jika tidak ada klaster tetangga.
        }
    }
    return $minB;
}

// Fungsi untuk menghitung Silhouette Coefficient akhir (S).
// Rumus: S = (b - a) / max(a, b). Nilai S yang mendekati 1 menunjukkan pengelompokan yang baik.
function silhouette($a = 0, $b = 0) {
    $s = array();
    $jmlA = count($a);
    for ($i = 1; $i <= $jmlA; $i++) {
        if ($a[$i] > $b[$i]) {
            $s[$i] = number_format(($b[$i] - $a[$i]) / $a[$i], 5);
        } elseif ($b[$i] > $a[$i]) {
            $s[$i] = number_format(($b[$i] - $a[$i]) / $b[$i], 5);
        } else {
            $s[$i] = 0;
        }
    }
    return $s;
}
?>