@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - CAUSE Smart Society')
@section('page-title', 'Admin Dashboard')
@section('page-description', 'Welcome to your administrative dashboard')

@section('sidebar')
    @include('partials.admin-sidebar')
@endsection

@section('content')
    @include('partials.admin-quick-actions')
    @include('partials.announcements-feed')
@endsection
