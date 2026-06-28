@extends('layouts.dashboard')

@section('title', 'Deco Dashboard - CAUSE Smart Society')
@section('page-title', 'Decoration Director Dashboard')
@section('page-description', 'Welcome to your decoration dashboard')

@section('sidebar')
    @include('partials.team-sidebar')
@endsection

@section('content')
    <div class="mb-8">
        @include('partials.tasks-widget', ['role' => 'deco'])
    </div>
    @include('partials.announcements-feed')
@endsection
