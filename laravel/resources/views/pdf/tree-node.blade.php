<div class="child">
    <p class="person">
        {{ $node['person']->name }}
        ({{ ucfirst($node['person']->gender) }})
        - {{ optional($node['person']->dob)->format('Y') }}
    </p>

    @foreach ($node['spouses'] as $spouse)
        <p>Spouse: {{ $spouse->name }}</p>
    @endforeach

    @foreach ($node['children'] as $child)
        @include('pdf.tree-node', ['node' => $child])
    @endforeach
</div>
