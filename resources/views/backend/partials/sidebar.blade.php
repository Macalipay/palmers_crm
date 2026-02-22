<nav id="sidebar" class="sidebar {{isset($toggled)?$toggled:''}}">
    <a class="sidebar-brand" href="#">
        {{-- <img src="/images/profile/{{Auth::user()->picture}}" class="img-fluid rounded-circle mb-2 profile-logo" alt="Voter's Picture" width="10"/> --}}
        <img src="{{ asset('/images/ms-logo.png') }}" class="img-fluid	" width="150" height="32"/>
    </a>

    <div class="sidebar-content">
        <ul class="sidebar-nav">
                <li class="sidebar-header">
                    Main
                </li>
                <li class="sidebar-item">
                    <a href="#dashboard" data-toggle="collapse" class="sidebar-link collapsed">
                        <i class="align-middle mr-2 fas fa-fw fa-chart-line"></i> <span class="align-middle">Dashboard</span>
                    </a>
                    <ul id="dashboard" class="sidebar-dropdown list-unstyled collapse " data-parent="#sidebar">
                        @role('SUPER ADMIN|Super Admin')
                            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('home') }}">Daily</a></li>
                            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('monthly') }}">Monthly</a></li>
                            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('annual') }}">Annual</a></li>
                        @endrole
                        @role('SUPER ADMIN|Super Admin|TELEMARKETING')
                            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('telemarketing/dashboard') }}">Telemarketing</a></li>
                            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('otc/dashboard') }}">OTC</a></li>
                            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('csd/dashboard') }}">CSD</a></li>
                        @endrole
                    </ul>
                </li>

                <li class="sidebar-item">
                    <a href="#company" data-toggle="collapse" class="sidebar-link collapsed">
                        <i class="align-middle mr-2 fas fa-fw fa-building"></i> <span class="align-middle">Company Name</span>
                    </a>
                    <ul id="company" class="sidebar-dropdown list-unstyled collapse " data-parent="#sidebar">
                        <li class="sidebar-item"><a class="sidebar-link" href="{{ url('company') }}">Company</a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{ url('store') }}">Store</a></li>
                    </ul>
                </li>
            
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ url('item') }}">
                        <i class="align-middle mr-2 fa fa-fw fa-fire-extinguisher" style="color:#153d77"></i> <span class="align-middle">Item/Product</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ url('calendar') }}">
                        <i class="align-middle mr-2 fa fa-fw fa-calendar" style="color:#153d77"></i> <span class="align-middle">Calendar</span>
                    </a>
                </li>

            @role('SUPER ADMIN|Super Admin')
                <li class="sidebar-header">
                    Reports
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ url('reports/sales') }}">
                        <i class="align-middle mr-2 fas fa-fw fa-file-alt"></i> <span class="align-middle">Sales Report</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ url('reports/telemarketing') }}">
                        <i class="align-middle mr-2 fas fa-fw fa-phone"></i> <span class="align-middle">Telemarketing Report</span>
                    </a>
                </li>
            @endrole

            <li class="sidebar-header">
                Transaction
            </li>
                <li class="sidebar-item">
                    @role('SUPER ADMIN|Super Admin|CSD')
                        <a class="sidebar-link" href="{{ url('sale') }}">
                            <i class="align-middle mr-2 fa fa-fw fa-money-bill-wave-alt" style="color:#153d77"></i> <span class="align-middle">CSD</span>
                        </a>
                    @endrole
                    @role('SUPER ADMIN|Super Admin|OVER THE COUNTER')
                        <a class="sidebar-link" href="{{ url('otc') }}">
                            <i class="align-middle mr-2 fa fa-fw fa-money-bill-wave-alt" style="color:#153d77"></i> <span class="align-middle">OTC</span>
                        </a>
                    @endrole
                    @role('SUPER ADMIN|Super Admin|FSD')
                        <a class="sidebar-link" href="{{ url('fsd') }}">
                            <i class="align-middle mr-2 fa fa-fw fa-money-bill-wave-alt" style="color:#153d77"></i> <span class="align-middle">FSD</span>
                        </a>
                    @endrole
                    @role('SUPER ADMIN|Super Admin|ASD')
                        <a class="sidebar-link" href="{{ url('asd') }}">
                            <i class="align-middle mr-2 fa fa-fw fa-money-bill-wave-alt" style="color:#153d77"></i> <span class="align-middle">ASD</span>
                        </a>
                    @endrole
                </li>

                
                @role('SUPER ADMIN|Super Admin')
                    <li class="sidebar-item">
                        <a href="#s_name" data-toggle="collapse" class="sidebar-link collapsed">
                            <i class="align-middle mr-2 fas fa-fw fa-users"></i> <span class="align-middle">100 Names</span>
                        </a>
                        <ul id="s_name" class="sidebar-dropdown list-unstyled collapse " data-parent="#sidebar">
                            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('referral') }}">Referral</a></li>
                            <li class="sidebar-item"><a class="sidebar-link" href="{{ url('referral/tree') }}">Referral Tree</a></li>
                        </ul>
                    </li>
                
                <li class="sidebar-item">
                    <a href="#telemarketing-sd" data-toggle="collapse" class="sidebar-link collapsed">
                        <i class="align-middle mr-2 fas fa-fw fa-users"></i> <span class="align-middle">CRM</span>
                    </a>
                    <ul id="telemarketing-sd" class="sidebar-dropdown list-unstyled collapse " data-parent="#sidebar">
                        <li class="sidebar-item"><a class="sidebar-link" href="{{ url('prospect') }}">Prospect</a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{ url('engage') }}">Engage</a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{ url('acquire') }}">Acquire</a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{ url('retention') }}">Retention</a></li>
                    </ul>
                </li>
                @endrole

                
                @role('SUPER ADMIN|Super Admin|TELEMARKETING|TELEMARKETING HEAD')
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ url('telemarketing') }}">
                        <i class="align-middle mr-2 fa fa-fw fa-money-bill-wave-alt" style="color:#153d77"></i> <span class="align-middle">TeleMarketing</span>
                    </a>
                </li>
                @endrole

            <li class="sidebar-header">
                Setup
            </li>
            @role('SUPER ADMIN|Super Admin')
                <li class="sidebar-item">
                    <a href="#user_management" data-toggle="collapse" class="sidebar-link collapsed">
                        <i class="align-middle mr-2 fas fa-fw fa-user"></i> <span class="align-middle">User Management</span>
                    </a>
                    <ul id="user_management" class="sidebar-dropdown list-unstyled collapse " data-parent="#sidebar">
                        <li class="sidebar-item"><a class="sidebar-link" href="{{ url('user') }}">User</a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{ url('role') }}">Role</a></li>
                    </ul>
                </li>
            @endrole
            <li class="sidebar-item">
                <a href="#maintenance" data-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle mr-2 fas fa-fw fa-tools"></i> <span class="align-middle">Maintenance</span>
                </a>
                <ul id="maintenance" class="sidebar-dropdown list-unstyled collapse " data-parent="#sidebar">
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ url('sales_associate') }}">Sales Associate</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ url('merchandiser') }}">Merchandiser</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ url('source') }}">Source</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ url('division') }}">Division</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ url('province') }}">Province</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ url('brand') }}">Brand</a></li>
                    <li class="sidebar-item"><a class="sidebar-link" href="{{ url('personnel') }}">Personnel</a></li>
                </ul>
            </li>
            @role('SUPER ADMIN|Super Admin')
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ url('activity_logs') }}">
                        <i class="align-middle mr-2 fa fa-fw fa-file-signature" style="color:#153d77"></i> <span class="align-middle">Activity Logs</span>
                    </a>
                </li>
            @endrole
        </ul>
    </div>
</nav>
