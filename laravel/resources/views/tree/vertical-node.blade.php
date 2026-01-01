<div class="tree-node" style="margin-left: {{ $level * 30 }}px">

    <div class="person-box">
        <strong>{{ $node['person']->name }}</strong>
        <span class="meta">
            ({{ ucfirst($node['person']->gender) }}
            @if($node['person']->dob)
                · {{ \Carbon\Carbon::parse($node['person']->dob)->format('Y') }}
            @endif)
        </span>
    </div>

    {{-- spouses --}}
    @foreach ($node['spouses'] as $spouse)
        <div class="spouse-box" style="margin-left: 20px">
            ❤ {{ $spouse->name }}
        </div>
    @endforeach

    {{-- children --}}
    @foreach ($node['children'] as $child)
        @include('tree.vertical-node', [
            'node' => $child,
            'level' => $level + 1
        ])
    @endforeach

</div>
