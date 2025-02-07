<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>myMANIT</title>
    <link rel="shortcut icon" href="https://upload.wikimedia.org/wikipedia/en/4/4f/Maulana_Azad_National_Institute_of_Technology_Logo.png" type="image/x-icon" />
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="https://upload.wikimedia.org/wikipedia/en/4/4f/Maulana_Azad_National_Institute_of_Technology_Logo.png" alt="Logo">
            <h1>
                <span>myMANIT</span>
            </h1>
        </div>

        <form method="post">
            <div class="scholar-no" id="scholarSection">
                
                <div class="scholar_input">
                    <label for="scholar-number" class="form-label">Scholar Number:</label>
                    <input type="text" class="scholar-input" name="scholarNumber" id="scholarnum">
                </div>
                
                <div id="otpSection" class="otp-section">
                    <label for="otp-entry" class="form-label">OTP:</label>
                    <input type="text" id="otpInput" class="otpInput" name="otp">
                    <button class="verify" name="otpVerify">Verify</button>
                </div>
                
                <button id="submit" class="submit_button" name="submit">Continue</button>
            
            </div>
        </form>
    </div>
</body>
</html>
