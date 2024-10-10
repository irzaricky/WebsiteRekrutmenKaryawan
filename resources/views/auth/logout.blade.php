@extends('layouts.app')
@section('content')
    <div class="mb-0 w-screen lg:w-[500px] card shadow-lg border-none shadow-slate-100 relative">
        <div class="!px-10 !py-12 card-body">
            <a href="#!">
                <img src="{{ URL::to('assets/images/logo-light.png') }}" alt="" class="hidden h-6 mx-auto dark:block">
                <img src="{{ URL::to('assets/images/logo-dark.png') }}" alt="" class="block h-6 mx-auto dark:hidden">
            </a>
            <div class="mt-8 text-center">
                <div class="mb-4 text-center">
                    <i data-lucide="log-out" class="mx-auto text-purple-500 size-6 fill-purple-100"></i>
                </div>
                <h4 class="mb-2 text-custom-500 dark:text-custom-500">You are Logged Out</h4>
                <p class="mb-8 text-slate-500 dark:text-zink-200">Thank you for using starcode admin template</p>
            </div>
            <a href="{{ route('login') }}" class="w-full text-white transition-all duration-200 ease-linear btn bg-custom-500 border-custom-500 hover:text-white hover:bg-custom-600 hover:border-custom-600 focus:text-white focus:bg-custom-600 focus:border-custom-600 focus:ring focus:ring-custom-100 active:text-white active:bg-custom-600 active:border-custom-600 active:ring active:ring-custom-100 dark:ring-custom-400/20">Sign In</a>
        </div>
    </div>
@endsection
