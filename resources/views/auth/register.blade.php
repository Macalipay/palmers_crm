<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <link rel="icon" href="{{ asset('images/logo/logo.png') }}">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Responsive Bootstrap 4 Admin &amp; Dashboard Template">
	<meta name="author" content="Bootlab">

	<title>PRACA</title>

    <link href="{{ asset('docs/css/dark.css') }}" rel="stylesheet">
    
	<style>
		body {
			opacity: 0;
		}
	</style>
    <script src="{{ asset('docs/js/settings.js') }}"></script>
    
</head>

<body class="theme-blue">
	<div class="splash active">
		<div class="splash-icon"></div>
	</div>

	<main class="main h-100 w-100">
		<div class="container h-100">
			<div class="row h-100">
				<div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
					<div class="d-table-cell align-middle">

						<div class="text-center mt-4">
							<h1 class="h2">PRACA Registration</h1>
							<p class="lead">
								Kindly fill-up your Information.
							</p>
						</div>

						<div class="card">
							<div class="card-body">
								<div class="m-sm-4">
                                    <form method="POST" action="{{ route('register') }}">
                                        @csrf
										<div class="form-group">
											<label>First Name</label>
                                            <input id="firstname" type="text" class="form-control form-control-lg @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname') }}" required autocomplete="firstname" autofocus placeholder="Enter your First Name">
            
                                            @error('firstname')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
											<label>Middle Name</label>
                                            <input id="middlename" type="text" class="form-control form-control-lg @error('middlename') is-invalid @enderror" name="middlename" value="{{ old('middlename') }}" required autocomplete="middlename" autofocus placeholder="Enter your Middle Name">
            
                                            @error('middlename')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group">
											<label>Last Name</label>
                                            <input id="lastname" type="text" class="form-control form-control-lg @error('lastname') is-invalid @enderror" name="lastname" value="{{ old('lastname') }}" required autocomplete="lastname" autofocus placeholder="Enter your Last Name">
            
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
											<label>Gender</label>
                                            <select name="gender" class="form-control form-control-lg @error('gender') is-invalid @enderror" id="gender">
                                                <option value="M">Male</option>
                                                <option value="F">Female</option>
                                            </select>    
                                        </div>
                                        
                                        <div class="form-group">
											<label>Degree</label>
                                            <select name="degree" class="form-control form-control-lg @error('degree') is-invalid @enderror" id="degree">
                                                <option value="College Degree">College Degree</option>
                                                <option value="Non College Degree">Non College Degree</option>
                                            </select>    
                                        </div>
                                        
                                        <div class="form-group">
											<label>Voter Status</label>
                                            <select name="status" class="form-control form-control-lg @error('status') is-invalid @enderror" id="status">
                                                <option value="0">New Voter</option>
                                                <option value="1">Old Voter</option>
                                            </select>    
                                        </div>

                                        <div class="form-group">
											<label>Contact</label>
                                            <input id="contact" type="number" class="form-control form-control-lg @error('contact') is-invalid @enderror" name="contact" value="{{ old('contact') }}" required autocomplete="contact" autofocus placeholder="Enter your Contact #">
                                        </div>

                                        <div class="form-group">
											<label>GCash or Paymaya</label>
                                            <input id="emoney" type="number" class="form-control form-control-lg @error('emoney') is-invalid @enderror" name="emoney" value="{{ old('emoney') }}" required autocomplete="emoney" autofocus placeholder="Enter your GCash # or Paymaya">
                                        </div>
                                        
                                        <div class="form-group">
											<label>Birthday</label>
                                            <input id="birthday" type="date" class="form-control form-control-lg @error('birthday') is-invalid @enderror" name="birthday" value="{{ old('birthday') }}" required autocomplete="birthday" autofocus>
                                        </div>

                                        <div class="form-group">
											<label>Region</label>
                                            <select class="form-control form-control-lg" id="region_id"  name="region_id" placeholder="Pick a Region...">
                                                <option selected disabled>Select a Region</option>
                                                @foreach ($regions as $region)
                                                    <option value="{{ $region->region_id }}">{{ $region->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group">
											<label>Province</label>
                                            <select class="form-control form-control-lg" id="province_id"  name="province_id" placeholder="Pick a Province...">
                                                <option selected disabled>Select a Province</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
											<label>City</label>
                                            <select class="form-control form-control-lg" id="city_id"  name="city_id" placeholder="Pick a City...">
                                                <option selected disabled>Select a City</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
											<label>Barangay</label>
                                            <select class="form-control form-control-lg" id="barangay_id"  name="barangay_id" placeholder="Pick a Barangay...">
                                                <option selected disabled>Select a Barangay</option>
                                            </select>
                                        </div>

										<div class="form-group">
											<label>Email</label>
											<input id="email" type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your Email">

                                            @error('email')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
										</div>
										<div class="form-group">
											<label>Password</label>
											<input id="password" type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Enter your Password">

                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
										</div>
										<div class="form-group">
											<label>Confirm Password</label>
                                            <input id="password-confirm" type="password" class="form-control form-control-lg" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your Password">
										</div>
										<div class="text-center mt-3">
                                            <a href="{{ url('/') }}" class="btn btn-lg btn-default">Login</a>
											<button type="submit" class="btn btn-lg btn-primary">Register</button>
										</div>
									</form>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</main>

	<svg width="0" height="0" style="position:absolute">
    <defs>
      <symbol viewBox="0 0 512 512" id="ion-ios-pulse-strong">
        <path
          d="M448 273.001c-21.27 0-39.296 13.999-45.596 32.999h-38.857l-28.361-85.417a15.999 15.999 0 0 0-15.183-10.956c-.112 0-.224 0-.335.004a15.997 15.997 0 0 0-15.049 11.588l-44.484 155.262-52.353-314.108C206.535 54.893 200.333 48 192 48s-13.693 5.776-15.525 13.135L115.496 306H16v31.999h112c7.348 0 13.75-5.003 15.525-12.134l45.368-182.177 51.324 307.94c1.229 7.377 7.397 11.92 14.864 12.344.308.018.614.028.919.028 7.097 0 13.406-3.701 15.381-10.594l49.744-173.617 15.689 47.252A16.001 16.001 0 0 0 352 337.999h51.108C409.973 355.999 427.477 369 448 369c26.511 0 48-22.492 48-49 0-26.509-21.489-46.999-48-46.999z">
        </path>
      </symbol>
    </defs>
  </svg>
	<script src="{{ asset('docs/js/app.js') }}"></script>
    <script>
        function region(id){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/register/province/' + id,
                method: 'get',
                success: function(data) {
                    $('#province_id option:not(:first)').remove();
                    $('#city_id option:not(:first)').remove();
                    $('#barangay_id option:not(:first)').remove();

                    for(i=0; i < data.provinces.length; i++) {
                        $("#province_id").append('<option value='+ data.provinces[i].province_id +'>' + data.provinces[i].name + '</option>');
                    }
                }
            });
        }

        function city(id){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/register/city/' + id,
                method: 'get',
                success: function(data) {
                    $('#city_id option:not(:first)').remove();
                    $('#barangay_id option:not(:first)').remove();

                    for(i=0; i < data.cities.length; i++) {
                        $("#city_id").append('<option value='+ data.cities[i].city_id +'>' + data.cities[i].name + '</option>');
                    }
                }
            });
        }

        function barangay(id){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/register/barangay/' + id,
                method: 'get',
                success: function(data) {
                    $('#barangay_id option:not(:first)').remove();

                    for(i=0; i < data.barangays.length; i++) {
                        $("#barangay_id").append('<option value='+ data.barangays[i].code +'>' + data.barangays[i].name + '</option>');
                    }
                }
            });
        }

        $(document).ready(function(){
			$('#region_id').change(function(){
                region(this.value);
            })

            $('#province_id').change(function(){
                city(this.value);
            })

            $('#city_id').change(function(){
                barangay(this.value);
            })
		})
    </script>
</body>
</html>


{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
