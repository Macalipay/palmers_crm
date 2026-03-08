<nav class="navbar navbar-expand navbar-theme">
    @php
        $profilePicture = Auth::user()->picture;
        $profileExt = strtolower(pathinfo((string) $profilePicture, PATHINFO_EXTENSION));
        $isProfileImage = $profilePicture
            && in_array($profileExt, ['jpg', 'jpeg', 'png', 'gif', 'webp'])
            && file_exists(public_path('images/profile/' . $profilePicture));
        $profileSrc = '/images/profile/' . ($isProfileImage ? $profilePicture : 'default.jpg');
    @endphp
    <a class="sidebar-toggle d-flex mr-2">
        <i class="hamburger align-self-center"></i>
        </a>
	        @yield('page-title')
        
    <div class="navbar-collapse collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown ml-lg-2">
                <a class="nav-link dropdown-toggle position-relative" href="#" id="userDropdown" data-toggle="dropdown">
                    <div class="profile-card">
                        <img src="{{ $profileSrc }}" class="profile-avatar" alt="Profile Picture"/>
                        <div class="font-weight-bold user-name"> {{ Auth::user()->name }}</div>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <a class="dropdown-item" data-toggle="modal" data-target="#changePhotoModal"><i class="align-middle mr-1 fas fa-fw fa-user"></i> Profile Picture</a>
                    <a class="dropdown-item" data-toggle="modal" data-target="#changePasswordModal"><i class="align-middle mr-1 fas fa-fw fa-lock"></i> Change Password</a>
                    <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                           <i class="align-middle mr-1 fas fa-fw fa-arrow-alt-circle-right"></i> Sign out</a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                </div>
            </li>
        </ul>
    </div>
</nav>
