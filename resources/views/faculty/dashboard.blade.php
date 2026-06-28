@extends('layouts.dashboard')

@section('title', 'Faculty Dashboard - CAUSE Smart Society')
@section('page-title', 'Faculty Dashboard')
@section('page-description', 'Welcome to your faculty dashboard')

@section('sidebar')
    @include('partials.faculty-sidebar')
@endsection

@section('content')
    <!-- Main body empty for announcements (handled by layout) -->
@endsection
