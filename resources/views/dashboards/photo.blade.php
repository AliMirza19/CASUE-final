@extends('layouts.dashboard')

@section('title', 'Photo Dashboard - CAUSE Smart Society')
@section('page-title', 'Photography Director Dashboard')
@section('page-description', 'Welcome to your photography dashboard')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
    <div class="mb-8">
        @include('partials.tasks-widget', ['role' => 'photo'])
    </div>
    @include('partials.announcements-feed')
@endsection
