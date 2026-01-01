<x-app-layout>
<div class="container-fluid mt-4">
    <style>

        .tree-wrapper {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 10px;
            height: 100vh;
        }

        .tree {
            display: inline-flex;   /* NOT flex */
            justify-content: center;
            min-width: max-content; /* critical */
        }

        /* .tree {
            display: flex;
            justify-content: center;
        } */

        .tree-node {
            text-align: center;
            position: relative;
            padding: 10px;
        }

        /* person + spouse row */
        .person-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        /* person box */
        .person,
        .spouse {
            padding: 8px 14px;
            border-radius: 6px;
            border: 2px solid #444;
            background: #fff;
            font-weight: 600;
            white-space: nowrap;
        }

        /* gender colors */
        .person.male,
        .spouse.male {
            border-color: #0d6efd;
        }

        .person.female,
        .spouse.female {
            border-color: #d63384;
        }

        /* children container */
        .children {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            position: relative;
        }

        /* horizontal connector */
        .children::before {
            content: '';
            position: absolute;
            top: -15px;
            left: 0;
            right: 0;
            height: 2px;
            background: #444;
        }

        /* each child branch */
        .child {
            position: relative;
            padding: 0 20px;
        }

        /* vertical connector */
        .child::before {
            content: '';
            position: absolute;
            top: -15px;
            left: 50%;
            width: 2px;
            height: 15px;
            background: #444;
        }
    </style>
    <h4 class="mb-4 text-center">
        Family Tree – {{ $root->name }}
    </h4>

    <div class="tree-wrapper">
        <div class="tree">
            @include('tree.node', ['node' => $tree])
        </div>
    </div>
</div>
</x-app-layout>