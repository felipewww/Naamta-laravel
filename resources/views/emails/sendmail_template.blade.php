<html>
    <head></head>
    <body>
        <table width="400px" style="width: 500px; border: 1px solid #c0c0c0; border-collapse: collapse; font-family: sans-serif; color: gray;">
            <tbody style="width: 400px;">
                <tr style="text-align: center; background: #ececec; color: #797979;">
                    <td style="padding: 15px;"><img src="{{ asset('/media/images/naamta_logo.png') }}" width="80px"></td>
                </tr>
                {{--<tr style="height: 20px">--}}
                    {{--<td></td>--}}
                {{--</tr>--}}
                <tr>
                    <td style="padding: 20px; padding-top: 30px;">
                        <h3 style="text-align: center">
                            @yield('title')
                        </h3>
                        @yield('content')
                        <br>
                        <br>
                        Best regards,
                        <br>
                        Naamta
                    </td>
                </tr>
                <tr style="background: #ececec; font-size: 10px; text-align: center;">
                    <td style="padding: 20px;">
                        Naamta.ca | <?= date('Y') ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </body>
</html>