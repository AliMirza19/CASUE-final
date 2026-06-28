@extends('layouts.dashboard')

@section('title', 'VC Dashboard - CAUSE Smart Society')
@section('page-title', 'VC Dashboard')
@section('page-description', 'Welcome to your coordinator dashboard')

@section('sidebar')
    @include('partials.vc-sidebar')
@endsection

@section('content')
    @include('partials.announcements-feed')
@endsection
