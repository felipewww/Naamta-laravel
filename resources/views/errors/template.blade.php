<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset("bower_components/bootstrap/dist/css/bootstrap.min.css") }}" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Bree+Serif" rel="stylesheet">

    <link rel="icon" type="image/png" href="media/images/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="media/images/favicon-16x16.png" sizes="16x16" />

    <style>
        body {
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABZ0RVh0Q3JlYXRpb24gVGltZQAxMC8yOS8xMiKqq3kAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzVxteM2AAABHklEQVRIib2Vyw6EIAxFW5idr///Qx9sfG3pLEyJ3tAwi5EmBqRo7vHawiEEERHS6x7MTMxMVv6+z3tPMUYSkfTM/R0fEaG2bbMv+Gc4nZzn+dN4HAcREa3r+hi3bcuu68jLskhVIlW073tWaYlQ9+F9IpqmSfq+fwskhdO/AwmUTJXrOuaRQNeRkOd5lq7rXmS5InmERKoER/QMvUAPlZDHcZRhGN4CSeGY+aHMqgcks5RrHv/eeh455x5KrMq2yHQdibDO6ncG/KZWL7M8xDyS1/MIO0NJqdULLS81X6/X6aR0nqBSJcPeZnlZrzN477NKURn2Nus8sjzmEII0TfMiyxUuxphVWjpJkbx0btUnshRihVv70Bv8ItXq6Asoi/ZiCbU6YgAAAABJRU5ErkJggg==);
            font-family: 'Bree Serif', serif;
        }
        .error-template {padding: 40px 15px;text-align: center;}
        .error-actions {margin-top:15px;margin-bottom:15px;}
        .error-actions .btn { margin-right:10px; font-family: Arial, sans-serif; font-size: 13px; }

        div#details {
            /*display: none;*/
            position: absolute;
            bottom: 0;
            background: rgb(236, 236, 236);
            width: 100%;
            left: 0;
            border-top: 1px solid #c1c1c1;
            padding: 15px;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="error-template">
                    <img src="{{asset('media/images/naamta_logo.png')}}" alt="home" class="dark-logo" />

                    <h1>Something went wrong</h1>
                    {{--<h2>404 Not Found</h2>--}}
                    <div class="error-details">
                        Sorry, an error has occured, Requested page not found!
                    </div>

                    <div class="error-actions">
                        <a href="javascript:history.back();" class="btn btn-default btn-lg">
                            <span class="glyphicon glyphicon-chevron-left"></span> Back
                        </a>
                        <a href="/" class="btn btn-primary btn-lg">
                            <span class="glyphicon glyphicon-home"></span>
                            Take Me Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div id="details">
            <div>{{ dd(get_defined_vars()) }}</div>
        </div>
    </div>
</body>