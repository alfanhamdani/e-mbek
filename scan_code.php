<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan QR Code</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
</head>
<body>
    <h1>Scan QR Code</h1>
    <div id="reader" style="width: 300px;"></div>
    <p id="result"></p>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            document.getElementById('result').innerText = "QR Code ditemukan: " + decodedText;
            window.location.href = 'show.php?id_hewan=' + decodedText;
        }

        function onScanFailure(error) {
            console.warn(`Scan gagal: ${error}`);
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: 250 }
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
</body>
</html>


