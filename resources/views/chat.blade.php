@extends('layouts.app')

@section('content')

	<section class="container-fluid">

        <div class="row">

            <div id="chat" class="col-md-6 ml-md-auto mr-md-auto order-3 order-xs-3">

                <tabs-component user="{{ $user }}" initial-rooms="{{ $initialRooms }}"></tabs-component>

            </div>

            <div id="rooms" class="col-md-3 ml-md-auto mr-md-auto order-2 order-xs-2">

                <rooms-component user="{{ $user }}" rooms="{{ $rooms }}"></rooms-component>

            </div>

            <div id="createroom" class="col-md-3 ml-md-auto mr-md-auto order-1 order-xs-1">

                <create-room-component self="{{ $user }}"></create-room-component>

            </div>
        </div>

	</section>

@endsection