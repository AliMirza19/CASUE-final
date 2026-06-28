@extends('layouts.dashboard')

@section('title', 'HOD Dashboard - CAUSE Smart Society')
@section('page-title', 'HOD Dashboard')
@section('page-description', 'Welcome to your department dashboard')

@section('sidebar')
    @include('partials.hod-sidebar')
@endsection

@section('content')
    @include('partials.announcements-feed')
@endsection
