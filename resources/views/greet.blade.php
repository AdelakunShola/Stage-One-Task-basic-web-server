<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Greeting</title>
</head>
<body>
    <div id="greeting">Loading...</div>

    <script>
        // Get visitor_name from the URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const visitorName = urlParams.get('visitor_name') || 'Guest';

        fetch(`/api/greet?visitor_name=${encodeURIComponent(visitorName)}`, {
            method: 'get',
            headers: new Headers({
                'ngrok-skip-browser-warning': '69420',
            }),
        })
        .then((response) => response.json())
        .then((data) => {
            document.getElementById('greeting').innerText = JSON.stringify(data, null, 2);
        })
        .catch((err) => {
            console.error(err);
            document.getElementById('greeting').innerText = 'Failed to load greeting.';
        });
    </script>
</body>
</html>
