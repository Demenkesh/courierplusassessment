<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if !mso]><!-->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!--<![endif]-->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $mail['subject'] }}</title>
<style type="text/css">
    .button {
        background-color: #b99470;
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
    }

    .button3 {
        width: 100%;
    }
</style>
<style type="text/css">
    .ReadMsgBody {
        width: 100%;
        background-color: #F8F8FC;
    }

    .ExternalClass {
        width: 100%;
        background-color: #F8F8FC;
    }

    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {
        line-height: 100%;
    }

    html {
        width: 100%;
    }

    body {
        -webkit-text-size-adjust: none;
        -ms-text-size-adjust: none;
        margin: 0;
        padding: 0;
    }

    table {
        border-spacing: 0;
        table-layout: fixed;
        margin: 0 auto;
        border-collapse: collapse;
    }

    table table table {
        table-layout: auto;
    }

    .yshortcuts a {
        border-bottom: none !important;
    }

    img:hover {
        opacity: 0.9 !important;
    }

    a {
        color: #0087ff;
        text-decoration: none;
    }

    .textbutton a {
        font-family: 'open sans', arial, sans-serif !important;
    }

    .btn-link a {
        color: #F8F8FC !important;
    }

    @media only screen and (max-width: 480px) {
        body {
            width: auto !important;
        }

        *[class="table-inner"] {
            width: 90% !important;
            text-align: center !important;
        }

        *[class="table-full"] {
            width: 100% !important;
            text-align: center !important;
        }

        /* image */
        img[class="img1"] {
            width: 100% !important;
            height: auto !important;
        }
    }
</style>

