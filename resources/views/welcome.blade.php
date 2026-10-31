@extends('web.layout.app')
@section('content')

 <x-sections.landing />
 <x-sections.about />
 <livewire:home.booking /> 
 <x-sections.appointment />

 <x-sections.prices :services="$services" />
@include('components.sections.why-choose-us')

 <x-sections.review />
 <x-sections.services :services="$services" />



@endsection

 