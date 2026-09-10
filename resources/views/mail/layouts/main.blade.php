@php

    $brandName = data_get($setting ?? [], 'display_name') ?: data_get($setting ?? [], 'company_name') ?: config('app.name', 'AutoRent');
    $tagline = data_get($setting ?? [], 'tagline') ?: data_get($setting ?? [], 'slogan');

    $logoUrl = data_get($setting ?? [], 'logo_path');
    $websiteUrl = data_get($setting ?? [], 'website');
    $companyPhone = data_get($setting ?? [], 'phone');
    $supportEmail = data_get($setting ?? [], 'support_email');

    $companyAddress = data_get($setting ?? [], 'address');

    if (blank($companyAddress)) {
        $companyAddress = collect([
            data_get($setting ?? [], 'address_line_1'),
            data_get($setting ?? [], 'address_line_2'),
        ])
            ->filter()
            ->implode(', ');
    }

    $socialItems = collect([
        [
            'name' => 'Facebook',
            'url' => data_get($setting ?? [], 'facebook'),
        ],
        [
            'name' => 'Instagram',
            'url' => data_get($setting ?? [], 'instagram'),
        ],
        [
            'name' => 'LinkedIn',
            'url' => data_get($setting ?? [], 'linkedin'),
        ],
        [
            'name' => 'Twitter',
            'url' => data_get($setting ?? [], 'twitter'),
        ],
        [
            'name' => 'YouTube',
            'url' => data_get($setting ?? [], 'youtube'),
        ],
        [
            'name' => 'TikTok',
            'url' => data_get($setting ?? [], 'tiktok'),
        ],
        [
            'name' => 'WhatsApp',
            'url' => data_get($setting ?? [], 'whatsapp'),
        ],
        [
            'name' => 'Viber',
            'url' => data_get($setting ?? [], 'viber'),
        ],
    ])
        ->filter(fn(array $social): bool => filled($social['url']))
        ->values();

    /*
    |--------------------------------------------------------------------------
    | Dynamic Brand Colors
    |--------------------------------------------------------------------------
    */

    $normalizeHex = static function ($value, string $fallback): string {
        $value = is_string($value) ? trim($value) : '';

        if (preg_match('/^#?[0-9A-Fa-f]{6}$/', $value)) {
            return '#' . strtoupper(ltrim($value, '#'));
        }

        if (preg_match('/^#?[0-9A-Fa-f]{3}$/', $value)) {
            $hex = strtoupper(ltrim($value, '#'));

            return sprintf('#%s%s%s%s%s%s', $hex[0], $hex[0], $hex[1], $hex[1], $hex[2], $hex[2]);
        }

        return $fallback;
    };

    $mixHex = static function (string $hex, string $with, float $amount): string {
        $hex = ltrim($hex, '#');
        $with = ltrim($with, '#');
        $amount = max(0, min(1, $amount));

        $rgb = [];

        for ($i = 0; $i < 3; $i++) {
            $base = hexdec(substr($hex, $i * 2, 2));
            $target = hexdec(substr($with, $i * 2, 2));

            $rgb[] = (int) round($base + ($target - $base) * $amount);
        }

        return sprintf('#%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
    };

    $primary = $normalizeHex(data_get($setting ?? [], 'primary_color'), '#31837C');

    $secondary = $normalizeHex(data_get($setting ?? [], 'secondary_color'), '#D65A00');

    $primaryDark = $mixHex($primary, '#000000', 0.18);
    $primarySoft = $mixHex($primary, '#FFFFFF', 0.91);
    $primaryBorder = $mixHex($primary, '#FFFFFF', 0.76);

    $secondaryDark = $mixHex($secondary, '#000000', 0.16);
    $secondarySoft = $mixHex($secondary, '#FFFFFF', 0.91);
    $secondaryBorder = $mixHex($secondary, '#FFFFFF', 0.72);
@endphp

<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="x-apple-disable-message-reformatting">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="color-scheme" content="light">

    <meta name="supported-color-schemes" content="light">

    <title>
        {{ $subject ?? $brandName }}
    </title>

    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>
                    96
                </o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->

    <style type="text/css">
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        table {
            border-spacing: 0 !important;
            border-collapse: collapse !important;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            outline: none;
            text-decoration: none;
            display: block;
        }

        a {
            text-decoration: none;
        }

        a[x-apple-data-detectors],
        u+#body a,
        #MessageViewBody a {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        @media only screen and (max-width: 640px) {
            .page-padding {
                padding: 18px 10px !important;
            }

            .email-shell {
                width: 100% !important;
                max-width: 100% !important;
            }

            .header-padding {
                padding: 20px 22px !important;
            }

            .hero-padding {
                padding: 30px 22px 28px !important;
            }

            .content-padding {
                padding: 30px 22px !important;
            }

            .footer-padding {
                padding: 24px 22px !important;
            }

            .headline {
                font-size: 26px !important;
                line-height: 1.25 !important;
            }

            .subheadline {
                font-size: 15px !important;
            }

            .primary-button {
                display: block !important;
                width: auto !important;
                text-align: center !important;
            }

            .header-website {
                display: none !important;
            }
        }
    </style>
