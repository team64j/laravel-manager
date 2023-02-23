@extends('manager::auth')

@section('content')
    <form id="formLogin">
        @csrf

        <div class="flex items-center mb-6 pt-4 pl-1">
            <span class="logo w-16 h-16"></span>
            <span class="pl-4 text-white uppercase font-light text-4xl">{{ config('app.name') }}</span>
        </div>

        <div class="form-group form-floating mb-6">
            <label for="floatingName" class="block mb-1">{{ __('global.username') }}</label>
            <input name="username"
                   type="text"
                   id="floatingName"
                   class="w-full bg-transparent py-3 font-bold text-white text-lg rounded"
                   value="{{ old('username') }}"
                   placeholder="{{ __('global.username') }}"
                   autofocus>
        </div>

        <div class="form-group form-floating mb-6">
            <label for="floatingPassword" class="block mb-1">{{ __('global.password') }}</label>
            <input name="password"
                   type="password"
                   id="floatingPassword"
                   class="w-full bg-transparent py-3 font-bold text-white text-lg rounded"
                   value="{{ old('password') }}"
                   placeholder="{{ __('global.password') }}">
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input name="remember"
                       type="checkbox"
                       checked="checked"
                       id="remember"
                       class="mr-2 bg-transparent rounded">
                <label class="text-sm" for="remember">{{ __('global.remember_username') }}</label>
            </div>
            <div>
                <button class="btn-green font-bold py-2 px-4 rounded"
                        type="submit">{{ __('global.login_button') }}
                </button>
            </div>
        </div>

    </form>

    <div class="text-center text-sm text-gray-500 hover:text-gray-400">
        <a href="{{ route('manager.forgot') }}">{{ __('global.forgot_your_password') }}</a>
    </div>

    <script>
      const formLogin = document.getElementById('formLogin')

      formLogin.addEventListener('submit', function (event) {
        event.preventDefault()

        const formUsername = formLogin.querySelector('[name="username"]')
        const formPassword = formLogin.querySelector('[name="password"]')
        const formRemember = formLogin.querySelector('[name="remember"]')

        formLogin.querySelectorAll('.error').forEach(i => i.parentElement.removeChild(i))
        formUsername.classList.remove('border-rose-500')
        formPassword.classList.remove('border-rose-500')

        fetch('manager/auth/login', {
          method: 'post',
          credentials: 'same-origin',
          headers: {
            'Content-Type': 'application/json;charset=utf-8',
            'X-CSRF-TOKEN': formLogin.querySelector('[name="_token"]').value
          },
          body: JSON.stringify({
            username: formUsername.value,
            password: formPassword.value,
            remember: formRemember.checked
          })
        }).then(response => {
          if (response.status === 200) {
            response.json().then(response => {
              localStorage['token'] = response['access_token']
              location.href = response['redirect']
            })
          } else if (response.status === 401) {
            formUsername.classList.add('border-rose-500')
            formPassword.classList.add('border-rose-500')
          } else {
            response.json().then(response => {

              if (typeof response === 'object') {
                for (const i in response) {
                  const el = formLogin.querySelector('[name="' + i + '"]')

                  if (el) {
                    el.classList.add('border-rose-500')
                    el.insertAdjacentHTML('afterend', '<span class="error text-rose-500">' + response[i] + '</span>')
                  }
                }
              }
            })
          }
        })
      })
    </script>
@endsection
