<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Wallet - Transfer</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #764ba2;
            min-height: 100vh;
        }
    </style>
</head>
<body>
<div id="app">
    <div style="max-width: 1400px; margin: 0 auto; padding: 40px 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <transfer-form></transfer-form>
            <transaction-list></transaction-list>
        </div>
    </div>
</div>
</body>
</html>