<div class="tree-node">

    {{-- Person + spouse --}}
    <div class="person-row">
        <div class="person {{ $node['person']->gender }}">
            {{ $node['person']->name }}
        </div>

        @foreach ($node['spouses'] as $spouse)
            <div class="spouse {{ $spouse->gender }}">
                {{ $spouse->name }}
            </div>
        @endforeach

    </div>

    {{-- Children --}}
    @if(count($node['children']) > 0)
        <div class="children">
            @foreach ($node['children'] as $child)
                <div class="child">
                    @include('tree.node', ['node' => $child])
                </div>
            @endforeach
        </div>
    @endif

</div>
