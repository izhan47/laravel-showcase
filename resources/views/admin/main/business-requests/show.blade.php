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
                    <label>First Name</label>
                    <p>{{ $result->first_name }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Last Name</label>
                    <p>{{ $result->last_name }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Business Name</label>
                    <p>{{ $result->business_name }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Contact Email</label>
                    <p>{{ $result->contact_email }}</p>
                </div>
                <div class="wag-profile-name-details">
                    <label>Message</label>
                    <p>{{ $result->message }}</p>
                </div>
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