</head>

<body id="body"
    style="
        margin:0;
        padding:0;
        background:#F4F7F7;
        font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;
        -webkit-font-smoothing:antialiased;
    ">

    {{-- Email preview text --}}
    @if (!empty($previewText))
        <div
            style="
                display:none;
                max-height:0;
                overflow:hidden;
                mso-hide:all;
                opacity:0;
                color:transparent;
            ">
            {{ $previewText }}
        </div>
    @endif

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
        style="
            width:100%;
            background:#F4F7F7;
        ">
        <tr>
            <td align="center" class="page-padding" style="
                    padding:42px 16px;
                ">

                <table role="presentation" width="620" cellpadding="0" cellspacing="0" class="email-shell"
                    style="
                        width:100%;
                        max-width:620px;
                        background:#FFFFFF;
                        border:1px solid #DFE8E7;
                        border-radius:18px;
                        overflow:hidden;
                        box-shadow:0 14px 38px rgba(31,73,69,0.08);
                    ">

                    {{-- Dynamic brand accent --}}
                    <tr>
                        <td
                            style="
                                padding:0;
                                font-size:0;
                                line-height:0;
                            ">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="76%" height="5"
                                        style="
                                            height:5px;
                                            background:{{ $primary }};
                                            font-size:0;
                                            line-height:0;
                                        ">
                                        &nbsp;
                                    </td>

                                    <td width="24%" height="5"
                                        style="
                                            height:5px;
                                            background:{{ $secondary }};
                                            font-size:0;
                                            line-height:0;
                                        ">
                                        &nbsp;
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    @include('mail.layouts.header')

                   {{-- Main content --}}
                    <tr>
                        <td class="content-padding"
                            style="
                                padding:32px 38px;
                                background:#FFFFFF;
                                text-align:left;
                            ">

                            @if (!empty($greeting))
                                <h2
                                    style="
                                        margin:0 0 14px;
                                        color:#25313A;
                                        font-size:18px;
                                        line-height:1.45;
                                        font-weight:700;
                                    ">
                                    {{ $greeting }}
                                </h2>
                            @endif

                            @if (!empty($bodyMessage) && is_string($bodyMessage))
                                <div
                                    style="
                                        margin:0;
                                        color:#5F6B75;
                                        font-size:15px;
                                        line-height:1.75;
                                    ">
                                    {!! $bodyMessage !!}
                                </div>
                            @elseif (isset($message) && is_string($message) && filled($message))
                                <div
                                    style="
                                        margin:0;
                                        color:#5F6B75;
                                        font-size:15px;
                                        line-height:1.75;
                                    ">
                                    {!! $message !!}
                                </div>
                            @endif

                            {{-- Optional custom Blade/HTML slot --}}
                            @yield('content')

                            @if (!empty($infoMessage))
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="
                                        margin:26px 0 0;
                                        background:{{ $primarySoft }};
                                        border:1px solid {{ $primaryBorder }};
                                        border-radius:13px;
                                    ">
                                    <tr>
                                        <td width="4"
                                            style="
                                                width:4px;
                                                background:{{ $primary }};
                                                border-radius:13px 0 0 13px;
                                                font-size:0;
                                                line-height:0;
                                            ">
                                            &nbsp;
                                        </td>

                                        <td
                                            style="
                                                padding:17px 18px;
                                            ">
                                            <div
                                                style="
                                                    margin:0 0 4px;
                                                    font-size:13px;
                                                    line-height:1.4;
                                                    color:{{ $primaryDark }};
                                                    font-weight:800;
                                                ">
                                                Information
                                            </div>

                                            <div
                                                style="
                                                    font-size:14px;
                                                    line-height:1.65;
                                                    color:#56636B;
                                                ">
                                                {!! $infoMessage !!}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- Secondary / important box --}}
                            @if (!empty($highlightTitle) || !empty($highlightMessage) || !empty($alertMessage))
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="
                                        margin:26px 0 0;
                                        background:{{ $secondarySoft }};
                                        border:1px solid {{ $secondaryBorder }};
                                        border-radius:13px;
                                    ">
                                    <tr>
                                        <td width="4"
                                            style="
                                                width:4px;
                                                background:{{ $secondary }};
                                                border-radius:13px 0 0 13px;
                                                font-size:0;
                                                line-height:0;
                                            ">
                                            &nbsp;
                                        </td>

                                        <td
                                            style="
                                                padding:17px 18px;
                                            ">
                                            <div
                                                style="
                                                    margin:0 0 4px;
                                                    font-size:13px;
                                                    line-height:1.4;
                                                    color:{{ $secondaryDark }};
                                                    font-weight:800;
                                                ">
                                                {{ $highlightTitle ?? 'Important' }}
                                            </div>

                                            <div
                                                style="
                                                    font-size:14px;
                                                    line-height:1.65;
                                                    color:#7B4A2C;
                                                ">
                                                {!! $highlightMessage ?? ($alertMessage ?? '') !!}
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- Primary CTA --}}
                            @if (!empty($ctaUrl) && !empty($ctaText))
                                <table role="presentation" cellpadding="0" cellspacing="0"
                                    style="
                                        margin:28px 0 0;
                                    ">
                                    <tr>
                                        <td align="left">

                                            <!--[if mso]>
                                            <v:roundrect
                                                xmlns:v="urn:schemas-microsoft-com:vml"
                                                xmlns:w="urn:schemas-microsoft-com:office:word"
                                                href="{{ $ctaUrl }}"
                                                style="height:48px;v-text-anchor:middle;width:220px;"
                                                arcsize="18%"
                                                strokecolor="{{ $primaryDark }}"
                                                fillcolor="{{ $primary }}"
                                            >
                                                <w:anchorlock/>
                                                <center
                                                    style="
                                                        color:#FFFFFF;
                                                        font-family:Arial,sans-serif;
                                                        font-size:15px;
                                                        font-weight:bold;
                                                    "
                                                >
                                                    {{ $ctaText }}
                                                </center>
                                            </v:roundrect>
                                            <![endif]-->

                                            <!--[if !mso]><!-- -->
                                            <a href="{{ $ctaUrl }}" target="_blank" rel="noopener noreferrer"
                                                class="primary-button"
                                                style="
                                                    display:inline-block;
                                                    background:{{ $primary }};
                                                    border:1px solid {{ $primaryDark }};
                                                    border-radius:10px;
                                                    padding:14px 24px;
                                                    color:#FFFFFF;
                                                    font-size:15px;
                                                    line-height:18px;
                                                    font-weight:800;
                                                    text-decoration:none;
                                                ">
                                                {{ $ctaText }} &nbsp;&rarr;
                                            </a>
                                            <!--<![endif]-->
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            {{-- Secondary CTA --}}
                            @if (!empty($secondaryCtaUrl) && !empty($secondaryCtaText))
                                <div
                                    style="
                                        margin-top:15px;
                                        font-size:13px;
                                        line-height:1.6;
                                    ">
                                    <a href="{{ $secondaryCtaUrl }}" target="_blank" rel="noopener noreferrer"
                                        style="
                                            color:{{ $secondary }};
                                            font-weight:700;
                                            text-decoration:underline;
                                        ">
                                        {{ $secondaryCtaText }}
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>

                    @include('mail.layouts.footer')

                </table>
            </td>
        </tr>
    </table>

</body>

</html>
