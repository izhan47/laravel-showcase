@extends('admin.layouts.admin')

@push("styles")
@endpush

@push('meta-tags')
    <title>{{ config('wagenabled.app_name') }} | {{  $module_name }}</title>
@endpush

@section('content')
<section class="wag-admin-plan-main-cover-section wag-admin-inner-page-main">
    <div class="wag-page-main-header-bar">
        <div class="wag-title-bar-main">
            <a class="wag-go-back-btn-main" href="{{ $module_route }}" class="">Go Back</a>
            @if(isset($singular_module_name))
                <h1 class="wag-admin-page-title-main mt-3">{{  $singular_module_name }}</h1>
            @endif
        </div>
    </div>
    <div class="wag-profile-details-block-main">
        <div class="wag-profile-details">
            <div class="clearfix">
                <a href="" class="wag-profile-pic">
                    <img src="{{ $result->profile_image_full_path }}" height="100" width="100" />
                </a>
            </div>
            <div class="wag-profile-pic-details">
                <div class="wag-profile-name-details">
                    <label>Name</label>
                    <p>{{ $result->name }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Email</label>
                    <p>{{ $result->email }}</p>
                </div>

                <div class="wag-profile-name-details">
                    <label>Zip code</label>
                    <p>{{ $result->zipcode ? $result->zipcode : '---'  }}</p>
                </div>
                <hr />
                <div class="wag-profile-name-details">
                    <label>Vet Place Name</label>
                    <p>{{ $result->vet_place_name ? $result->vet_place_name : '---'  }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Vet Address</label>
                    <p>{{ $result->vet_address ? $result->vet_address : '---'  }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Vet Phone Number</label>
                    <p id="formated-phone-number">{{ $result->vet_phone_number  ? $result->vet_phone_number : '---' }}</p>
                </div>
            </div>
        </div>
    </div>

  <h1 class="wag-admin-page-title-main mt-3 mb-3">User's Pets</h1>

   <div class="wag-profile-details-block-main">
       <div class="row">
           @if($result->pets->count() > 0)
               @foreach($result->pets as $pet)
                   <div class="col-4 mb-1">
                       <div class="wag-profile-details">
                           <div class="row">
                               <div class="col-12">
                                   <img src="{{ $pet->pet_image_thumb_full_path }}"  height="250px" width="100%" />
                               </div>
                               <div class="col-12">
                                   <div class="wag-profile-pic-details">
                                       <div class="wag-profile-name-details">
                                           <label>Name</label>
                                           <p>{{ $pet->name }}</p>
                                       </div>
                                       <div class="wag-profile-name-details">
                                           <label>Breed</label>
                                           <p>
                                           @foreach($pet->breed as $breed)
                                              &bull;{{ $breed->name }}
                                           @endforeach
                                           </p>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               @endforeach
           @endif
       </div>
   </div>

   @if($result->pets->count() == 0)
       <div class="wag-profile-details-block-main">
           <div class="wag-profile-details">
               <p>No pet found!</p>
           </div>
       </div>
   @endif
</section>
@endsection

@push("scripts")
    <script type="text/javascript">

    if( "{!! $result->vet_phone_number !!}" ) {
        var FormattedPhoneNum = getFormattedPhoneNum("{!! $result->vet_phone_number !!}");
        $("#formated-phone-number").text(FormattedPhoneNum);
    }

    function getFormattedPhoneNum( input ) {
        let output = "(";
        input.replace( /^\D*(\d{0,3})\D*(\d{0,3})\D*(\d{0,4})/, function( match, g1, g2, g3 )
            {
              if ( g1.length ) {
                output += g1;
                if ( g1.length === 3 ) {
                    output += ")";
                    if ( g2.length ) {
                        output += " " + g2;
                        if ( g2.length === 3 ) {
                            output += " - ";
                            if ( g3.length ) {
                                output += g3;
                            }
                        }
                    }
                 }
              }
            }
        );
        return output;
    }

    </script>
@endpush
