@extends('mail.layouts.main')

@section('content')
    <tr>
        <td style="padding:40px 40px 20px; color:#1f2937;">
            <p style="margin:0 0 15px; font-size: 16px; line-height: 1.6;">
                {!! $content !!}
            </p>
        </td>
    </tr>
@endsection
