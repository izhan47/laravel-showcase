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
            <a href="{{ $module_route }}" class="wag-go-back-btn-main">Go Back</a>
            
        </div>
    </div>
    <div class="wag-profile-details-block-main">
        <div class="wag-profile-details">
            <div class="wag-profile-pic-details">
                @if(isset($module_name))
                    <h1 class="wag-admin-page-title-main mt-3 mb-3">{{  $module_name }}</h1>
                @endif
                <div class="wag-profile-name-details">
                    <label>Store Name</label>
                    <p>{{ $result->store_name }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Contact Email</label>
                    <p>{{ $result->email }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Website</label>
                    <p>{{ $result->website_url }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Phone Number</label>
                    <p>{{ $result->phone_number }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Address</label>
                    <p>{{ $result->address_line_1 }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Donation Link</label>
                    <p>{{ $result->donation_link }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Description</label>
                    <p>{{ $result->description }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Featured Pet Pros</label>
                    <p>{{ $result->is_featured_pet_pro }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Featured Title</label>
                    <p>{{ $result->featured_title }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Featured Description</label>
                    <p>{{ $result->featured_description }}</p>
                <div class="wag-profile-name-details">
                    <label>Date</label>
                    <p>{{ $result->formated_created_at }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push("scripts")

@endpush
