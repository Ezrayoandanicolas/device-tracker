<!DOCTYPE html>
<html>
<head>
    <title>Please wait...</title>
    <script src="/fingerprint.js"></script>
</head>

<body>

<p>Sedang mengarahkan...</p>

<script>
// fingerprint.js akan otomatis jalan
// Kita tinggal tunggu 200–400 ms → lalu redirect
setTimeout(() => {
    window.location.href = "{{ preg_replace('/^http:/i', 'https:', $target) }}";
}, 400);
</script>
</body>
</html>