<table bgcolor="#F8F8FC" width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tbody>
        <tr>
            <td height="50"></td>
        </tr>
        <tr>
            <td align="center" style="text-align:center;vertical-align:top;font-size:0;">
                <table align="center" border="0" cellpadding="0" cellspacing="0">
                    <tbody>
                        <tr>
                            <td align="center" width="600">
                                <!--header-->
                                <table class="table-inner" width="95%" border="0" align="center" cellpadding="0"
                                    cellspacing="0">
                                </table>
                                <!--end header-->
                                <table class="table-inner" width="95%" border="0" cellspacing="0"
                                    cellpadding="0">
                                    <tbody>
                                        <tr>
                                            <td bgcolor="#FFFFFF" align="center"
                                                style="text-align:center;vertical-align:top;font-size:0;">
                                                <table align="center" width="90%" border="0" cellspacing="0"
                                                    cellpadding="0">
                                                    <tbody>
                                                        <tr>
                                                            <td height="35">
                                                                <blockquote
                                                                    style="margin: 0 0 0 40px; border: none; padding: 0px;">
                                                                    <br>
                                                                </blockquote>
                                                            </td>
                                                        </tr>
                                                        <!--logo-->
                                                        <tr>
                                                            <td align="center" style="vertical-align:top;font-size:0;">
                                                            </td>
                                                        </tr>
                                                        <!--end logo-->

                                                        <tr>
                                                            <td height="10"><img src="{{ $mail['imagelogo'] }}"
                                                                    width="200"></td>
                                                        </tr>
                                                        <!--headline-->

                                                        <tr>
                                                            <td align="center"
                                                                style="font-family: 'Open Sans', Arial, sans-serif; font-size: 22px;color:#333333;font-weight: bold; text-align: center;">
                                                                <br>
                                                                @if (!empty($mail['subject']))
                                                                    {{ $mail['subject'] }}.
                                                                @endif

                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td height="20"></td>
                                                        </tr>

                                                        <tr>
                                                            <td align="center"
                                                                style="font-family: 'Open Sans', Arial, sans-serif; font-size: 22px;color:#414a51;font-weight: bold; text-align: justify;">
                                                                <br>
                                                                @if (!empty($mail['name']))
                                                                    Dear, {{ $mail['name'] }}.!
                                                                @else
                                                                    Dear,!
                                                                @endif

                                                            </td>
                                                        </tr>
                                                        <!--end headline-->
                                                        <tr>
                                                            <td align="center"
                                                                style="text-align:center;vertical-align:top;font-size:0;">
                                                                <table width="40" border="0" align="center"
                                                                    cellpadding="0" cellspacing="0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td height="20"
                                                                                style=" border-bottom:3px solid ##b99470;">
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td height="20"></td>
                                                        </tr>
                                                        <!--content-->

                                                        @if (!empty($mail['textmessagefirst']))
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-family: 'Open sans', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px; text-align: left; ">
                                                                    {!! $mail['textmessagefirst'] ?? '' !!} </td>
                                                            </tr>
                                                        @endif

                                                        @if (!empty($mail['textmessagesecond']))
                                                            <tr>
                                                                <td height="20"></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-family: 'Open sans', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px; text-align: left; ">
                                                                    {{ $mail['textmessagesecond'] ?? '' }}
                                                                </td>
                                                            </tr>
                                                        @endif

                                                        @if (!empty($mail['checkin']))
                                                            <tr>
                                                                <td height="20"></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-family: 'Open sans', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px; text-align: left; ">
                                                                    {{ $mail['checkin'] ?? '' }}
                                                                </td>
                                                            </tr>
                                                        @endif

                                                        @if (!empty($mail['code']))
                                                            <tr>
                                                                <td height="20"></td>
                                                            </tr>
                                                            <td align="center"
                                                                style="font-family:'Open sans',Arial,sans-serif;color:#7f8c8d;font-size:16px;line-height:28px; text-align:center">
                                                                <button
                                                                    style="width: 100%;background-color: #b99470;border: none;color: white;padding: 15px 32px; text-align: center; text-decoration: none;display: inline-block;font-size: 16px;margin: 4px 2px;"
                                                                    class="button button3">
                                                                    {{ $mail['code'] ?? '' }}
                                                                </button>
                                                            </td>
                                                        @endif

                                                        <tr>
                                                            <td align="center"
                                                                style="text-align:center; vertical-align:top; font-size:0;">
                                                                <table width="40" border="0" align="center"
                                                                    cellpadding="0" cellspacing="0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <!-- Spacer for padding above the border -->
                                                                            <td height="20"
                                                                                style="border-bottom:3px solid #b99470;">
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        @php
                                                            $toEmail = env('MAIL_USERNAME');
                                                            $AppName = env('APP_NAME');
                                                            $Address = env('HOME_ADDRESS');
                                                            $city = env('HOTEL_CITY');
                                                            $phone = env('PHONE');
                                                        @endphp

                                                        @if (!empty($mail['homeaddress']))
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-family: 'Open sans', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px; text-align: left; ">
                                                                    Hotel Name : {{ $AppName }}
                                                                </td>
                                                            </tr>


                                                            <tr>
                                                                <td height="20"></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-family: 'Open sans', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px; text-align: left; ">
                                                                    Hotel Address :
                                                                    {{ $Address }}
                                                                </td>
                                                            </tr>


                                                            <tr>
                                                                <td height="20"></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-family: 'Open sans', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px; text-align: left; ">
                                                                    City : {{ $city }}
                                                                </td>
                                                            </tr>


                                                            <tr>
                                                                <td height="20"></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-family: 'Open sans', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px; text-align: left; ">
                                                                    Hotel Phone Number :
                                                                    {{ $phone }}
                                                                </td>
                                                            </tr>


                                                            <tr>
                                                                <td height="20"></td>
                                                            </tr>
                                                            <tr>
                                                                <td align="left"
                                                                    style="font-family: 'Open sans', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px; text-align: left; ">
                                                                    Hotel Email Address :
                                                                    {{ $toEmail }}
                                                                </td>
                                                            </tr>
                                                        @endif

                                                        <!--end content-->
                                                        <tr>
                                                            <td height="40">hh</td>
                                                        </tr>

                                                        <tr>
                                                            <td align="left"
                                                                style="font-family: 'Open sans', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px; text-align: left; ">
                                                                If you need further assistance, our dedicate team is
                                                                available to help you every step of the way. Please
                                                                send
                                                                us an email at <a href="mailto:{{ $toEmail }}">
                                                                    <span
                                                                        class="theme-color">{{ $toEmail }}</span></a>.
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td height="40">hh</td>
                                                        </tr>

                                                        <tr>
                                                            <td align="left"
                                                                style="font-family: 'Open sans', Arial, sans-serif; color:#7f8c8d; font-size:16px; line-height: 28px; text-align: left; ">
                                                                Best regards,
                                                                <br />
                                                                {{ $AppName }} Support Team
                                                            </td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="10"></td>
                                        </tr>
                                        <tr>
                                            <td height="45" align="center" bgcolor="#FFFFFF"
                                                style="border-bottom-left-radius:6px;border-bottom-right-radius:6px;">
                                                <table align="center" width="90%" border="0" cellspacing="0"
                                                    cellpadding="0">
                                                    <tbody>
                                                        <tr>
                                                            <td height="10"><br><br><br></td>
                                                        </tr>
                                                        <!--preference-->
                                                        <tr>
                                                            <td class="preference-link" align="center"
                                                                style="font-family: 'Open sans', Arial, sans-serif; color:#95a5a6; font-size:14px;">
                                                                This email was sent by <span
                                                                    class="theme-color">{{ $toEmail }}</span>.
                                                                If
                                                                you'd rather not receive this kind of email, Don’t
                                                                want
                                                                any more emails from Notable?
                                                                <span>Unsubscribe</span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <!--end preference-->
                                                        <tr>
                                                            <td height="10"></td>
                                                        </tr>

                                                        <tr>
                                                            <td class="preference-link" align="center"
                                                                style="font-family: 'Open sans', Arial, sans-serif; color:#95a5a6; font-size:14px;">
                                                                {{ $Address }}
                                                                <br />
                                                                © {{ now()->year }} {{ $AppName }}
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td height="60"></td>
        </tr>
    </tbody>
</table>
