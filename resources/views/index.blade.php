<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
<meta http-equiv="cache-control" content="public">
<meta name="description" content="A command line website.">
<meta name="keywords" content="chimpcom, command line website, shell, javascript command line, cli">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=0" />
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
<link href='{{ asset(elixir('css/main.css')) }}' rel='stylesheet' type='text/css'>
@if(config('services.ga.code'))
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-28242373-1"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'UA-28242373-1');
</script>
@endif
</head>
<body>
<div id="chimpcom"></div>
<input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
<script type="text/javascript" src="{{ asset(elixir('js/main.js')) }}"></script>
</body>
</html>
