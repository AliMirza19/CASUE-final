@extends('layouts.dashboard')

@section('title', 'Patron Dashboard - CAUSE Smart Society')
@section('page-title', 'Patron Dashboard')
@section('page-description', 'Welcome to your patron dashboard')

@section('sidebar')
    @include('partials.patron-sidebar')
@endsection

@section('content')
    @include('partials.announcements-feed')
@endsection
