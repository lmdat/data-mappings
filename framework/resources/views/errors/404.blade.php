<!DOCTYPE html>
<html>
    <head>
        <title>Be right back.</title>

        <link href='//fonts.googleapis.com/css?family=Lato:100' rel='stylesheet' type='text/css'>
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300|Roboto|Roboto+Condensed:300,400" rel="stylesheet">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
            }

            .container {
                text-align: center;
                display: table-cell;
                padding-top: 60px;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-family: 'Open Sans', sans-serif;
                font-weight:300;
                font-size: 40px;
                margin-bottom: 20px;
            }

            .error_content{
                font-family: 'Roboto Condensed', sans-serif;
                font-weight:300;
                font-size: 30px;
                color: firebrick;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">
                    Be right back.
                    <br/><small><strong>404:</strong></small>
                </div>
                <div class="error_content">
                    <strong>{{ $exception->getMessage() }}</strong>
                </div>
                
            </div>
        </div>
    </body>
</html>
