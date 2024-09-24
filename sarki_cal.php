<!DOCTYPE html>
<html>
<head>
    <title>Şarkı Çalma</title>
</head>
<body>
<?php
$servername = "localhost";
$username = "root";
$password = "mysql22";
$dbname = "sarki_secim";
$apiKey = "XXXXXX"; 


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$sql="SELECT * FROM tblkeys";
$sonuc = mysqli_query($conn, $sql);

if ($sonuc) 
{
    $apiKey = mysqli_fetch_assoc($sonuc);
} else {
    echo "Api key bulunamadı.";
}

$sql = "SELECT * FROM sarkilar WHERE calindi = FALSE ORDER BY id ASC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $sarki_adi = $row["sarki_adi"];
        $id = $row["id"];

        // YouTube'da şarkıyı ara
        $searchUrl = "https://www.googleapis.com/youtube/v3/search?part=snippet&q=" . urlencode($sarki_adi) . "&key=" . $apiKey;
        $response = file_get_contents($searchUrl);
        $data = json_decode($response, true);
        $videoId = $data['items'][0]['id']['videoId'];

        // Şarkıyı çal
        echo "Şu an çalıyor: " . $sarki_adi;
        echo "<iframe id='player' width='560' height='315' src='https://www.youtube.com/embed/" . $videoId . "?enablejsapi=1&autoplay=1' frameborder='0' allow='autoplay; encrypted-media' allowfullscreen></iframe>";

        // Şarkıyı çalındı olarak işaretle
        $sql_update = "UPDATE sarkilar SET calindi = TRUE WHERE id = $id";
        $conn->query($sql_update);
    }
} else {
    echo "Çalınacak şarkı yok.";
}

$conn->close();
?>

    <script>
        // YouTube iframe API
        var tag = document.createElement('script');
        tag.src = "https://www.youtube.com/iframe_api";
        var firstScriptTag = document.getElementsByTagName('script')[0];
        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

        var player;
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
    }

    function onPlayerReady(event) {
        event.target.playVideo();
    }

    function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.ENDED) {
            // Şarkı bittiğinde sayfayı yeniden yükle
            window.location.reload();
        }
    }
    </script>
</body>
</html>
