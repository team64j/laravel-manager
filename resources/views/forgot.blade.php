@extends('manager::auth')

@section('content')
    <form>
        @csrf

        <div class="flex items-center mb-6 pt-4 pl-1">
            <span class="logo w-16 h-16"></span>
            <span class="pl-4 text-white uppercase font-light text-4xl">{{ config('app.name') }}</span>
        </div>

        <div class="form-group form-floating mb-6">
            <label for="floatingName" class="block mb-1">{{ __('global.email') }}</label>
            <input name="email"
                   type="text"
                   id="floatingName"
                   class="w-full bg-transparent py-3 font-bold text-white text-lg rounded {{ $errors->has('email') ? 'border-rose-500' : '' }}"
                   value="{{ old('email') }}"
                   placeholder="{{ __('global.email') }}"
                   autofocus>
            @if ($errors->has('email'))
                <span class="text-rose-500">{{ $errors->first('email') }}</span>
            @endif
        </div>

        <div class="flex items-center justify-end">
            <button class="bg-green-700 hover:bg-green-800 text-white font-bold py-2 px-4 rounded"
                    type="submit">{{ __('global.login_button') }}
            </button>
        </div>

    </form>

    <div class="text-center text-sm text-gray-500 hover:text-gray-400">
        <a href="{{ route('manager.login') }}">{{ __('global.mgr_login_start') }}</a>
    </div>
@endsection
