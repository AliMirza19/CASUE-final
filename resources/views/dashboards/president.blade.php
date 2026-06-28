@extends('layouts.dashboard')

@section('title', 'President Dashboard - CAUSE Smart Society')
@section('page-title', 'President Dashboard')
@section('page-description', 'Welcome to your presidential dashboard')

@section('sidebar')
    @include('partials.president-sidebar')
@endsection

@section('content')
    @include('partials.announcements-feed')
@endsection
