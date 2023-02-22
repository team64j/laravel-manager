<template>
  <div class="h-full flex flex-col justify-between">
    <div>

      <div class="flex items-center mb-6 pt-4 pl-1">
        <span class="logo w-16 h-16"></span>
        <span class="pl-4 text-white uppercase text-4xl font-light">APP_NAME</span>
      </div>

      <div class="form-group form-floating mb-6">
        <label for="floatingName" class="block mb-1">'global.username'</label>
        <input name="username"
               type="text"
               id="floatingName"
               class="w-full bg-transparent py-3 font-bold text-white text-lg rounded border-rose-500"
               value=""
               placeholder="'global.username'"
               autofocus>
        @if ($errors->has('username'))
        <span class="text-rose-500">username</span>
        @endif
      </div>

      <div class="form-group form-floating mb-6">
        <label for="floatingPassword" class="block mb-1">'global.password'</label>
        <input name="password"
               type="password"
               id="floatingPassword"
               class="w-full bg-transparent py-3 font-bold text-white text-lg rounded border-rose-500"
               value=""
               placeholder="'global.password'">
        @if ($errors->has('password'))
        <span class="text-rose-500">'password'</span>
        @endif
      </div>

      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <input name="remember"
                 type="checkbox"
                 checked="checked"
                 id="remember"
                 class="mr-2 bg-transparent rounded">
          <label class="text-sm" for="remember">'global.remember_username'</label>
        </div>
        <div>
          <button class="btn-green font-bold py-2 px-4 rounded"
                  type="submit">'global.login_button'
          </button>
        </div>
      </div>

    </div>

    <div class="text-center text-sm text-gray-500 hover:text-gray-400">
      <a href="forgot">'global.forgot_your_password'</a>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AuthLoginPage'
}
</script>

<style scoped>

</style>
