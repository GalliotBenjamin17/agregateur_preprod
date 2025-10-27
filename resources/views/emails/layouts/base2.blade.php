<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        @media screen {
            @font-face {
                font-family: Colfax;
                src: url({{ asset('fonts/emails/ColfaxAIRegular.woff2') }});
                font-weight: normal;
            }

            @font-face {
                font-family: Colfax;
                src: url({{ asset('fonts/emails/Colfax-Medium.woff2') }});
                font-weight: bold;
            }
        }

        /**
         * Avoid browser level font resizing.
         * 1. Windows Mobile
         * 2. iOS / OSX
         */
        body,
        table,
        td,
        a {
            -ms-text-size-adjust: 100%; /* 1 */
            -webkit-text-size-adjust: 100%; /* 2 */
            font-family: Colfax, Helvetica, Arial, sans-serif;
        }

        /**
         * Remove extra space added to tables and cells in Outlook.
         */
        table,
        td {
            mso-table-rspace: 0pt;
            mso-table-lspace: 0pt;
        }

        /**
         * Better fluid images in Internet Explorer.
         */
        img {
            -ms-interpolation-mode: bicubic;
        }

        /**
         * Remove blue links for iOS devices.
         */
        a[x-apple-data-detectors] {
            font-family: inherit !important;
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            color: inherit !important;
            text-decoration: none !important;
        }

        /**
         * Fix centering issues in Android 4.4.
         */
        div[style*="margin: 16px 0;"] {
            margin: 0 !important;
        }

        body {
            width: 100% !important;
            height: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /**
         * Collapse table borders to avoid space between cells.
         */
        table {
            border-collapse: collapse !important;
        }

        a {
            @isset($tenant)
                color: {{ $tenant->primary_color }};
            @else
                color: #0078bb;
            @endisset
        }

        img {
            height: auto;
            line-height: 100%;
            text-decoration: none;
            border: 0;
            outline: none;
        }

        h1 {
            font-size: 26px;
            line-height: 1.4;
            font-weight: 700;
            margin: 0;
        }
        h2 {
            font-size: 24px;
            line-height: 1.4;
            font-weight: 700;
            margin: 0;
        }
        p {
            margin: 0;
        }
        p + p {
            margin-top: 16px;
        }
    </style>


</head>
<body style="background-color:#ececf1;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;width:100% ;height:100% ;padding:0 ;margin:0 ;">

<div class="preheader" style="display: none; max-width: 0; max-height: 0; overflow: hidden; font-size: 1px; line-height: 1px; color: #fff; opacity: 0;">

</div>

<!-- start body -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;border-collapse:collapse ;">
    <!-- start body block -->
    <tr>
        <td align="center" style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                <tr>
                    <td align="center" valign="top" width="600">
            <![endif]-->
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;background-color:#fff;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;border-collapse:collapse ;">
                <!-- header image -->
                <tr>
                    <td valign="top" style="padding:0 0 32px;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;">
                        <table align="left" width="100%" border="0" cellpadding="0" cellspacing="0" style="min-width:100%;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;border-collapse:collapse ;">
                            <tbody>
                            <tr>
                                <td valign="top" style="padding-top:0;padding-bottom:0;text-align:center;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;">
                                    @isset($tenant)
                                        <img align="center" alt="" src="{{ asset($tenant->email_banner) }}" width="600" height="200" style="max-width:100%;padding-bottom:0;display:inline !important;vertical-align:bottom;-ms-interpolation-mode:bicubic;height:auto;line-height:100%;text-decoration:none;border:0;outline:none;">
                                    @else
                                        <img align="center" alt="" src="{{ asset('img/emails/header.png') }}" width="600" height="200" style="max-width:100%;padding-bottom:0;display:inline !important;vertical-align:bottom;-ms-interpolation-mode:bicubic;height:auto;line-height:100%;text-decoration:none;border:0;outline:none;">
                                    @endif
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>



                <tr>
                    <td align="left" style="padding:16px 24px 0;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;">
                        <h1 style="margin:0;font-size:26px;line-height:1.4;font-weight:700;">
                            @yield('title')
                        </h1>
                    </td>
                </tr>

                <tr>
                    <td align="left" bgcolor="#ffffff" style="padding:16px 24px;font-size:16px;line-height:24px;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;">
                        @yield('content')
                    </td>
                </tr>

                @hasSection('action')
                    <tr>
                        <td align="left" bgcolor="#ffffff" style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;border-collapse:collapse ;">
                                <tr>
                                    <td align="left" bgcolor="#ffffff" style="padding:12px 24px;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;">
                                        <table border="0" cellpadding="0" cellspacing="0" style="-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;border-collapse:collapse ;">
                                            <tr>
                                                @yield('action')
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif

                <tr>
                    <td align="left" bgcolor="#ffffff" style="padding:16px 24px;font-size:16px;line-height:24px;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;">
                        <p style="margin:0;">
                            @isset($tenant)
                                {{ $tenant->name }},<br>
                                {{ $tenant->address }}
                            @else
                                Coopérative Carbone,<br>
                                1 rue Fleming, 17000, La Rochelle
                            @endisset
                        </p>
                    </td>
                </tr>

                <!-- spacer -->
                <tr><td valign="top" style="padding:9px;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;"></td></tr>

            </table>
            <!--[if (gte mso 9)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    <!-- end body block -->

    <!-- start footer -->
    <tr>
        <td align="center" style="padding:24px;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;">
            <!--[if (gte mso 9)|(IE)]>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                <tr>
                    <td align="center" valign="top" width="600">
                    <![endif]-->
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;border-collapse:collapse;font-size: 15px;">
                        Lien de désabonnement : <a href="{{ route('gdpr.hub.index') }}">Cliquez-ici</a>
                    </table>
                    <!--[if (gte mso 9)|(IE)]>
                    </td>
                </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    <!-- end footer -->

</table>
</body>
</html>
