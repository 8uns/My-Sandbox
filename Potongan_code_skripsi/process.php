<?php
$secondFirst = date(s);
include_once '../function/connection.php';
include_once '../function/function.php';
include_once 'clustering.php';
$notif = isset($_GET['notif']) ? $_GET['notif'] : false;
$option = isset($_GET['option']) ? $_GET['option'] : false;
$tabel = "penduduk";
if ($notif != false) {
    if ($notif == 'clustering') {
        $query = mysqli_query($connect, "SELECT * FROM $tabel");
        $count = mysqli_num_rows($query);
        $jumlahCluster = $option;
        if ($jumlahCluster == 2) {
            list($K, $N, $n, $cluster[1], $cluster[2]) = getRandoms($count, $jumlahCluster);
            $mysqli_insert_srs_K = mysqli_query($connect, "UPDATE srs SET K='$K' WHERE
id_srs='2'");
            $mysqli_insert_srs_N = mysqli_query($connect, "UPDATE srs SET n_po='$N' WHERE
id_srs='2'");
            $mysqli_insert_srs_n = mysqli_query($connect, "UPDATE srs SET n_sam='$n' WHERE
id_srs='2'");
        } elseif ($jumlahCluster == 3) {
            list($K, $N, $n, $cluster[1], $cluster[2], $cluster[3]) = getRandoms(
                $count,
                $jumlahCluster
            );
            $mysqli_insert_srs_K = mysqli_query($connect, "UPDATE srs SET K='$K' WHERE
id_srs='3'");
            $mysqli_insert_srs_N = mysqli_query($connect, "UPDATE srs SET n_po='$N' WHERE
id_srs='3'");
            $mysqli_insert_srs_n = mysqli_query($connect, "UPDATE srs SET n_sam='$n' WHERE
id_srs='3'");
        } elseif ($jumlahCluster == 4) {
            list($K, $N, $n, $cluster[1], $cluster[2], $cluster[3], $cluster[4]) =
                getRandoms($count, $jumlahCluster);
            $mysqli_insert_srs_K = mysqli_query($connect, "UPDATE srs SET K='$K' WHERE
id_srs='4'");
            $mysqli_insert_srs_N = mysqli_query($connect, "UPDATE srs SET n_po='$N' WHERE
id_srs='4'");
            $mysqli_insert_srs_n = mysqli_query($connect, "UPDATE srs SET n_sam='$n' WHERE
id_srs='4'");
        }
        $querySelect = mysqli_query($connect, "SELECT * FROM penduduk");
        $m[1] = 1;
        $m[2] = 1;
        $m[3] = 1;
        $m[4] = 1;
        $m[5] = 1;
        $m[6] = 1;
        echo "N : k1 | k2 | k3 | k4 | k5 | k6 | Cluster <br>";
        while ($row = mysqli_fetch_assoc($querySelect)) {
            for ($i = 1; $i <= $jumlahCluster; $i++) {
                if ($row['id_pend'] == $cluster[$i][$m[$i]]) {
                    echo "$row[id_pend] : $row[id_k1] | $row[id_k2] | $row[id_k3] | $row[id_k4] | $row[luas_lr] | $row[id_k6] | Cluster $i<br>";
                    if ($jumlahCluster == 2) {
                        $queryUpdateRand[$i] = mysqli_query($connect, "UPDATE penduduk SET rand2='$i' WHERE id_pend='$row[id_pend]'");
                    } elseif ($jumlahCluster == 3) {
                        $queryUpdateRand[$i] = mysqli_query($connect, "UPDATE penduduk SET rand3='$i' WHERE id_pend='$row[id_pend]'");
                    } elseif ($jumlahCluster == 4) {
                        $queryUpdateRand[$i] = mysqli_query($connect, "UPDATE penduduk SET rand4='$i' WHERE id_pend='$row[id_pend]'");
                    }
                    $m[$i]++;
                }
            }
        }
        for ($i = 1; $i <= $jumlahCluster; $i++) { // menggunakan for untuk menghitung centroid setiap cluster

            $tc[$i] = count($cluster[$i]); // mendapatkan jumlah data setiap cluster
            $querys[$i] = mysqli_query($connect, "SELECT * FROM $tabel"); // memanggil nilai
            list(
                $centroid[$i][1],
                $centroid[$i][2],
                $centroid[$i][3],
                $centroid[$i][4],
                $centroid[$i][5],
                $centroid[$i][6]
            ) = getCentroids(
                $cluster[$i],
                $tc[$i],
                $querys[$i]
            );
            echo "<br>nilai centroid cluster $i: <br>";
            echo $centroid[$i][1] . " | " . $centroid[$i][2] . " | " . $centroid[$i][3] . "
| " . $centroid[$i][4] . " | " . $centroid[$i][5] . " | " . $centroid[$i][6] .
                "<br>";
            $k1 = number_format($centroid[$i][1], 5);
            $k2 = number_format($centroid[$i][2], 5);
            $k3 = number_format($centroid[$i][3], 5);
            $k4 = number_format($centroid[$i][4], 5);
            $k5 = number_format($centroid[$i][5], 5);
            $k6 = number_format($centroid[$i][6], 5);
            if ($jumlahCluster == 4) {
                if ($i == 1) {
                    $queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET
c41='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");
                } elseif ($i == 2) {
                    $queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET
c42='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");
                } elseif ($i == 3) {
                    $queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET
c43='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");
                } elseif ($i == 4) {
                    $queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET
c44='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");
                }
            } elseif ($jumlahCluster == 3) {
                if ($i == 1) {
                    $queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET
c31='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");
                } elseif ($i == 2) {
                    $queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET
c32='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");
                } elseif ($i == 3) {
                    $queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET
c33='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");
                }
            } elseif ($jumlahCluster == 2) {
                if ($i == 1) {
                    $queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET
c21='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");
                } elseif ($i == 2) {
                    $queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET
c22='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");
                }
            }
        }
        echo "<br><br>";
        $iterasi = true;
        $jmlIterasi = 0;
        if ($jumlahCluster == 2) {
            $queryDelDist = mysqli_query($connect, "UPDATE jarak_akhir SET j21='NULL',
j22='NULL'");
        } elseif ($jumlahCluster == 3) {
            $queryDelDist = mysqli_query($connect, "UPDATE jarak_akhir SET j31='NULL',
j32='NULL', j33='NULL'");
        } elseif ($jumlahCluster == 4) {
            $queryDelDist = mysqli_query($connect, "UPDATE jarak_akhir SET j41='NULL',
j42='NULL', j43='NULL', j44='NULL'");
        }
        $firstDistance = 0;
        while ($iterasi) {
            $firstDistance++;
            for ($i = 1; $i <= $jumlahCluster; $i++) { // menggunakan perulangan untuk menghitung setiap cluster
                $queryGetDistance[$i] = mysqli_query($connect, "SELECT * FROM $tabel"); //
                $distance[$i] = getDistance(
                    $queryGetDistance[$i],
                    number_format($centroid[$i][1], 5),
                    number_format($centroid[$i][2], 5),
                    umber_format($centroid[$i][3], 5),
                    number_format($centroid[$i][4], 5),
                    number_format($centroid[$i][5], 5),
                    number_format($centroid[$i][6], 5)
                );
                $j = 1;
                $queryPendforDist = mysqli_query($connect, "SELECT * FROM penduduk"); // memanggil data penduduk untuk diupdate
                while ($row = mysqli_fetch_assoc($queryPendforDist)) {
                    $value = number_format($distance[$i][$j], 5);
                    if ($jumlahCluster == 4) {
                        $querySelectDist[$j] = mysqli_query($connect, "SELECT * FROM jarak_akhir");
                        $x = 1;
                        while ($rowDist = mysqli_fetch_assoc($querySelectDist[$j])) {
                            $tampungNumDist[$x] = $rowDist['num'];
                            $x++;
                        }
                        if ($firstDistance == 1) {
                            if (in_array($j, $tampungNumDist)) {
                                echo "true ada nilai <br>";
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET
id_pend='$row[id_pend]', j41='$value' WHERE num='$j'");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET
id_pend='$row[id_pend]', j42='$value' WHERE num='$j'");
                                } elseif ($i == 3) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET
id_pend='$row[id_pend]', j43='$value' WHERE num='$j'");
                                } elseif ($i == 4) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET
id_pend='$row[id_pend]', j44='$value' WHERE num='$j'");
                                }
                            } else {
                                echo "false<br>";
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend,
j41) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend,
j42) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 3) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend,
j43) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 4) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend,
j44) VALUES ('$j','$row[id_pend]','$value')");
                                }
                            }
                        } else {
                            if (in_array($j, $tampungNumDist)) {
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET
id_pend='$row[id_pend]', j41='$value' WHERE num='$j'");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET
id_pend='$row[id_pend]', j42='$value' WHERE num='$j'");
                                } elseif ($i == 3) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET
id_pend='$row[id_pend]', j43='$value' WHERE num='$j'");
                                } elseif ($i == 4) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET
id_pend='$row[id_pend]', j44='$value' WHERE num='$j'");
                                }
                            } else {
                                // echo "false<br>";
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend,
j41) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend,
j42) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 3) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend,
j43) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 4) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend,
j44) VALUES ('$j','$row[id_pend]','$value')");
                                }
                            }
                        }
                    } elseif ($jumlahCluster == 3) {
                        $querySelectDist[$j] = mysqli_query($connect, "SELECT * FROM jarak_akhir");
                        $x = 1;
                        while ($rowDist = mysqli_fetch_assoc($querySelectDist[$j])) {
                            $tampungNumDist[$x] = $rowDist['num'];
                            $x++;
                        }
                        if ($firstDistance == 1) {
                            if (in_array($j, $tampungNumDist)) {
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET
id_pend='$row[id_pend]', j31='$value' WHERE num='$j'");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET
id_pend='$row[id_pend]', j32='$value' WHERE num='$j'");
                                } elseif ($i == 3) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET
id_pend='$row[id_pend]', j33='$value' WHERE num='$j'");
                                }
                            } else {
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend,
j31) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend,
j32) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 3) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend,
j33) VALUES ('$j','$row[id_pend]','$value')");
                                }
                            }
                        } else {
                            if (in_array($j, $tampungNumDist)) {
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET
id_pend='$row[id_pend]', j31='$value' WHERE num='$j'");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET
id_pend='$row[id_pend]', j32='$value' WHERE num='$j'");
                                } elseif ($i == 3) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET
id_pend='$row[id_pend]', j33='$value' WHERE num='$j'");
                                }
                            } else {
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend,
j31) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend,
j32) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 3) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend,
j33) VALUES ('$j','$row[id_pend]','$value')");
                                }
                            }
                        }
                    } elseif ($jumlahCluster == 2) {
                        $querySelectDist[$j] = mysqli_query($connect, "SELECT * FROM jarak_akhir");
                        $x = 1;
                        while ($rowDist = mysqli_fetch_assoc($querySelectDist[$j])) {
                            $tampungNumDist[$x] = $rowDist['num'];
                            $x++;
                        }
                        if ($firstDistance == 1) {
                            if (in_array($j, $tampungNumDist)) {
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET id_pend='$row[id_pend]', j21='$value' WHERE num='$j'");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET
id_pend='$row[id_pend]', j22='$value' WHERE num='$j'");
                                }
                            } else {
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend,
j21) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend,
j22) VALUES ('$j','$row[id_pend]','$value')");
                                }
                            }
                        } else {
                            if (in_array($j, $tampungNumDist)) {
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET
id_pend='$row[id_pend]', j21='$value' WHERE num='$j'");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET
id_pend='$row[id_pend]', j22='$value' WHERE num='$j'");
                                }
                            } else {
                                if ($i == 1) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend,
j21) VALUES ('$j','$row[id_pend]','$value')");
                                } elseif ($i == 2) {
                                    $queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend,
j22) VALUES ('$j','$row[id_pend]','$value')");
                                }
                            }
                        }
                    }
                    $j++;
                }
            }
            $m[1] = 1;
            $m[2] = 1;
            $m[3] = 1;
            $m[4] = 1;
            $newCluster[1][$m[1]] = 0;
            $newCluster[2][$m[2]] = 0;
            $newCluster[3][$m[3]] = 0;
            $newCluster[4][$m[4]] = 0;
            for ($j = 1; $j <= $count; $j++) {
                if ($jumlahCluster == 2) {
                    if ($distance[1][$j] < $distance[2][$j]) {
                        $newCluster[1][$m[1]] = $j;
                        $m[1]++;
                    } elseif ($distance[2][$j] < $distance[1][$j]) {
                        $newCluster[2][$m[2]] = $j;
                        $m[2]++;
                    } else {
                        // echo "ke - $j | data anomali<br>";
                    }
                } elseif ($jumlahCluster == 3) {
                    if ($distance[1][$j] < $distance[2][$j] && $distance[1][$j] < $distance[3][$j]) {
                        $newCluster[1][$m[1]] = $j;
                        $m[1]++;
                    } elseif (
                        $distance[2][$j] < $distance[1][$j] && $distance[2][$j] <
                        $distance[3][$j]
                    ) {
                        $newCluster[2][$m[2]] = $j;
                        $m[2]++;
                    } elseif (
                        $distance[3][$j] < $distance[1][$j] && $distance[3][$j] <
                        $distance[2][$j]
                    ) {
                        $newCluster[3][$m[3]] = $j;
                        $m[3]++;
                    } else {
                        // echo "ke - $j | data anomali<br>";
                    }
                } elseif ($jumlahCluster == 4) {
                    if (
                        $distance[1][$j] < $distance[2][$j] && $distance[1][$j] < $distance[3][$j]
                        && $distance[1][$j] < $distance[4][$j]
                    ) {
                        $newCluster[1][$m[1]] = $j;
                        $m[1]++;
                    } elseif (
                        $distance[2][$j] < $distance[1][$j] && $distance[2][$j] <
                        $distance[3][$j] && $distance[2][$j] < $distance[4][$j]
                    ) {
                        $newCluster[2][$m[2]] = $j;
                        $m[2]++;
                    } elseif (
                        $distance[3][$j] < $distance[1][$j] && $distance[3][$j] <
                        $distance[2][$j] && $distance[3][$j] < $distance[4][$j]
                    ) {
                        $newCluster[3][$m[3]] = $j;
                        $m[3]++;
                    } elseif (
                        $distance[4][$j] < $distance[1][$j] && $distance[4][$j] <
                        $distance[2][$j] && $distance[4][$j] < $distance[3][$j]
                    ) {
                        $newCluster[4][$m[4]] = $j;
                        $m[4]++;
                    } else {
                        // echo "ke - $j | data anomali<br>";
                    }
                }
                for ($i = 1; $i <= $jumlahCluster; $i++) {
                    $t[$i] = count($newCluster[$i]);
                    $querys[$i] = mysqli_query($connect, "SELECT * FROM $tabel");
                    list(
                        $newCentroid[$i][1],
                        $newCentroid[$i][2],
                        $newCentroid[$i][3],
                        $newCentroid[$i][4],
                        $newCentroid[$i][5],
                        $newCentroid[$i][6]
                    ) =
                        getCentroids($newCluster[$i], $t[$i], $querys[$i]);
                    $kLast1[$i] = number_format($newCentroid[$i][1], 5);
                    $kLast2[$i] = number_format($newCentroid[$i][2], 5);
                    $kLast3[$i] = number_format($newCentroid[$i][3], 5);
                    $kLast4[$i] = number_format($newCentroid[$i][4], 5);
                    $kLast5[$i] = number_format($newCentroid[$i][5], 5);
                    $kLast6[$i] = number_format($newCentroid[$i][6], 5);
                }
                $pindah = 0;
                for ($i = 1; $i <= $jumlahCluster; $i++) {
                    if (
                        $centroid[$i][1] != $newCentroid[$i][1] || $centroid[$i][2] !=
                        $newCentroid[$i][2] || $centroid[$i][3] != $newCentroid[$i][3]
                        || $centroid[$i][4] != $newCentroid[$i][4] || $centroid[$i][5] !=
                        $newCentroid[$i][5] || $centroid[$i][6] != $newCentroid[$i][6]
                    ) {
                        $pindah++;
                    } else {
                        // echo "<br>PADA CLUSTER $i:";
                        // echo "<br> DATA TIDAK BERPINDAH <br>";
                    }
                    $centroid[$i][1] = $newCentroid[$i][1];
                    $centroid[$i][2] = $newCentroid[$i][2];
                    $centroid[$i][3] = $newCentroid[$i][3];
                    $centroid[$i][4] = $newCentroid[$i][4];
                    $centroid[$i][5] = $newCentroid[$i][5];
                    $centroid[$i][6] = $newCentroid[$i][6];
                }
                if ($pindah == 0) {
                    $iterasi = false;
                }
                if ($iterasi == true) {
                    $newCluster[1] = false;
                    $newCluster[2] = false;
                    $newCluster[3] = false;
                    $newCluster[4] = false;
                    $newCluster[5] = false;
                    $newCluster[6] = false;
                }
                $jmlIterasi++;
            } // penutup iterasi
            for ($i = 1; $i <= $jumlahCluster; $i++) {
                if ($jumlahCluster == 4) {
                    if ($i == 1) {
                        $queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET
c41='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]'
WHERE centroid='last'");
                    } elseif ($i == 2) {
                        $queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET
c42='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]'
WHERE centroid='last'");
                    } elseif ($i == 3) {
                        $queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET
c43='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]'
WHERE centroid='last'");
                    } elseif ($i == 4) {
                        $queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET
c44='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]'
WHERE centroid='last'");
                    }
                } elseif ($jumlahCluster == 3) {
                    if ($i == 1) {
                        $queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET
c31='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]'
WHERE centroid='last'");
                    } elseif ($i == 2) {
                        $queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET
c32='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]'
WHERE centroid='last'");
                    } elseif ($i == 3) {
                        $queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET
c33='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]'
WHERE centroid='last'");
                    }
                } elseif ($jumlahCluster == 2) {
                    if ($i == 1) {
                        $queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET
c21='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]'
WHERE centroid='last'");
                    } elseif ($i == 2) {
                        $queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET
c22='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]'
WHERE centroid='last'");
                    }
                }
            }
            if ($iterasi == false) {
                $jmlIterasi -= 1;
                if ($jumlahCluster == 4) {
                    $queryIterasi = mysqli_query($connect, "UPDATE jml_iterasi SET
4clust='$jmlIterasi' WHERE id_iterasi='1'");
                } elseif ($jumlahCluster == 3) {
                    $queryIterasi = mysqli_query($connect, "UPDATE jml_iterasi SET
3clust='$jmlIterasi' WHERE id_iterasi='1'");
                } elseif ($jumlahCluster == 2) {
                    $queryIterasi = mysqli_query($connect, "UPDATE jml_iterasi SET
2clust='$jmlIterasi' WHERE id_iterasi='1'");
                }
                $query = mysqli_query($connect, "SELECT * FROM $tabel");
                $h[1] = 1;
                $h[2] = 1;
                $h[3] = 1;
                $h[4] = 1;
                $h[5] = 1;
                $h[6] = 1;
                $urut = 1;
                while ($row = mysqli_fetch_assoc($query)) {
                    if (
                        isset($newCluster[1][$h[1]]) || isset($newCluster[2][$h[2]]) ||
                        isset($newCluster[3][$h[3]]) || isset($newCluster[4][$h[4]]) ||
                        isset($newCluster[5][$h[5]]) || isset($newCluster[6][$h[6]])
                    ) {
                        for ($i = 1; $i <= $jumlahCluster; $i++) {
                            if (isset($newCluster[$i][$h[$i]])) {
                                if ($row['id_pend'] == $newCluster[$i][$h[$i]]) {
                                    if ($jumlahCluster == 2) {
                                        $queryUpdateClust[$i] = mysqli_query($connect, "UPDATE penduduk SET 2clust='$i'
WHERE id_pend='$row[id_pend]'");
                                    } elseif ($jumlahCluster == 3) {
                                        $queryUpdateClust[$i] = mysqli_query($connect, "UPDATE penduduk SET 3clust='$i'
WHERE id_pend='$row[id_pend]'");
                                    } elseif ($jumlahCluster == 4) {
                                        $queryUpdateClust[$i] = mysqli_query($connect, "UPDATE penduduk SET 4clust='$i'
WHERE id_pend='$row[id_pend]'");
                                    }
                                    $h[$i]++;
                                }
                            }
                        }
                        if (
                            !in_array($row['id_pend'], $newCluster[1]) && !in_array(
                                $row['id_pend'],
                                $newCluster[2]
                            ) &&
                            !in_array($row['id_pend'], $newCluster[3]) && !in_array(
                                $row['id_pend'],
                                $newCluster[4]
                            ) &&
                            !in_array($row['id_pend'], $newCluster[5]) && !in_array(
                                $row['id_pend'],
                                $newCluster[6]
                            )
                        ) {
                            // echo "ke - $urut : $row[id_k1] | $row[id_k2] | $row[id_k3] | $row[id_k4] | $row[luas_lr] | $row[id_k6] | tidak memiliki kelompok<br>";
                        }
                    }
                    $urut++;
                }
            }
            if ($option == 2) {
                echo "<META HTTP-EQUIV='Refresh' Content='0; URL=" . BASEURL .
                    "index.php?page=clusters&subpage=twocluster&view=chart'>";
            } elseif ($option == 3) {
                echo "<META HTTP-EQUIV='Refresh' Content='0; URL=" . BASEURL .
                    "index.php?page=clusters&subpage=threecluster&view=chart'>";
            } elseif ($option == 4) {
                echo "<META HTTP-EQUIV='Refresh' Content='0; URL=" . BASEURL .
                    "index.php?page=clusters&subpage=fourcluster&view=chart'>";
            }
        }
    } elseif ($notif == 'validasi') { // MELAKUKAN VALIDASI
        $u[1] = 1;
        $u[2] = 1;
        $u[3] = 1;
        $u[4] = 1;
        $querySelPend = mysqli_query($connect, "SELECT * FROM penduduk");
        $i = 1;
        while ($row = mysqli_fetch_assoc($querySelPend)) {
            if ($option == 2) {
                if ($row['2clust'] == 1) {
                    $newCluster[1][$u[1]] = $row['id_pend'];
                    echo "data pada 4 cluster. kelompok - " . $row['id_pend'], "<br>";
                    $u[1]++;
                } elseif ($row['2clust'] == 2) {
                    $newCluster[2][$u[2]] = $row['id_pend'];
                    echo "data pada 4 cluster. kelompok - " . $row['id_pend'], "<br>";
                    $u[2]++;
                }
            } elseif ($option == 3) {
                if ($row['3clust'] == 1) {
                    $newCluster[1][$u[1]] = $row['id_pend'];
                    echo "data pada 4 cluster. kelompok - " . $row['id_pend'], "<br>";
                    $u[1]++;
                } elseif ($row['3clust'] == 2) {
                    $newCluster[2][$u[2]] = $row['id_pend'];
                    echo "data pada 4 cluster. kelompok - " . $row['id_pend'], "<br>";
                    $u[2]++;
                } elseif ($row['3clust'] == 3) {
                    $newCluster[3][$u[3]] = $row['id_pend'];
                    echo "data pada 4 cluster. kelompok - " . $row['id_pend'], "<br>";
                    $u[3]++;
                }
            } elseif ($option == 4) {
                if ($row['4clust'] == 1) {
                    $newCluster[1][$u[1]] = $row['id_pend'];
                    echo "data pada 4 cluster. kelompok - " . $row['id_pend'], "<br>";
                    $u[1]++;
                } elseif ($row['4clust'] == 2) {
                    $newCluster[2][$u[2]] = $row['id_pend'];
                    echo "data pada 4 cluster. kelompok - " . $row['id_pend'], "<br>";
                    $u[2]++;
                } elseif ($row['4clust'] == 3) {
                    $newCluster[3][$u[3]] = $row['id_pend'];
                    echo "data pada 4 cluster. kelompok - " . $row['id_pend'], "<br>";
                    $u[3]++;
                } elseif ($row['4clust'] == 4) {
                    $newCluster[4][$u[4]] = $row['id_pend'];
                    echo "data pada 4 cluster. kelompok - " . $row['id_pend'], "<br>";
                    $u[4]++;
                }
            }
        }
        echo "<br>";
        echo "<br>";
        echo "MELAKUKAN VALIDASI<br>";
        echo "<br>";
        echo "Menampilkan Nilai A<br>";
        $queryA = mysqli_query($connect, "SELECT * FROM $tabel");
        $nilaiA = getA($queryA, $newCluster);
        $nilaiRataA = 0;
        for ($i = 1; $i <= mysqli_num_rows($queryA); $i++) {
            echo "ke - $i : " . $nilaiA[$i] . "<br>";
            $id_det = $i;
            $valueDetSil = $nilaiA[$i];
            $nilaiRataA += $nilaiA[$i];
            if ($option == 4) {
                $querySelecDetSil[$j] = mysqli_query($connect, "SELECT * FROM det_sil");
                $x = 1;
                while ($rowDetSil = mysqli_fetch_assoc($querySelecDetSil[$j])) {
                    $tampungIdPendDetSil[$x] = $rowDetSil['id_ds'];
                    $x++;
                }
                if (in_array($i, $tampungIdPendDetSil)) {
                    $queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil SET id_pend='$i',
a4='$valueDetSil' WHERE id_ds='$i'");
                } else {
                    $queryInsertDetSil = mysqli_query($connect, "INSERT INTO det_sil (id_ds, id_pend,
a4) VALUES ('$i','$i','$valueDetSil')");
                }
            } elseif ($option == 3) {
                $querySelecDetSil[$j] = mysqli_query($connect, "SELECT * FROM det_sil");
                $x = 1;
                while ($rowDetSil = mysqli_fetch_assoc($querySelecDetSil[$j])) {
                    $tampungIdPendDetSil[$x] = $rowDetSil['id_ds'];
                    $x++;
                }
                if (in_array($i, $tampungIdPendDetSil)) {
                    $queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil SET id_pend='$i',
a3='$valueDetSil' WHERE id_ds='$i'");
                } else {
                    $queryInsertDetSil = mysqli_query($connect, "INSERT INTO det_sil (id_ds, id_pend,
a3) VALUES ('$i','$i','$valueDetSil')");
                }
            } elseif ($option == 2) {
                $querySelecDetSil[$j] = mysqli_query($connect, "SELECT * FROM det_sil");
                $x = 1;
                while ($rowDetSil = mysqli_fetch_assoc($querySelecDetSil[$j])) {
                    $tampungIdPendDetSil[$x] = $rowDetSil['id_ds'];
                    $x++;
                }
                if (in_array($i, $tampungIdPendDetSil)) {
                    $queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil SET id_pend='$i',
a2='$valueDetSil' WHERE id_ds='$i'");
                } else {
                    $queryInsertDetSil = mysqli_query($connect, "INSERT INTO det_sil (id_ds, id_pend,
a2) VALUES ('$i','$i','$valueDetSil')");
                }
            }
        }
        $nilaiRataA = $nilaiRataA / mysqli_num_rows($queryA);
        echo "<br>Dengan rata-rata nilai A adalah $nilaiRataA<br><br>";
        echo "<br>";
        echo "Menampilkan Nilai B<br>";
        $queryB = mysqli_query($connect, "SELECT * FROM $tabel");
        $nilaiB = getB($queryB, $newCluster);
        $nilaiRataB = 0;
        for ($i = 1; $i <= mysqli_num_rows($queryB); $i++) {
            if ($nilaiB[$i] == 9999) {
                echo "ke - $i : " . "0" . "<br>";
            } else {
                echo "ke - $i : " . $nilaiB[$i] . "<br>";
                $valueDetSil = $nilaiB[$i];
                $nilaiRataB += $nilaiB[$i];
                if ($option == 4) {
                    $querySelecDetSil[$j] = mysqli_query($connect, "SELECT * FROM
det_sil");
                    $x = 1;
                    while ($rowDetSil = mysqli_fetch_assoc($querySelecDetSil[$j])) {
                        $tampungIdPendDetSil[$x] = $rowDetSil['id_ds'];
                        $x++;
                    }
                    if (in_array($i, $tampungIdPendDetSil)) {
                        $queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil SET id_pend='$i',
b4='$valueDetSil' WHERE id_ds='$i'");
                    } else {
                        $queryInsertDetSil = mysqli_query($connect, "INSERT INTO det_sil (id_ds,
id_pend, b4) VALUES ('$i','$i','$valueDetSil')");
                    }
                } elseif ($option == 3) {
                    $querySelecDetSil[$j] = mysqli_query($connect, "SELECT
* FROM det_sil");
                    $x = 1;
                    while ($rowDetSil =
                        mysqli_fetch_assoc($querySelecDetSil[$j])
                    ) {
                        $tampungIdPendDetSil[$x] = $rowDetSil['id_ds'];
                        $x++;
                    }
                    if (in_array($i, $tampungIdPendDetSil)) {
                        $queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil SET id_pend='$i',
b3='$valueDetSil' WHERE id_ds='$i'");
                    } else {
                        $queryInsertDetSil = mysqli_query($connect, "INSERT INTO det_sil (id_ds,
id_pend, b3) VALUES ('$i','$i','$valueDetSil')");
                    }
                } elseif ($option == 2) {
                    $querySelecDetSil[$j] = mysqli_query($connect, "SELECT * FROM det_sil");
                    $x = 1;
                    while ($rowDetSil = mysqli_fetch_assoc($querySelecDetSil[$j])) {
                        $tampungIdPendDetSil[$x] = $rowDetSil['id_ds'];
                        $x++;
                    }
                    if (in_array($i, $tampungIdPendDetSil)) {
                        $queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil SET id_pend='$i',
b2='$valueDetSil' WHERE id_ds='$i'");
                    } else {
                        $queryInsertDetSil = mysqli_query($connect, "INSERT INTO det_sil (id_ds,
id_pend, b2) VALUES ('$i','$i','$valueDetSil')");
                    }
                }
            }
        }
        $nilaiRataB = $nilaiRataB / mysqli_num_rows($queryB);
        echo "<br>Dengan rata-rata nilai B adalah $nilaiRataB<br><br>";
        $silhout = silhouette($nilaiA, $nilaiB);
        echo "Nilai Silhouette Coefficient dari $jumlahCluster Cluster : $silhout<br>";
        echo "<pre>" . print_r($nilaiA) . "</pre><br>";
        echo
        "<pre>" . print_r($nilaiB) . "</pre><br>";
        echo "<br>";
        echo "Menampilkan Nilai Silhouette Coefficient<br>";
        $nilaiRataS = 0;
        for ($i = 1; $i <= count($silhout); $i++) {
            echo "ke - $i : " . $silhout[$i] . "<br>";
            $nilaiRataS += $silhout[$i];
            if ($option == 4) {
                $queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil SET
s4='$silhout[$i]' WHERE id_ds='$i'");
            } elseif ($option == 3) {
                $queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil SET
s3='$silhout[$i]' WHERE id_ds='$i'");
            } elseif ($option == 2) {
                $queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil
SET s2='$silhout[$i]' WHERE id_ds='$i'");
            }
        }
        $nilaiRataS = $nilaiRataS / ($i - 1);
        if ($option == 2) {
            $queryInsertSil = mysqli_query($connect, "UPDATE sil_coe SET
pow_2clust='$nilaiRataS' WHERE id_sil='1'");
        } elseif ($option == 3) {
            $queryInsertSil = mysqli_query($connect, "UPDATE sil_coe
SET pow_3clust='$nilaiRataS' WHERE id_sil='1'");
        } elseif ($option == 4) {
            $queryInsertSil = mysqli_query($connect, "UPDATE sil_coe SET
pow_4clust='$nilaiRataS' WHERE id_sil='1'");
        }
        echo "<br>";
        echo "Nilai Silhouette Coefficient dari $jumlahCluster Cluster :
$nilaiRataS<br>";
        echo "<META HTTP-EQUIV='Refresh' Content='1; URL=" . BASEURL . "index.php'>";
    }
} else {
    echo "ERROR";
}
