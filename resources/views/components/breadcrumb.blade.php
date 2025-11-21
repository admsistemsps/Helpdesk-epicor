<span class="flex items-center space-x-2 text-gray-700 mb-3">
    <i class="fa-solid fa-house"></i>
    <a href="{{ route('dashboard') }}" class="hover:underline">Home</a>
    <span>|</span>

    @php
    $segments = request()->segments();
    $url = '';
    @endphp

    @foreach ($segments as $key => $segment)
    @php
    if (is_numeric($segment) || preg_match('/^[A-Za-z0-9]{10,}$/', $segment)) continue;

    $url .= '/' . $segment;
    $isLast = $loop->last || is_numeric(end($segments)) || preg_match('/^[A-Za-z0-9]{10,}$/', end($segments));

    $displaySegment = str_replace('-', ' ', $segment);

    $isFuhd = preg_match('/^f-uhd-sps-\d{4}-\d+$/i', $segment);
    if ($isFuhd) {
    $displaySegment = strtoupper(str_replace('-', '/', $segment));
    } else {
    $displaySegment = ucwords($displaySegment);
    }
    @endphp

    @if ($isLast)
    <span class="text-gray-500">{{ $displaySegment }}</span>
    @elseif ($isFuhd)
    <span class="text-gray-500">{{ $displaySegment }}</span>
    <span>|</span>
    @else
    <a href="{{ url($url) }}" class="hover:underline">{{ $displaySegment }}</a>
    <span>|</span>
    @endif
    @endforeach
</span>