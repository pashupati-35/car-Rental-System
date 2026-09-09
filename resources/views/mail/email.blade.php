@extends('mail.layouts.main')

@section('content')
    <tr>
        <td class="content-cell" style="padding:42px 46px 44px; background-color:#ffffff; color:#344054;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td style="padding:0 0 24px;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                            style="background-color:#f7fbfa; border:1px solid #d9ece9; border-radius:10px;">
                            <tr>
                                <td width="48" align="center" valign="middle" style="padding:14px 0 14px 14px;">
                                    <table role="presentation" width="34" height="34" cellpadding="0" cellspacing="0" border="0"
                                        style="width:34px; height:34px; background-color:#e6f4f2; border-radius:17px;">
                                        <tr>
                                            <td align="center" valign="middle" style="color:#0f766e; font-size:17px; line-height:34px; font-weight:700;">
                                                &#9993;
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td valign="middle" style="padding:13px 16px 13px 12px;">
                                    <div style="color:#0f6d67; font-size:11px; line-height:1.35; font-weight:750; text-transform:uppercase; letter-spacing:0.8px;">
                                        Futech Solution
                                    </div>
                                    <div style="padding-top:3px; color:#475467; font-size:12px; line-height:1.45;">
                                        Secure account &amp; service notification
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="email-content" style="font-size:15px; line-height:1.72; color:#344054; word-break:break-word;">
                        {!! $content !!}
                    </td>
                </tr>
                <tr>
                    <td style="padding:30px 0 0;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                            style="border-top:1px solid #eaecf0;">
                            <tr>
                                <td style="padding:20px 0 0; color:#667085; font-size:12px; line-height:1.6;">
                                    Need help? Please contact our support team if you have questions about this message.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection
