@extends('layouts.app')

@section('title')
<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
@endsection

@section('header')
<h1>Welcome to MonkeysLegion!</h1>
@endsection

@section('content')
<section>
    <p>This is your home page. Use this area to introduce visitors to your app.</p>
</section>
@endsection