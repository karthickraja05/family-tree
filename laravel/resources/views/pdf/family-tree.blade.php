<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; }
        .person { font-weight: bold; }
        .child { margin-left: 30px; }
    </style>
</head>
<body>

<h2>Family Tree: {{ $root->name }}</h2>

@include('pdf.tree-node', ['node' => $tree])

</body>
</html>
