<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link href="{{ asset('images/logo/logo.png')}}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png')}}" rel="apple-touch-icon">
	<meta name="csrf-token" content="{{	csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Bootstrap 4 Admin &amp; Dashboard Template">
	<meta name="author" content="Bootlab">

	<title> PALMER </title>
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">
	<link href="{{ asset('css/new_template.css') }}" rel="stylesheet">
	<link href="{{ asset('docs/css/modern.css') }}" rel="stylesheet">
	<!-- <link href="{{ asset('docs/css/fontawesome.css') }}" rel="stylesheet"> -->
	<link rel="stylesheet" href="{{ asset('css/datatable.min.css') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery-ui.css') }}" />
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap.js') }}"></script>
	<script src="{{ asset('js/app.js') }}"></script>
	<link href="{{ asset('/css/app.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/custom.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/custom/activity_logs.css') }}" rel="stylesheet">
	<link href="{{ asset('/css/voter_information.css') }}" rel="stylesheet">
	@yield('links')
	@stack('head')
	@yield('style')

	<style>
		span.page-title {
			font-size: 20px;
			color: white;
			font-weight: bold;
		}
		.breadcrumb {
			padding: 0px !important;
		}
		.breadcrumb-item {
			color: white !important;
		}
		div#datatables_filter input[type="search"] {
			border: 1px solid #ccc;
			padding: 5px;
			border-radius: 50px;
		}
		/* .wrapper:before {
			background: #197f02;
		} */

		a.sidebar-brand, .splash-icon {
			background: #3b7ddd !important;
		}
		.header-title {
			font-size: 1.64063rem;
			color: white;
			font-weight: bold;
		}
		li.nav-item.dropdown.ml-lg-2>a>i {
			color: white;
		}
		.hamburger, .hamburger:after, .hamburger:before {
			background: hsl(0deg 0% 100%) !important;
		}
		a.sidebar-brand, .splash-icon {
			background: white !important;
			text-align: center;
		}
		.table.dataTable tr th {
			font-weight: 500;
			font-size: 12px;
			padding: 5px 10px !important;
			border-bottom: 2px solid #3f3f3f;
			font-weight: bold;
			text-transform: uppercase;

		}
		table.dataTable tbody td {
			padding: 8px 10px;
			padding: 5px 10px;
			font-size: 13px;
			border: 0px;
		}
		.action {
			padding: 3px 5px;
			font-size: 8px !important;
			margin-right: 5px;
			border: 0px;
		}
		.sidebar-brand, .sidebar-brand:hover {
			padding: 4rem 1em;
			background: #153d77;
			font-size: 1.175rem;
			font-weight: 500;
			color: #fff;
			width: 100%;
			display: block;
			text-decoration: none;
		}
	</style>
</head>

<body>
	<div class="splash active">
		<div class="splash-icon"></div>
	</div>

	<div class="wrapper">
		@include('backend.partials.sidebar')
		<div class="main">
			@include('backend.partials.header')
			@yield('content')
			@include('backend.partials.footer')
		</div>
	</div>


	<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Change Password</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body m-3">
					<form method="POST" action="{{ route('change.password') }}">
						@csrf

						@foreach ($errors->all() as $error)
							<p class="text-danger">{{ $error }}</p>
						@endforeach

						<div class="form-group row">
							<label for="password" class="col-md-4 col-form-label text-md-right">Current Password</label>

							<div class="col-md-6">
								<input id="password" type="password" class="form-control" name="current_password" autocomplete="current-password">
							</div>
						</div>

						<div class="form-group row">
							<label for="password" class="col-md-4 col-form-label text-md-right">New Password</label>

							<div class="col-md-6">
								<input id="new_password" type="password" class="form-control" name="new_password" autocomplete="current-password">
							</div>
						</div>

						<div class="form-group row">
							<label for="password" class="col-md-4 col-form-label text-md-right">New Confirm Password</label>

							<div class="col-md-6">
								<input id="new_confirm_password" type="password" class="form-control" name="new_confirm_password" autocomplete="current-password">
							</div>
						</div>

						<div class="form-group row mb-0">
							<div class="col-md-8 offset-md-4">
								<button type="submit" class="btn btn-primary">
									Update Password
								</button>
							</div>
						</div>
					</form>

				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="changePhotoModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Change Profile Picture</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body m-3">
					<form method="POST" action="{{ route('change.picture') }}" enctype="multipart/form-data">
						@csrf
						<div class="form-group row">
							<input type="hidden" name="user_id" id="user_id" value="{{Auth::user()->id}}"/>
							<label class="col-form-label col-sm-3 text-sm-right">Profile Picture</label>
							<div class="col-sm-9">
								<input type="file" name="picture" class="form-control">
							</div>
						</div>

						<div class="form-group row mb-0">
							<div class="col-md-8 offset-md-4">
								<button type="submit" class="btn btn-primary">
									Upload
								</button>
							</div>
						</div>
					</form>

				</div>
			</div>
		</div>
	</div>
    <div class="modal fade" id="loadModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    Generating Record, please wait...
                </div>
            </div>
        </div>
    </div>

	<svg width="0" height="0" style="position:absolute">
    <defs>
      <symbol viewBox="0 0 512 512" id="ion-ios-pulse-strong">
        <path
          d="M448 273.001c-21.27 0-39.296 13.999-45.596 32.999h-38.857l-28.361-85.417a15.999 15.999 0 0 0-15.183-10.956c-.112 0-.224 0-.335.004a15.997 15.997 0 0 0-15.049 11.588l-44.484 155.262-52.353-314.108C206.535 54.893 200.333 48 192 48s-13.693 5.776-15.525 13.135L115.496 306H16v31.999h112c7.348 0 13.75-5.003 15.525-12.134l45.368-182.177 51.324 307.94c1.229 7.377 7.397 11.92 14.864 12.344.308.018.614.028.919.028 7.097 0 13.406-3.701 15.381-10.594l49.744-173.617 15.689 47.252A16.001 16.001 0 0 0 352 337.999h51.108C409.973 355.999 427.477 369 448 369c26.511 0 48-22.492 48-49 0-26.509-21.489-46.999-48-46.999z">
        </path>
      </symbol>
    </defs>
  </svg>
    {{-- <script type="text/javascript" src="{{ asset('/assets/js/jquery.colorbox-min.js')}}"></script> --}}
	<script type="text/javascript" src="{{ asset('/packages/barryvdh/elfinder/js/standalonepopup.min.js')}}"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
	<script>
		$(document).ready(function(){

		})
	</script>
	@yield('scripts')

</body>
</html>
