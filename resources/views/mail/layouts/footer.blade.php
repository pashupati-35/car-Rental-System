 {{-- Footer - no logo --}}
                    <tr>
                        <td class="footer-padding"
                            style="
                                padding:28px 38px 24px;
                                background:#FAFBFB;
                                border-top:1px solid #E7ECEC;
                                text-align:center;
                            ">

                            {{-- Brand name linked to website --}}
                            @if (!empty($websiteUrl))
                                <div
                                    style="
                                        margin:0;
                                        text-align:center;
                                    ">
                                    <a href="{{ $websiteUrl }}" target="_blank" rel="noopener noreferrer"
                                        style="
                                            font-size:14px;
                                            line-height:1.4;
                                            color:#25313A;
                                            font-weight:800;
                                            text-decoration:none;
                                        ">
                                        {{ $brandName }}
                                    </a>
                                </div>
                            @else
                                <div
                                    style="
                                        margin:0;
                                        font-size:14px;
                                        line-height:1.4;
                                        color:#25313A;
                                        font-weight:800;
                                        text-align:center;
                                    ">
                                    {{ $brandName }}
                                </div>
                            @endif

                            {{-- Address --}}
                            @if (!empty($companyAddress))
                                <div
                                    style="
                                        margin-top:7px;
                                        font-size:11.5px;
                                        line-height:1.6;
                                        color:#98A2AA;
                                        text-align:center;
                                    ">
                                    {{ $companyAddress }}
                                </div>
                            @endif

                            {{-- Phone --}}
                            @if (!empty($companyPhone))
                                <div
                                    style="
                                        margin-top:12px;
                                        font-size:12px;
                                        line-height:1.6;
                                        color:#66727C;
                                        text-align:center;
                                    ">
                                    <span
                                        style="
                                            font-weight:600;
                                            color:#7D878F;
                                        ">
                                        Phone:
                                    </span>

                                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $companyPhone) }}"
                                        style="
                                            color:{{ $primary }};
                                            font-weight:700;
                                            text-decoration:none;
                                        ">
                                        {{ $companyPhone }}
                                    </a>
                                </div>
                            @endif

                            {{-- Email --}}
                            @if (!empty($supportEmail))
                                <div
                                    style="
                                        margin-top:4px;
                                        font-size:12px;
                                        line-height:1.6;
                                        color:#66727C;
                                        text-align:center;
                                    ">
                                    <span
                                        style="
                                            font-weight:600;
                                            color:#7D878F;
                                        ">
                                        Email:
                                    </span>

                                    <a href="mailto:{{ $supportEmail }}"
                                        style="
                                            color:{{ $primary }};
                                            font-weight:700;
                                            text-decoration:none;
                                        ">
                                        {{ $supportEmail }}
                                    </a>
                                </div>
                            @endif

                            {{-- Social URLs come directly from $setting columns --}}
                            @if ($socialItems->isNotEmpty())
                                <div
                                    style="
                                        margin-top:16px;
                                        font-size:11.5px;
                                        line-height:2;
                                        color:#A0A9B0;
                                        text-align:center;
                                    ">
                                    @foreach ($socialItems as $social)
                                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                            style="
                                                color:{{ $primary }};
                                                font-weight:700;
                                                text-decoration:none;
                                            ">
                                            {{ $social['name'] }}
                                        </a>

                                        @if (!$loop->last)
                                            <span
                                                style="
                                                    color:#C1C9CE;
                                                ">
                                                &nbsp;&middot;&nbsp;
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            <div
                                style="
                                    height:1px;
                                    background:#E7ECEC;
                                    margin-top:18px;
                                    font-size:0;
                                    line-height:0;
                                ">
                                &nbsp;
                            </div>

                            {{-- Copyright --}}
                            <div
                                style="
                                    padding-top:14px;
                                    font-size:10.5px;
                                    line-height:1.6;
                                    color:#A0A9B0;
                                    text-align:center;
                                ">
                                &copy; {{ date('Y') }} {{ $brandName }}.
                                All rights reserved.
                            </div>

                            {{-- Recipient --}}
                            @if (!empty($sentToEmail))
                                <div
                                    style="
                                        margin-top:3px;
                                        font-size:10.5px;
                                        line-height:1.6;
                                        color:#A0A9B0;
                                        text-align:center;
                                    ">
                                    Sent to {{ $sentToEmail }}
                                </div>
                            @endif

                            {{-- Marketing controls only when supplied --}}
                            @if (!empty($unsubscribeUrl) || !empty($preferencesUrl))
                                <div
                                    style="
                                        margin-top:7px;
                                        font-size:10.5px;
                                        line-height:1.6;
                                        text-align:center;
                                    ">
                                    @if (!empty($unsubscribeUrl))
                                        <a href="{{ $unsubscribeUrl }}"
                                            style="
                                                color:#7D878F;
                                                text-decoration:underline;
                                            ">
                                            Unsubscribe
                                        </a>
                                    @endif

                                    @if (!empty($unsubscribeUrl) && !empty($preferencesUrl))
                                        <span
                                            style="
                                                color:#B6BEC3;
                                            ">
                                            &nbsp;&middot;&nbsp;
                                        </span>
                                    @endif

                                    @if (!empty($preferencesUrl))
                                        <a href="{{ $preferencesUrl }}"
                                            style="
                                                color:#7D878F;
                                                text-decoration:underline;
                                            ">
                                            Email preferences
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
