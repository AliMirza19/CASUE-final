@extends('layouts.dashboard')

@section('title', 'GD Dashboard - CAUSE Smart Society')
@section('page-title', 'Graphic Designer Dashboard')
@section('page-description', 'Welcome to your design dashboard')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
    <div class="mb-8">
        @include('partials.tasks-widget', ['role' => 'gd'])
    </div>
    @include('partials.announcements-feed')
@endsection
