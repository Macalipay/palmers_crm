@extends('backend.master.template')
@section('style')
    <link rel="stylesheet" href="{{ asset('treant/Treant.css') }}">
    <link rel="stylesheet" href="{{ asset('css/collapsable.css') }}">
    <link rel="stylesheet" href="{{ asset('treant/vendor/perfect-scrollbar/perfect-scrollbar.css') }}">
@endsection
@section('content')
    <main class="content">
        <div class="container-fluid">
            <div class="header">
                <h1 class="header-title">
                    Tree
                </h1>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Tree Diagram Screen
                               
                            </h5>
                        </div>
                        <div class="col-12">
                            <div class="chart" id="collapsable"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@section('style')
<style>
    .node {
        padding: 10px;
        border: 1px solid #484848;
        border-radius: 0px !important;
        background: black !important;
    }
    .node {
        display: flex !important;
    }
    .Treant > .node img {
   
    }
</style>
@endsection

@section('scripts')
    <script src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('/treant/vendor/raphael.js') }}"></script>
    <script src="{{ asset('/treant/Treant.js') }}"></script>
    <script src="{{ asset('/treant/vendor/jquery.easing.js') }}"></script>
    <script src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

    <script>
         function tree_diagram() {
    // PARENT
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
            url: '/referral/parent',
            method: 'get',
            success: function(parentData) {
                // MEMBER
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/referral/parseJSON/' + 2,
                    method: 'get',
                    success: function(memberData) {
                        console.log(memberData);

                        // Function to recursively build the node structure
                        function buildNode(nodeData) {
                            return {
                                innerHTML: `<p>${nodeData.name}</p>`,
                                HTMLclass: nodeData.class || "child-node",
                                image: nodeData.image || "/images/logo/logo.png",
                                children: nodeData.children ? nodeData.children.map(buildNode) : []
                            };
                        }

                        // Constructing the mother tree node structure
                        var motherTree = {
                            image: "/images/logo/logo.png",
                            HTMLclass: "mother-tree",
                            text: { name: 'PALMER' },
                            collapsed: true,
                            children: memberData.children.map(buildNode)
                        };

                        var chart_config = {
                            chart: {
                                container: "#collapsable",
                                animateOnInit: true,
                                node: {
                                    collapsable: true
                                },
                                animation: {
                                    nodeAnimation: "easeOutBounce",
                                    nodeSpeed: 700,
                                    connectorsAnimation: "bounce",
                                    connectorsSpeed: 700
                                }
                            },
                            nodeStructure: motherTree
                        };

                        new Treant(chart_config);
                    }
                });
            }
        });
    }

        $(function() {
            tree_diagram();
            // tree = new Treant( chart_config );

        });
    </script>
@endsection
