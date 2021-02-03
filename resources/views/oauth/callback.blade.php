<html>
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
    <script>
        window.opener.postMessage(@json($payload), "{{ env('CLIENT_BASE_URL') }}")
        window.close()
    </script>
</head>
<body>
</body>
</html>
