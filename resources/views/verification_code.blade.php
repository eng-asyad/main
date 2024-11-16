<!-- resources/views/emails/verification_code.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Verification Code</title>
</head>
<body>
    <h1>Your Verification Code</h1>
    <p>Use the following code to verify your email address:</p>
    <h2>{{ $code }}</h2>
</body>
</html>