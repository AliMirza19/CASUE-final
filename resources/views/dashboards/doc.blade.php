@extends('layouts.dashboard')

@section('title', 'Doc Dashboard - CAUSE Smart Society')
@section('page-title', 'Documentation Director Dashboard')
@section('page-description', 'Welcome to your documentation dashboard')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
    <div class="mb-8">
        @include('partials.tasks-widget', ['role' => 'doc'])
    </div>
    @include('partials.announcements-feed')
@endsection
