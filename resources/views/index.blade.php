<!doctype html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta http-equiv="cache-control" content="public">
    <meta name="description" content="A command line website.">
    <meta name="keywords" content="chimpcom, command line website, shell, javascript command line, cli">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta property="og:title" content="Chimpcom">
    <meta property="og:type" content="website">
    <meta property="og:description" content="A command line website.">
    <meta property="og:image" content="">
    <meta property="og:url" content="https://deviouschimp.co.uk">
    <meta property="og:locale" content="en-GB">
    <meta property="og:site_name" content="Chimpcom">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@mr_chimp">
    <meta name="twitter:title" content="Chimpcom">
    <meta name="twitter:description" content="A command line website.">
    <title>Chimpcom</title>
    <link rel="me" href="https://mastodon.social/@mr_chimp" />
    <link rel="apple-touch-icon" sizes="57x57" href="/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="/favicon-194x194.png" sizes="194x194">
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#111112">
    <meta name="msapplication-TileColor" content="#111112">
    <meta name="msapplication-TileImage" content="/mstile-144x144.png">
    <meta name="theme-color" content="#111112">
    <link nonce="{{ csp_nonce() }}" href='{{ mix('css/main.css') }}' rel='stylesheet' type='text/css'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Mono:ital,wght@0,400;0,700;1,400&display=swap"
        rel="stylesheet">
</head>

<body>
    <noscript>Enable JavaScript.</noscript>
    <div id="cmd"></div>
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <script nonce="{{ csp_nonce() }}" type="text/javascript" src="{{ mix('js/main.js') }}"></script>
</body>

</html>
