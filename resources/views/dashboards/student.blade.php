@extends('layouts.dashboard')

@section('title', 'Student Dashboard - CAUSE Smart Society')
@section('page-title', 'Student Dashboard')
@section('page-description', 'Welcome to your student dashboard')

@section('sidebar')
    @include('partials.student-sidebar')
@endsection

@section('content')
    @include('partials.announcements-feed')
@endsection
