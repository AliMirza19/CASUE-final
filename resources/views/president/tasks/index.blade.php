@extends('layouts.dashboard')

@section('title', 'Team Tasks - CAUSE Smart Society')
@section('page-title', 'Team Tasks Status')
@section('page-description', 'Monitor the status and progress of tasks assigned to various society teams')

@section('sidebar')
    @include('partials.president-sidebar')
@endsection

@section('content')
    <div class="space-y-6">
        @include('partials.assigned-tasks-widget')
    </div>
@endsection
