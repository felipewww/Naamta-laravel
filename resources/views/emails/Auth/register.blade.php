<html>
    <body>
    Welcome to Naamta!
    <br>
        Please, confirm your register <a href="{{ asset('/register/confirmation/'.$token) }}">clicking here</a>.
    <br>
    If you cannot click, copy the link below and paste in your browser.
    <a href="#">{{ asset('/register/confirmation/'.$token) }}</a>

    Thanks,
    <br>
    Naamta Developers.
    </body>
</html>
