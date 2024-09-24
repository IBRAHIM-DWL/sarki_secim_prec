<?php
$servername = "localhost";
$username = "root";
$password = "mysql22";
$dbname = "sarki_secim";

session_start();

// Bağlantıyı oluştur
$baglantiMysSql = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($baglantiMysSql->connect_error) {
    die("Bağlantı hatası: " . $baglantiMysSql->connect_error);
}

$masa_no = isset($_GET['masa_no']) ? $_GET['masa_no'] : '';


if (isset($_SERVER['REQUEST_METHOD'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $masa_no = $_POST["masa_no"];
        $sarki_adi = $_POST["sarki_adi"];
        
        $stmt = $baglantiMysSql->prepare("SELECT COUNT(*) FROM sarkilar WHERE masa_no=? and calindi=0");
        $stmt->bind_param("i", $masa_no); 
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        if($count>2)
        {
            $stmt->close();
            $stmt = $baglantiMysSql->prepare("SELECT id FROM sarkilar WHERE masa_no=?");
            $stmt->bind_param("i", $masa_no); 
            $stmt->execute();
            $stmt->bind_result($sira);
            $stmt->fetch();
            $stmt->close();
            $stmt = $baglantiMysSql->prepare("SELECT id FROM sarkilar order by id asc");
            $stmt->execute();
            $stmt->bind_result($sirafarki);
            $stmt->fetch();
            $stmt->close();
            $sirafarki=$sira-$sirafarki+1;
            echo "Sırada bekleyen şarkınız vardır. Sıranız---> $sirafarki";               
        }
        else
        {
            $stmt->close();
            $stmt = $baglantiMysSql->prepare("INSERT INTO sarkilar (masa_no, sarki_adi) VALUES (?, ?)");
            $stmt->bind_param("ss", $masa_no, $sarki_adi);
        
            if ($stmt->execute()) 
            {
                echo "Şarkı seçimi başarılı!";
            } else {
                echo "Hata: " . $stmt->error;
            }
        }
    }
} else {
    echo "web sunucusu üzerinden çalıştırılmalıdır.";
}




// Bağlantıyı kapat
$baglantiMysSql->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Şarkı Seçimi</title>
</head>
<body>
    <form method="post" action="sarki_sec.php?masa_no=<?php echo $masa_no; ?>">
        Masa No: <input type="text" name="masa_no" value="<?php echo $masa_no; ?>" readonly><br>
        Şarkı Adı: <input type="text" name="sarki_adi"><br>
        <input type="submit" value="Şarkıyı Seç">
    </form>
</body>
</html>