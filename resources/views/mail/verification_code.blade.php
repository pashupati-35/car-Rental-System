@extends('mail.layouts.main')

@section('content')
    {!! $content !!}

    <div style="
        margin-top:25px;
        padding:15px;
        text-align:center;
        font-size:26px;
        font-weight:bold;
        letter-spacing:4px;
        background:#eef2ff;
        border-radius:6px;
        color:#1d4ed8;
    ">
        {{ $code }}
    </div>

    <p style="margin-top:20px; font-size:14px; color:#666;">
        This verification code is valid for a limited time.
    </p>
@endsection
