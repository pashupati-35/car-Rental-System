{{-- Header --}}
<tr>
    <td class="header-padding"
        style="
            padding:22px 38px;
            background:#FFFFFF;
            border-bottom:1px solid #E8EEEE;
        ">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="middle">
                    <table role="presentation" cellpadding="0" cellspacing="0">
                        <tr>

                            {{-- Logo only in header --}}
                            @if (!empty($logoUrl))
                                <td width="48" valign="middle"
                                    style="
                                        width:48px;
                                        padding-right:10px;
                                    ">
                                    @if (!empty($websiteUrl))
                                        <a href="{{ $websiteUrl }}" target="_blank"
                                            rel="noopener noreferrer"
                                            style="
                                                display:block;
                                                text-decoration:none;
                                            ">
                                            <img src="{{ $logoUrl }}" alt="{{ $brandName }}"
                                                width="38" height="38"
                                                style="
                                                    width:38px;
                                                    height:38px;
                                                    border-radius:10px;
                                                ">
                                        </a>
                                    @else
                                        <img src="{{ $logoUrl }}" alt="{{ $brandName }}"
                                            width="38" height="38"
                                            style="
                                                width:38px;
                                                height:38px;
                                                border-radius:10px;
                                            ">
                                    @endif
                                </td>
                            @else
                                <td width="48" valign="middle"
                                    style="
                                        width:48px;
                                        padding-right:10px;
                                    ">
                                    <div
                                        style="
                                            width:38px;
                                            height:38px;
                                            line-height:38px;
                                            text-align:center;
                                            background:{{ $primarySoft }};
                                            border:1px solid {{ $primaryBorder }};
                                            border-radius:10px;
                                            color:{{ $primary }};
                                            font-size:18px;
                                            font-weight:800;
                                        ">
                                        {{ strtoupper(substr($brandName, 0, 1)) }}
                                    </div>
                                </td>
                            @endif

                            <td valign="middle">
                                <div
                                    style="
                                        font-size:17px;
                                        line-height:1.25;
                                        color:#17202A;
                                        font-weight:800;
                                        letter-spacing:-0.2px;
                                    ">
                                    {{ $brandName }}
                                </div>

                                @if (!empty($tagline))
                                    <div
                                        style="
                                            margin-top:3px;
                                            font-size:11px;
                                            line-height:1.4;
                                            color:#7A858F;
                                        ">
                                        {{ $tagline }}
                                    </div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>

                @if (!empty($websiteUrl))
                    <td align="right" valign="middle" class="header-website">
                        <a href="{{ $websiteUrl }}" target="_blank" rel="noopener noreferrer"
                            style="
                                font-size:12px;
                                line-height:1.4;
                                color:{{ $primary }};
                                font-weight:700;
                                text-decoration:none;
                            ">
                            Visit website
                        </a>
                    </td>
                @endif
            </tr>
        </table>
    </td>
</tr>
