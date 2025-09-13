<?php
// Memulai penghitungan waktu untuk mengukur durasi proses.
$secondFirst = date(s);

// Mengimpor file-file yang berisi koneksi database dan fungsi-fungsi lain.
// 'connection.php' untuk koneksi ke database.
// 'function.php' berisi fungsi-fungsi umum.
// 'clustering.php' berisi logika inti dari algoritma K-Means.
include_once '../function/connection.php';
include_once '../function/function.php';
include_once 'clustering.php';

// Mendapatkan parameter dari URL.
// 'notif' berfungsi sebagai penanda aksi (misalnya 'clustering' atau 'validasi').
// 'option' berisi jumlah cluster yang dipilih oleh pengguna (misalnya 2, 3, atau 4).
$notif = isset($_GET['notif']) ? $_GET['notif'] : false;
$option = isset($_GET['option']) ? $_GET['option'] : false;

// Nama tabel data utama yang akan di-cluster.
$tabel = "penduduk";

// Memeriksa apakah parameter 'notif' telah disetel.
if ($notif != false) {
    // Jika notif adalah 'clustering', maka proses clustering dimulai.
    if ($notif == 'clustering') {
        // Mengambil semua data dari tabel 'penduduk'.
        $query = mysqli_query($connect, "SELECT * FROM $tabel");
        $count = mysqli_num_rows($query); // Menghitung total data.
        $jumlahCluster = $option; // Menetapkan jumlah cluster dari input user.

        // Bagian ini melakukan Systematic Random Sampling untuk inisialisasi centroid awal.
        // Kode ini menangani kasus untuk 2, 3, dan 4 cluster.
        if ($jumlahCluster == 2) {
            // Memanggil fungsi getRandoms untuk mendapatkan data awal cluster.
            list($K, $N, $n, $cluster[1], $cluster[2]) = getRandoms($count, $jumlahCluster);
            // Menyimpan hasil sampling ke tabel 'srs'.
            $mysqli_insert_srs_K = mysqli_query($connect, "UPDATE srs SET K='$K' WHERE id_srs='2'");
            $mysqli_insert_srs_N = mysqli_query($connect, "UPDATE srs SET n_po='$N' WHERE id_srs='2'");
            $mysqli_insert_srs_n = mysqli_query($connect, "UPDATE srs SET n_sam='$n' WHERE id_srs='2'");
        } elseif ($jumlahCluster == 3) {
            list($K, $N, $n, $cluster[1], $cluster[2], $cluster[3]) = getRandoms($count, $jumlahCluster);
            $mysqli_insert_srs_K = mysqli_query($connect, "UPDATE srs SET K='$K' WHERE id_srs='3'");
            $mysqli_insert_srs_N = mysqli_query($connect, "UPDATE srs SET n_po='$N' WHERE id_srs='3'");
            $mysqli_insert_srs_n = mysqli_query($connect, "UPDATE srs SET n_sam='$n' WHERE id_srs='3'");
        } elseif ($jumlahCluster == 4) {
            list($K, $N, $n, $cluster[1], $cluster[2], $cluster[3], $cluster[4]) = getRandoms($count, $jumlahCluster);
            $mysqli_insert_srs_K = mysqli_query($connect, "UPDATE srs SET K='$K' WHERE id_srs='4'");
            $mysqli_insert_srs_N = mysqli_query($connect, "UPDATE srs SET n_po='$N' WHERE id_srs='4'");
            $mysqli_insert_srs_n = mysqli_query($connect, "UPDATE srs SET n_sam='$n' WHERE id_srs='4'");
        }

        $querySelect = mysqli_query($connect, "SELECT * FROM penduduk");
        // Inisialisasi counter untuk setiap cluster.
        $m = array(1, 1, 1, 1, 1, 1);
        echo "N : k1 | k2 | k3 | k4 | k5 | k6 | Cluster <br>";
        // Mengelompokkan data awal ke dalam cluster yang telah diacak.
        while ($row = mysqli_fetch_assoc($querySelect)) {
            for ($i = 1; $i <= $jumlahCluster; $i++) {
                if ($row['id_pend'] == $cluster[$i][$m[$i]]) {
                    // Mencetak hasil pengelompokan awal.
                    echo "$row[id_pend] : $row[id_k1] | $row[id_k2] | $row[id_k3] | $row[id_k4] | $row[luas_lr] | $row[id_k6] | Cluster $i<br>";
                    // Memperbarui kolom 'rand' di tabel penduduk.
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
        
        // Menghitung centroid awal dari cluster yang telah dibuat.
        for ($i = 1; $i <= $jumlahCluster; $i++) {
            $tc[$i] = count($cluster[$i]);
            $querys[$i] = mysqli_query($connect, "SELECT * FROM $tabel");
            list($centroid[$i][1], $centroid[$i][2], $centroid[$i][3], $centroid[$i][4], $centroid[$i][5], $centroid[$i][6]) = getCentroids($cluster[$i], $tc[$i], $querys[$i]);
            echo "<br>nilai centroid cluster $i: <br>";
            echo $centroid[$i][1] . " | " . $centroid[$i][2] . " | " . $centroid[$i][3] . " | " . $centroid[$i][4] . " | " . $centroid[$i][5] . " | " . $centroid[$i][6] . "<br>";

            $k1 = number_format($centroid[$i][1], 5);
            $k2 = number_format($centroid[$i][2], 5);
            $k3 = number_format($centroid[$i][3], 5);
            $k4 = number_format($centroid[$i][4], 5);
            $k5 = number_format($centroid[$i][5], 5);
            $k6 = number_format($centroid[$i][6], 5);
            
            // Menyimpan nilai centroid awal ke dalam tabel 'allcentroid'.
            if ($jumlahCluster == 4) {
                if ($i == 1) {$queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET c41='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");}
                elseif ($i == 2) {$queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET c42='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");}
                elseif ($i == 3) {$queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET c43='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");}
                elseif ($i == 4) {$queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET c44='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");}
            } elseif ($jumlahCluster == 3) {
                if ($i == 1) {$queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET c31='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");}
                elseif ($i == 2) {$queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET c32='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");}
                elseif ($i == 3) {$queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET c33='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");}
            } elseif ($jumlahCluster == 2) {
                if ($i == 1) {$queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET c21='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");}
                elseif ($i == 2) {$queryUpdateFirstCent = mysqli_query($connect, "UPDATE allcentroid SET c22='$k1|$k2|$k3|$k4|$k5|$k6' WHERE centroid='first'");}
            }
        }
        echo "<br><br>";

        // Iterasi algoritma K-Means.
        $iterasi = true;
        $jmlIterasi = 0;
        // Menghapus data jarak sebelumnya.
        if ($jumlahCluster == 2) {$queryDelDist = mysqli_query($connect, "UPDATE jarak_akhir SET j21='NULL', j22='NULL'");}
        elseif ($jumlahCluster == 3) {$queryDelDist = mysqli_query($connect, "UPDATE jarak_akhir SET j31='NULL', j32='NULL', j33='NULL'");}
        elseif ($jumlahCluster == 4) {$queryDelDist = mysqli_query($connect, "UPDATE jarak_akhir SET j41='NULL', j42='NULL', j43='NULL', j44='NULL'");}

        $firstDistance = 0;
        while ($iterasi) {
            $firstDistance++;
            for ($i = 1; $i <= $jumlahCluster; $i++) {
                // Menghitung jarak setiap data ke centroid yang ada.
                $queryGetDistance[$i] = mysqli_query($connect, "SELECT * FROM $tabel");
                $distance[$i] = getDistance(
                    $queryGetDistance[$i],
                    number_format($centroid[$i][1], 5),
                    number_format($centroid[$i][2], 5),
                    number_format($centroid[$i][3], 5),
                    number_format($centroid[$i][4], 5),
                    number_format($centroid[$i][5], 5),
                    number_format($centroid[$i][6], 5)
                );
                
                $j = 1;
                $queryPendforDist = mysqli_query($connect, "SELECT * FROM penduduk");
                // Menyimpan nilai jarak ke database.
                while ($row = mysqli_fetch_assoc($queryPendforDist)) {
                    $value = number_format($distance[$i][$j], 5);
                    if ($jumlahCluster == 4) {
                        $querySelectDist[$j] = mysqli_query($connect, "SELECT * FROM jarak_akhir");
                        $x = 1;
                        while ($rowDist = mysqli_fetch_assoc($querySelectDist[$j])) { $tampungNumDist[$x] = $rowDist['num']; $x++; }
                        if ($firstDistance == 1) { // Iterasi pertama disimpan di tabel 'jarak_awal'.
                            if (in_array($j, $tampungNumDist)) { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET id_pend='$row[id_pend]', j41='$value' WHERE num='$j'");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET id_pend='$row[id_pend]', j42='$value' WHERE num='$j'");} elseif ($i == 3) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET id_pend='$row[id_pend]', j43='$value' WHERE num='$j'");} elseif ($i == 4) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET id_pend='$row[id_pend]', j44='$value' WHERE num='$j'");}}
                            else { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend, j41) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend, j42) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 3) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend, j43) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 4) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend, j44) VALUES ('$j','$row[id_pend]','$value')");}}
                        } else { // Iterasi selanjutnya disimpan di tabel 'jarak_akhir'.
                             if (in_array($j, $tampungNumDist)) { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET id_pend='$row[id_pend]', j41='$value' WHERE num='$j'");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET id_pend='$row[id_pend]', j42='$value' WHERE num='$j'");} elseif ($i == 3) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET id_pend='$row[id_pend]', j43='$value' WHERE num='$j'");} elseif ($i == 4) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET id_pend='$row[id_pend]', j44='$value' WHERE num='$j'");}}
                             else { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend, j41) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend, j42) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 3) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend, j43) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 4) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend, j44) VALUES ('$j','$row[id_pend]','$value')");}}
                        }
                    } elseif ($jumlahCluster == 3) { // Logika serupa untuk 3 cluster.
                        $querySelectDist[$j] = mysqli_query($connect, "SELECT * FROM jarak_akhir");
                        $x = 1;
                        while ($rowDist = mysqli_fetch_assoc($querySelectDist[$j])) { $tampungNumDist[$x] = $rowDist['num']; $x++; }
                        if ($firstDistance == 1) {
                            if (in_array($j, $tampungNumDist)) { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET id_pend='$row[id_pend]', j31='$value' WHERE num='$j'");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET id_pend='$row[id_pend]', j32='$value' WHERE num='$j'");} elseif ($i == 3) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET id_pend='$row[id_pend]', j33='$value' WHERE num='$j'");}}
                            else { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend, j31) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend, j32) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 3) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend, j33) VALUES ('$j','$row[id_pend]','$value')");}}
                        } else {
                            if (in_array($j, $tampungNumDist)) { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET id_pend='$row[id_pend]', j31='$value' WHERE num='$j'");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET id_pend='$row[id_pend]', j32='$value' WHERE num='$j'");} elseif ($i == 3) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET id_pend='$row[id_pend]', j33='$value' WHERE num='$j'");}}
                            else { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend, j31) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend, j32) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 3) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend, j33) VALUES ('$j','$row[id_pend]','$value')");}}
                        }
                    } elseif ($jumlahCluster == 2) { // Logika serupa untuk 2 cluster.
                        $querySelectDist[$j] = mysqli_query($connect, "SELECT * FROM jarak_akhir");
                        $x = 1;
                        while ($rowDist = mysqli_fetch_assoc($querySelectDist[$j])) { $tampungNumDist[$x] = $rowDist['num']; $x++; }
                        if ($firstDistance == 1) {
                            if (in_array($j, $tampungNumDist)) { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET id_pend='$row[id_pend]', j21='$value' WHERE num='$j'");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_awal SET id_pend='$row[id_pend]', j22='$value' WHERE num='$j'");}}
                            else { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend, j21) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_awal (num, id_pend, j22) VALUES ('$j','$row[id_pend]','$value')");}}
                        } else {
                            if (in_array($j, $tampungNumDist)) { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET id_pend='$row[id_pend]', j21='$value' WHERE num='$j'");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "UPDATE jarak_akhir SET id_pend='$row[id_pend]', j22='$value' WHERE num='$j'");}}
                            else { if ($i == 1) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend, j21) VALUES ('$j','$row[id_pend]','$value')");} elseif ($i == 2) {$queryInsertDist = mysqli_query($connect, "INSERT INTO jarak_akhir (num, id_pend, j22) VALUES ('$j','$row[id_pend]','$value')");}}
                        }
                    }
                    $j++;
                }
            }
            
            // Mengelompokkan ulang data berdasarkan jarak terdekat.
            $m = array(1, 1, 1, 1, 1, 1);
            $newCluster[1] = $newCluster[2] = $newCluster[3] = $newCluster[4] = $newCluster[5] = $newCluster[6] = array();
            for ($j = 1; $j <= $count; $j++) {
                if ($jumlahCluster == 2) {
                    if ($distance[1][$j] < $distance[2][$j]) {$newCluster[1][$m[1]] = $j; $m[1]++;}
                    elseif ($distance[2][$j] < $distance[1][$j]) {$newCluster[2][$m[2]] = $j; $m[2]++;}
                } elseif ($jumlahCluster == 3) {
                    if ($distance[1][$j] < $distance[2][$j] && $distance[1][$j] < $distance[3][$j]) {$newCluster[1][$m[1]] = $j; $m[1]++;}
                    elseif ($distance[2][$j] < $distance[1][$j] && $distance[2][$j] < $distance[3][$j]) {$newCluster[2][$m[2]] = $j; $m[2]++;}
                    elseif ($distance[3][$j] < $distance[1][$j] && $distance[3][$j] < $distance[2][$j]) {$newCluster[3][$m[3]] = $j; $m[3]++;}
                } elseif ($jumlahCluster == 4) {
                    if ($distance[1][$j] < $distance[2][$j] && $distance[1][$j] < $distance[3][$j] && $distance[1][$j] < $distance[4][$j]) {$newCluster[1][$m[1]] = $j; $m[1]++;}
                    elseif ($distance[2][$j] < $distance[1][$j] && $distance[2][$j] < $distance[3][$j] && $distance[2][$j] < $distance[4][$j]) {$newCluster[2][$m[2]] = $j; $m[2]++;}
                    elseif ($distance[3][$j] < $distance[1][$j] && $distance[3][$j] < $distance[2][$j] && $distance[3][$j] < $distance[4][$j]) {$newCluster[3][$m[3]] = $j; $m[3]++;}
                    elseif ($distance[4][$j] < $distance[1][$j] && $distance[4][$j] < $distance[2][$j] && $distance[4][$j] < $distance[3][$j]) {$newCluster[4][$m[4]] = $j; $m[4]++;}
                }
            }

            // Menghitung ulang centroid baru.
            for ($i = 1; $i <= $jumlahCluster; $i++) {
                $t[$i] = count($newCluster[$i]);
                $querys[$i] = mysqli_query($connect, "SELECT * FROM $tabel");
                list($newCentroid[$i][1], $newCentroid[$i][2], $newCentroid[$i][3], $newCentroid[$i][4], $newCentroid[$i][5], $newCentroid[$i][6]) = getCentroids($newCluster[$i], $t[$i], $querys[$i]);
                $kLast1[$i] = number_format($newCentroid[$i][1], 5);
                $kLast2[$i] = number_format($newCentroid[$i][2], 5);
                $kLast3[$i] = number_format($newCentroid[$i][3], 5);
                $kLast4[$i] = number_format($newCentroid[$i][4], 5);
                $kLast5[$i] = number_format($newCentroid[$i][5], 5);
                $kLast6[$i] = number_format($newCentroid[$i][6], 5);
            }

            // Memeriksa konvergensi: apakah centroid tidak berubah lagi.
            $pindah = 0;
            for ($i = 1; $i <= $jumlahCluster; $i++) {
                if (
                    $centroid[$i][1] != $newCentroid[$i][1] || $centroid[$i][2] != $newCentroid[$i][2] || $centroid[$i][3] != $newCentroid[$i][3]
                    || $centroid[$i][4] != $newCentroid[$i][4] || $centroid[$i][5] != $newCentroid[$i][5] || $centroid[$i][6] != $newCentroid[$i][6]
                ) {
                    $pindah++;
                }
                $centroid[$i][1] = $newCentroid[$i][1];
                $centroid[$i][2] = $newCentroid[$i][2];
                $centroid[$i][3] = $newCentroid[$i][3];
                $centroid[$i][4] = $newCentroid[$i][4];
                $centroid[$i][5] = $newCentroid[$i][5];
                $centroid[$i][6] = $newCentroid[$i][6];
            }
            if ($pindah == 0) { $iterasi = false; } // Jika tidak ada data yang berpindah, iterasi berhenti.

            $jmlIterasi++;
        }

        // Menyimpan nilai centroid akhir ke tabel 'allcentroid'.
        for ($i = 1; $i <= $jumlahCluster; $i++) {
            if ($jumlahCluster == 4) {
                if ($i == 1) {$queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET c41='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]' WHERE centroid='last'");}
                elseif ($i == 2) {$queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET c42='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]' WHERE centroid='last'");}
                elseif ($i == 3) {$queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET c43='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]' WHERE centroid='last'");}
                elseif ($i == 4) {$queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET c44='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]' WHERE centroid='last'");}
            } elseif ($jumlahCluster == 3) {
                if ($i == 1) {$queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET c31='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]' WHERE centroid='last'");}
                elseif ($i == 2) {$queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET c32='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]' WHERE centroid='last'");}
                elseif ($i == 3) {$queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET c33='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]' WHERE centroid='last'");}
            } elseif ($jumlahCluster == 2) {
                if ($i == 1) {$queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET c21='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]' WHERE centroid='last'");}
                elseif ($i == 2) {$queryUpdateLastCent = mysqli_query($connect, "UPDATE allcentroid SET c22='$kLast1[$i]|$kLast2[$i]|$kLast3[$i]|$kLast4[$i]|$kLast5[$i]|$kLast6[$i]' WHERE centroid='last'");}
            }
        }
    } elseif ($notif == 'validasi') { // Bagian ini melakukan validasi cluster menggunakan Silhouette Coefficient.
        // Proses ini mengambil data cluster yang sudah terbentuk dan menghitung nilai Silhouette Coefficient.
        $querySelPend = mysqli_query($connect, "SELECT * FROM penduduk");
        $u = array(1, 1, 1, 1, 1, 1);
        while ($row = mysqli_fetch_assoc($querySelPend)) {
            if ($option == 2) {
                if ($row['2clust'] == 1) {$newCluster[1][$u[1]] = $row['id_pend']; $u[1]++;} elseif ($row['2clust'] == 2) {$newCluster[2][$u[2]] = $row['id_pend']; $u[2]++;}
            } elseif ($option == 3) {
                if ($row['3clust'] == 1) {$newCluster[1][$u[1]] = $row['id_pend']; $u[1]++;} elseif ($row['3clust'] == 2) {$newCluster[2][$u[2]] = $row['id_pend']; $u[2]++;} elseif ($row['3clust'] == 3) {$newCluster[3][$u[3]] = $row['id_pend']; $u[3]++;}
            } elseif ($option == 4) {
                if ($row['4clust'] == 1) {$newCluster[1][$u[1]] = $row['id_pend']; $u[1]++;} elseif ($row['4clust'] == 2) {$newCluster[2][$u[2]] = $row['id_pend']; $u[2]++;} elseif ($row['4clust'] == 3) {$newCluster[3][$u[3]] = $row['id_pend']; $u[3]++;} elseif ($row['4clust'] == 4) {$newCluster[4][$u[4]] = $row['id_pend']; $u[4]++;}
            }
        }
        
        // Menghitung Nilai A (jarak rata-rata ke anggota klaster yang sama).
        $queryA = mysqli_query($connect, "SELECT * FROM $tabel");
        $nilaiA = getA($queryA, $newCluster);
        $nilaiRataA = 0;
        for ($i = 1; $i <= mysqli_num_rows($queryA); $i++) {
            $nilaiRataA += $nilaiA[$i];
            // Menyimpan nilai A ke database.
            if ($option == 4) {$querySelecDetSil[$j] = mysqli_query($connect, "SELECT * FROM det_sil"); $x=1; while($rowDetSil=mysqli_fetch_assoc($querySelecDetSil[$j])){ $tampungIdPendDetSil[$x]=$rowDetSil['id_ds']; $x++;} if(in_array($i,$tampungIdPendDetSil)){ $queryInsertDetSil=mysqli_query($connect, "UPDATE det_sil SET id_pend='$i', a4='$nilaiA[$i]' WHERE id_ds='$i'");} else{ $queryInsertDetSil=mysqli_query($connect, "INSERT INTO det_sil (id_ds, id_pend, a4) VALUES ('$i','$i','$nilaiA[$i]')");}}
            elseif ($option == 3) {$querySelecDetSil[$j] = mysqli_query($connect, "SELECT * FROM det_sil"); $x=1; while($rowDetSil=mysqli_fetch_assoc($querySelecDetSil[$j])){ $tampungIdPendDetSil[$x]=$rowDetSil['id_ds']; $x++;} if(in_array($i,$tampungIdPendDetSil)){ $queryInsertDetSil=mysqli_query($connect, "UPDATE det_sil SET id_pend='$i', a3='$nilaiA[$i]' WHERE id_ds='$i'");} else{ $queryInsertDetSil=mysqli_query($connect, "INSERT INTO det_sil (id_ds, id_pend, a3) VALUES ('$i','$i','$nilaiA[$i]')");}}
            elseif ($option == 2) {$querySelecDetSil[$j] = mysqli_query($connect, "SELECT * FROM det_sil"); $x=1; while($rowDetSil=mysqli_fetch_assoc($querySelecDetSil[$j])){ $tampungIdPendDetSil[$x]=$rowDetSil['id_ds']; $x++;} if(in_array($i,$tampungIdPendDetSil)){ $queryInsertDetSil=mysqli_query($connect, "UPDATE det_sil SET id_pend='$i', a2='$nilaiA[$i]' WHERE id_ds='$i'");} else{ $queryInsertDetSil=mysqli_query($connect, "INSERT INTO det_sil (id_ds, id_pend, a2) VALUES ('$i','$i','$nilaiA[$i]')");}}
        }
        $nilaiRataA = $nilaiRataA / mysqli_num_rows($queryA);
        
        // Menghitung Nilai B (jarak rata-rata ke anggota klaster terdekat).
        $queryB = mysqli_query($connect, "SELECT * FROM $tabel");
        $nilaiB = getB($queryB, $newCluster);
        $nilaiRataB = 0;
        for ($i = 1; $i <= mysqli_num_rows($queryB); $i++) {
            if ($nilaiB[$i] != 9999) {$nilaiRataB += $nilaiB[$i];}
            // Menyimpan nilai B ke database.
            if ($option == 4) {$querySelecDetSil[$j] = mysqli_query($connect, "SELECT * FROM det_sil"); $x=1; while($rowDetSil=mysqli_fetch_assoc($querySelecDetSil[$j])){ $tampungIdPendDetSil[$x]=$rowDetSil['id_ds']; $x++;} if(in_array($i,$tampungIdPendDetSil)){ $queryInsertDetSil=mysqli_query($connect, "UPDATE det_sil SET id_pend='$i', b4='$nilaiB[$i]' WHERE id_ds='$i'");} else{ $queryInsertDetSil=mysqli_query($connect, "INSERT INTO det_sil (id_ds, id_pend, b4) VALUES ('$i','$i','$nilaiB[$i]')");}}
            elseif ($option == 3) {$querySelecDetSil[$j] = mysqli_query($connect, "SELECT * FROM det_sil"); $x=1; while($rowDetSil=mysqli_fetch_assoc($querySelecDetSil[$j])){ $tampungIdPendDetSil[$x]=$rowDetSil['id_ds']; $x++;} if(in_array($i,$tampungIdPendDetSil)){ $queryInsertDetSil=mysqli_query($connect, "UPDATE det_sil SET id_pend='$i', b3='$nilaiB[$i]' WHERE id_ds='$i'");} else{ $queryInsertDetSil=mysqli_query($connect, "INSERT INTO det_sil (id_ds, id_pend, b3) VALUES ('$i','$i','$nilaiB[$i]')");}}
            elseif ($option == 2) {$querySelecDetSil[$j] = mysqli_query($connect, "SELECT * FROM det_sil"); $x=1; while($rowDetSil=mysqli_fetch_assoc($querySelecDetSil[$j])){ $tampungIdPendDetSil[$x]=$rowDetSil['id_ds']; $x++;} if(in_array($i,$tampungIdPendDetSil)){ $queryInsertDetSil=mysqli_query($connect, "UPDATE det_sil SET id_pend='$i', b2='$nilaiB[$i]' WHERE id_ds='$i'");} else{ $queryInsertDetSil=mysqli_query($connect, "INSERT INTO det_sil (id_ds, id_pend, b2) VALUES ('$i','$i','$nilaiB[$i]')");}}
        }
        $nilaiRataB = $nilaiRataB / mysqli_num_rows($queryB);
        
        // Menghitung Silhouette Coefficient (S).
        $silhout = silhouette($nilaiA, $nilaiB);
        $nilaiRataS = 0;
        for ($i = 1; $i <= count($silhout); $i++) {
            $nilaiRataS += $silhout[$i];
            // Menyimpan nilai S ke database.
            if ($option == 4) {$queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil SET s4='$silhout[$i]' WHERE id_ds='$i'");}
            elseif ($option == 3) {$queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil SET s3='$silhout[$i]' WHERE id_ds='$i'");}
            elseif ($option == 2) {$queryInsertDetSil = mysqli_query($connect, "UPDATE det_sil SET s2='$silhout[$i]' WHERE id_ds='$i'");}
        }
        $nilaiRataS = $nilaiRataS / ($i - 1);
        if ($option == 2) {$queryInsertSil = mysqli_query($connect, "UPDATE sil_coe SET pow_2clust='$nilaiRataS' WHERE id_sil='1'");}
        elseif ($option == 3) {$queryInsertSil = mysqli_query($connect, "UPDATE sil_coe SET pow_3clust='$nilaiRataS' WHERE id_sil='1'");}
        elseif ($option == 4) {$queryInsertSil = mysqli_query($connect, "UPDATE sil_coe SET pow_4clust='$nilaiRataS' WHERE id_sil='1'");}
    }
} else {
    echo "ERROR";
}
?>