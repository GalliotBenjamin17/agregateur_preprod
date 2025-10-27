@props([
    'link',
    'text',
    'bgColor' => '#244999',
    'textColor' => 'white',
])

<td align="left" bgcolor="{{ $bgColor }}"
    style="border-radius:3px;background-color:{{ $bgColor }};-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;mso-table-rspace:0pt;mso-table-lspace:0pt;">
    <a href="{{ $link }}" target="_blank" style="display:inline-block;padding:12px 20px 11px;font-weight:400;font-size:16px;line-height:24px;color:{{ $textColor }};text-decoration:none;border-radius:3px;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%;font-family:Colfax, Helvetica, Arial, sans-serif;">
        {{ $text }}
    </a>
</td>
