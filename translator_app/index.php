<?php
$translatedText = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputText = $_POST['inputText'] ?? '';

    if (!empty($inputText)) {
        $apiUrl = "http://localhost:8080/TranslatorResource-1.0-SNAPSHOT/api/translator";
        $data = json_encode(["text" => $inputText]);

        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_USERPWD, "admin:password");

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = 'Connection Error: ' . curl_error($ch);
        } else {
            curl_close($ch);
            if ($httpCode == 200) {
                $responseData = json_decode($response, true);
                if (isset($responseData['choices'][0]['message']['content'])) {
                    $translatedText = $responseData['choices'][0]['message']['content'];
                } elseif (isset($responseData['translation'])) {
                    $translatedText = $responseData['translation'];
                } else {
                    $translatedText = $response; 
                }
            } elseif ($httpCode == 401) {
                $error = "Authentication Failed: Check username/password.";
            } else {
                $error = "Server Error (Code $httpCode).";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Translator | English to Darija</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Cairo:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-color: #1f2937;
            --border-color: #e5e7eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            box-sizing: border-box;
        }

        .header-area {
            text-align: center;
            margin-bottom: 35px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .header-area img {
            width: 110px;
            height: auto;
            margin-bottom: 15px;
            border-radius: 18px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .header-area img:hover {
            transform: scale(1.05);
        }

        .header-area h1 {
            margin: 0;
            color: #111;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        
        .header-area span {
            color: #6b7280;
            font-size: 16px;
            margin-top: 5px;
        }

        .translator-container {
            display: flex;
            flex-direction: row;
            gap: 20px;
            width: 100%;
            max-width: 900px;
            background: var(--card-bg);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .side {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .divider {
            width: 1px;
            background-color: var(--border-color);
            margin: 0 10px;
            display: block;
        }

        label {
            font-weight: 700;
            margin-bottom: 10px;
            display: block;
            color: var(--text-color);
        }

        textarea, .output-box {
            width: 100%;
            height: 200px;
            padding: 15px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 18px;
            resize: none;
            box-sizing: border-box;
            outline: none;
            background: #fff;
            font-family: 'Inter', sans-serif;
        }

        textarea:focus {
            border-color: var(--primary-color);
        }

        .output-box {
            background-color: #f8fafc;
            border-color: #f1f5f9;
            font-family: 'Cairo', sans-serif;
            color: #334155;
            direction: rtl;
            overflow-y: auto;
            white-space: pre-wrap;
        }
        
        .placeholder-text {
            color: #9ca3af;
            text-align: center;
            padding-top: 80px;
            font-size: 14px;
        }

        .actions { margin-top: 15px; }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        button:hover { background-color: #4338ca; }

        .error-msg {
            grid-column: 1 / -1;
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-top: 20px;
            width: 100%;
        }

        @media (max-width: 768px) {
            .translator-container { flex-direction: column; }
            .divider { width: 100%; height: 1px; margin: 20px 0; }
            textarea, .output-box { height: 150px; }
        }
    </style>
</head>
<body>

    <div class="header-area">
        <img src="logo.png" alt="Darija AI Logo">
        <h1>Darija AI</h1>
        <span>English to Moroccan Darija</span>
    </div>

    <div class="translator-container">
        
        <div class="side">
            <form method="POST" id="transForm">
                <label for="inputText">🇺🇸 English</label>
                <textarea id="inputText" name="inputText" placeholder="Enter text here..."><?php echo isset($_POST['inputText']) ? htmlspecialchars($_POST['inputText']) : ''; ?></textarea>
                
                <div class="actions">
                    <button type="submit">Translate</button>
                </div>
            </form>
        </div>

        <div class="divider"></div>

        <div class="side">
            <label>🇲🇦 Darija</label>
            <div class="output-box">
                <?php if ($translatedText): ?>
                    <?php echo nl2br(htmlspecialchars($translatedText)); ?>
                <?php else: ?>
                    <div class="placeholder-text">Translation will appear here...</div>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

    </div>

</body>
</html>