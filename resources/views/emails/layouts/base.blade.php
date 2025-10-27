<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings xmlns:o="urn:schemas-microsoft-com:office:office">
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <style>
        td,th,div,p,a,h1,h2,h3,h4,h5,h6 {font-family: "Segoe UI", sans-serif; mso-line-height-rule: exactly;}
    </style>
    <![endif]-->
    <title>
        @yield('title')
    </title>
    <style>
        .bg-body-dark {
            background-color: #121212 !important;
        }
        .text-body-light {
            color: #F5F7FA !important;
        }
        .hover-underline:hover {
            text-decoration-line: underline !important;
        }
        .hover-opacity-80:hover {
            opacity: 0.8 !important;
        }
        .hover-text-decoration-underline:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .sm-block {
                display: block !important;
            }
            .sm-table-footer-group {
                display: table-footer-group !important;
            }
            .sm-w-full {
                width: 100% !important;
            }
            .sm-max-w-full {
                max-width: 100% !important;
            }
            .sm-p-8 {
                padding: 32px !important;
            }
            .sm-px-5 {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }
            .sm-px-4 {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }
            .sm-pb-6 {
                padding-bottom: 24px !important;
            }
            .sm-leading-5 {
                line-height: 20px !important;
            }
        }
        @media (prefers-color-scheme: dark) {
            .dark-bg-body-dark {
                background-color: #121212 !important;
            }
            .dark-bg-gray-1000 {
                background-color: #1e1e1e !important;
            }
            .dark-bg-gray-200 {
                background-color: #383838 !important;
            }
            .dark-text-gray-100 {
                color: #bdbdbd !important;
            }
            .dark-text-gray-50 {
                color: #e5e5e5 !important;
            }
            .dark-text-gray-200 {
                color: #383838 !important;
            }
        }
    </style>
</head>
<body class="dark-bg-body-dark" style="word-break: break-word; -webkit-font-smoothing: antialiased; margin: 0; width: 100%; background-color: #F5F7FA; padding: 0">
<div style="display: none">
    @yield('title')
    &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847; &#847;
</div>
<div role="article" aria-roledescription="email" aria-label="Welcome onboard!" lang="en">
    <table style="width: 100%; font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center" class="dark-bg-body-dark" style="background-color: #F5F7FA">
                <div class="sm-leading-5" role="separator" style="line-height: 32px">&zwnj;</div>
                <table class="sm-w-full" style="width: 600px" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                        <td align="center" class="sm-px-5" style="padding-left: 4px; padding-right: 4px">
                            <table style="width: 100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="dark-bg-gray-1000 dark-text-gray-100" style="border-radius: 4px; background-color: #fff; padding: 40px; text-align: left; font-size: 16px; color: #46586B">
                                        @isset($tenant)
                                            <img src="{{ asset($tenant->logo) }}" width="80" alt="{{ $tenant->name }}" style="border: 0; max-width: 100%; vertical-align: middle; line-height: 100%">
                                        @else
                                            <img src="{{ asset('img/logos/cooperative-carbone/logo_png.png') }}" width="100" alt="Coopérative carbone" style="border: 0; max-width: 100%; vertical-align: middle; line-height: 100%">
                                        @endisset
                                        <div role="separator" style="line-height: 40px">&zwnj;</div>

                                        @yield('content')
                                        <div role="separator" style="line-height: 12px">&zwnj;</div>
                                    </td>
                                </tr>
                                <tr role="separator">
                                    <td style="line-height: 32px">&zwnj;</td>
                                </tr>
                                @include('emails.layouts.footer')
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
