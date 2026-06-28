@extends('layouts.dashboard')

@section('title', 'Video Dashboard - CAUSE Smart Society')
@section('page-title', 'Videography Director Dashboard')
@section('page-description', 'Welcome to your videography dashboard')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
    <div class="mb-8">
        @include('partials.tasks-widget', ['role' => 'video'])
    </div>
    @include('partials.announcements-feed')
@endsection
