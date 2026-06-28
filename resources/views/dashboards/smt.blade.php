@extends('layouts.dashboard')

@section('title', 'SMT Dashboard - CAUSE Smart Society')
@section('page-title', 'SMT Dashboard')
@section('page-description', 'Welcome to your social media dashboard')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
    <div class="mb-8">
        @include('partials.tasks-widget', ['role' => 'smt'])
    </div>
    @include('partials.announcements-feed')
@endsection
