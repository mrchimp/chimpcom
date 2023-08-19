<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Diary Graphs</title>
    <style>
        body,
        html {
            width: 100%;
            height: 100%;
            padding: 0;
        }

        * {
            box-sizing: border-box;
        }

        .wrapper {
            align-items: center;
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: center;
        }

        .domain {
            opacity: 0;
        }

        .tick line {
            opacity: 0.2;
        }

        @media screen and (prefers-color-scheme: dark) {
            body {
                background-color: black;
                color: white;
            }
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <svg width="960" height="500" id="graph"></svg>
        <svg width="960" height="0" id="legend"></svg>
    </div>
    <script src="{{ mix('js/graphs.js') }}"></script>
</body>

</html>
