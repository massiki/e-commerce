@extends('layouts.app')

@section('content')
  <main class="pt-90">
    <div class="container" style="padding-top: 20px; padding-bottom: 80px; max-width: 760px;">
      <h2 class="page-title" style="margin-bottom: 24px;">My Profile</h2>

      <div class="bg-white border rounded-3 shadow-sm p-4 p-md-5 mb-4">
        @include('profile.partials.update-profile-information-form')
      </div>

      <div class="bg-white border rounded-3 shadow-sm p-4 p-md-5 mb-4">
        @include('profile.partials.update-password-form')
      </div>

      <div class="bg-white border rounded-3 shadow-sm p-4 p-md-5">
        @include('profile.partials.delete-user-form')
      </div>
    </div>
  </main>
@endsection
