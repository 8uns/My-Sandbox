<?php
function getRandoms($N/*jumlah data*/, $j_cluster/*jumlah cluster*/)
{
    $kelompok1 = array();
    $kelompok2 = array();
    $kelompok3 = array();
    $kelompok4 = array();
    $kelompok5 = array();
    $kelompok6 = array();
    $n = round($N / $j_cluster);
    $interval = round($N / $n);
    $K = $interval;
    $nilaiAwal = rand(1, $interval);
    if (
        $j_cluster == 2 || $j_cluster == 3 || $j_cluster == 4 ||
        $j_cluster == 5 || $j_cluster == 6
    ) {
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
    }
    if (
        $j_cluster == 3 || $j_cluster == 4 ||
        $j_cluster == 5 || $j_cluster == 6
    ) {
        $jt = count($tampung);
        $j = 1;
        $j1 = 1;
        $j2 = 1;
        $j3 = 1;
        do {
            if ($j == $j2 && count($kelompok2) < (int) $n) {
                $kelompok2[$j1] = $tampung[$j];
                $j1 += 1;
                $j2 += $interval;
            } else {
                $tampung2[$j3] = $tampung[$j];
                $j3 += 1;
            }
            $j += 1;
        } while ($j <= $jt);
    }
    if ($j_cluster == 4 || $j_cluster == 5 || $j_cluster == 6) {
        $kt = count($tampung2);
        $k = 1;
        $k1 = 1;
        $k2 = 1;
        $k3 = 1;
        do {
            if ($k == $k2 && count($kelompok3) < (int) $n) {
                $kelompok3[$k1] = $tampung2[$k];
                $k1 += 1;
                $k2 += $interval;
            } else {
                $tampung3[$k3] = $tampung2[$k];
                $k3 += 1;
            }
            $k += 1;
        } while ($k <= $kt);
    }
    if ($j_cluster == 5 || $j_cluster == 6) {
        $lt = count($tampung3);
        $l = 1;
        $l1 = 1;
        $l2 = 1;
        $l3 = 1;
        do {
            if ($l == $l2 && count($kelompok4) < (int) $n) {
                $kelompok4[$l1] = $tampung3[$l];
                $l1 += 1;
                $l2 += $interval;
            } else {
                $tampung4[$l3] = $tampung3[$l];
                $l3 += 1;
            }
            $l += 1;
        } while ($l <= $lt);
    }
    if ($j_cluster == 6) {
        $mt = count($tampung4);
        $m = 1;
        $m1 = 1;
        $m2 = 1;
        $m3 = 1;
        do {
            if ($m == $m2 && count($kelompok5) < (int) $n) {
                $kelompok5[$m1] = $tampung4[$m];
                $m1 += 1;
                $m2 += $interval;
            } else {
                $kelompok6[$m3] = $tampung4[$m];
                $m3 += 1;
            }
            $m += 1;
        } while ($m <= $mt);
    }
    if ($j_cluster < 2 || $j_cluster > 6) {
        echo "<br>Error : Argumen terakhir pada function 'randoms()' hanya dibatasi 2 - 6 !<br>";
    }
    if ($j_cluster == 2) {
        $kelompok2 = $tampung;
    } elseif ($j_cluster == 3) {
        $kelompok3 = $tampung2;
    } elseif ($j_cluster == 4) {
        $kelompok4 = $tampung3;
    } elseif ($j_cluster == 5) {
        $kelompok5 = $tampung4;
    }
    $K = $K;
    $N = $N;
    $n = $n;
    return array(
        $K,
        $N,
        $n,
        $kelompok1,
        $kelompok2,
        $kelompok3,
        $kelompok4,
        $kelompok5,
        $kelompok6
    );
    echo "processing random sampling. . . . . . done<br>";
}
function getCentroids($cluster = false, $count, $query)
{
    $atribut0 = 'id_pend';
    $atribut1 = 'id_k1';
    $atribut2 = 'id_k2';
    $atribut3 = 'id_k3';
    $atribut4 = 'id_k4';
    $atribut5 = 'luas_lr';
    $atribut6 = 'id_k6';
    $centroid1 = 0;
    $centroid2 = 0;
    $centroid3 = 0;
    $centroid4 = 0;
    $centroid5 = 0;
    $centroid6 = 0;
    $i = 1;
    $j = 1;
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
    $centroid1 = number_format(1 / $count * $centroid1, 5);
    $centroid2 = number_format(1 / $count * $centroid2, 5);
    $centroid3 = number_format(1 / $count * $centroid3, 5);
    $centroid4 = number_format(1 / $count * $centroid4, 5);
    $centroid5 = number_format(1 / $count * $centroid5, 5);
    $centroid6 = number_format(1 / $count * $centroid6, 5);
    return array(
        $centroid1,
        $centroid2,
        $centroid3,
        $centroid4,
        $centroid5,
        $centroid6
    );
}
function getDistance(
    $query,
    $centroid1,
    $centroid2,
    $centroid3,
    $centroid4,
    $centroid5,
    $centroid6
) {
    $atribut[0] = "id_pend";
    $atribut[1] = "id_k1";
    $atribut[2] = "id_k2";
    $atribut[3] = "id_k3";
    $atribut[4] = "id_k4";
    $atribut[5] = "luas_lr";
    $atribut[6] = "id_k6";
    $distance = array();
    $i = 1;
    while ($row = mysqli_fetch_assoc($query)) {
        $distance[$i] = (abs($row[$atribut[1]] - $centroid1)) + (abs($row[$atribut[2]]
            - $centroid2)) + (abs($row[$atribut[3]] - $centroid3))
            + (abs($row[$atribut[4]] - $centroid4)) + (abs($row[$atribut[5]] - $centroid5))
            + (abs($row[$atribut[6]] - $centroid6));
        $i++;
    }

    return $distance;
}
function getA($query, $cluster)
{
    $i = 1;
    while ($row = mysqli_fetch_assoc($query)) {
        for ($x = 1; $x <= 6; $x++) {
            if (in_array($row['id_pend'], $cluster[$x])) {
                $getData[$i][0] = $i;
                $getData[$i][1] = $row['id_k1'];
                $getData[$i][2] = $row['id_k2'];
                $getData[$i][3] = $row['id_k3'];
                $getData[$i][4] = $row['id_k4'];
                $getData[$i][5] = $row['luas_lr'];
                $getData[$i][6] = $row['id_k6'];
                $getData[$i][7] = $x;
                break;
            }
            $getData[$i][0] = $i;
            $getData[$i][1] = $row['id_k1'];
            $getData[$i][2] = $row['id_k2'];
            $getData[$i][3] = $row['id_k3'];
            $getData[$i][4] = $row['id_k4'];
            $getData[$i][5] = $row['luas_lr'];
            $getData[$i][6] = $row['id_k6'];
            $getData[$i][7] = 0;
        }
        $i++;
    }
    for ($i = 1; $i <= count($getData); $i++) {
        $a[$i] = 0;
        $dibagi[$i] = 0;
        for ($k = 1; $k <= 6; $k++) {
            if ($getData[$i][7] == $k) {
                for ($j = 1; $j <= count($getData); $j++) {
                    if ($getData[$j][7] == $k && $getData[$j][0] != $getData[$i][0]) {
                        $a[$i] += sqrt(pow(($getData[$i][1] - $getData[$j][1]), 2) + pow(($getData[$i][2]
                            - $getData[$j][2]), 2) + pow(($getData[$i][3] - $getData[$j][3]), 2)
                            + pow(($getData[$i][4] - $getData[$j][4]), 2) + pow(($getData[$i][5] -
                                $getData[$j][5]), 2) + pow(($getData[$i][6] - $getData[$j][6]), 2));
                        $dibagi[$i] = $dibagi[$i] + 1;
                        echo "nilai dibagi : $dibagi[$i] <br>";
                    }
                }
            }
        }
        if ($dibagi[$i] != 0) {
            $a[$i] = $a[$i] / $dibagi[$i];
        }
    }
    return $a;
}
function getB($query, $cluster)
{
    $i = 1;
    while ($row = mysqli_fetch_assoc($query)) {
        for ($x = 1; $x <= 6; $x++) {
            if (in_array($row['id_pend'], $cluster[$x])) {
                $getData[$i][1] = $row['id_k1'];
                $getData[$i][2] = $row['id_k2'];
                $getData[$i][3] = $row['id_k3'];
                $getData[$i][4] = $row['id_k4'];
                $getData[$i][5] = $row['luas_lr'];
                $getData[$i][6] = $row['id_k6'];
                $getData[$i][7] = $x;
                break;
            }
            $getData[$i][1] = $row['id_k1'];
            $getData[$i][2] = $row['id_k2'];
            $getData[$i][3] = $row['id_k3'];
            $getData[$i][4] = $row['id_k4'];
            $getData[$i][5] = $row['luas_lr'];
            $getData[$i][6] = $row['id_k6'];
            $getData[$i][7] = 0;
        }

        $i++;
    }
    $minB = array();
    for ($i = 1; $i <= count($getData); $i++) {
        for ($j = 1; $j <= count($getData); $j++) {
            $dibagi[$j] = 0;
            if ($getData[$i][7] != $getData[$j][7]) {
                $getValueB[$j] = 0;
                for ($k = 1; $k <= count($getData); $k++) {
                    if ($getData[$j][7] == $getData[$k][7]) {
                        $getValueB[$j] += (sqrt(pow(($getData[$i][1] -
                            $getData[$k][1]), 2) + pow(($getData[$i][2] - $getData[$k][2]), 2) +
                            pow(($getData[$i][3] - $getData[$k][3]), 2)
                            + pow(($getData[$i][4] -
                                $getData[$k][4]), 2) + pow(($getData[$i][5] - $getData[$k][5]), 2) +
                            pow(($getData[$i][6] - $getData[$k][6]), 2)));
                        $dibagi[$j]++;
                    }
                }
            }
            if ($dibagi[$j] != 0 && $getValueB[$j] != 0 && $getValueB[$j] != 0) {
                $getValueB[$j] = $getValueB[$j] / $dibagi[$j];
            }
        }
        if ($getValueB != 0) {
            $minB[$i] = min($getValueB);
        }
    }
    return $minB;
}
function silhouette($a = 0, $b = 0)
{
    $jmlA = count($a);
    $jmlB = count($b);
    for ($i = 1; $i <= $jmlA; $i++) {
        if ($a[$i] > $b[$i]) {
            $s[$i] = number_format(($b[$i] - $a[$i]) / $a[$i], 5);
        } elseif ($b[$i] > $a[$i]) {
            $s[$i] = number_format(($b[$i] - $a[$i]) / $b[$i], 5);
        }
        if ($a[$i] == 0 || $b[$i] == 0) {
            $s[$i] = 0;
        }
    }
    return $s;
}
