<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@include('manager::partials.head', [
    'scripts' => ['resources/css/app.css']
])
<body>
<div id="app" class="bg-cms-800">
    <div class="bg1 absolute left-0 top-0 right-0 bottom-0 bg-no-repeat bg-center bg-cover transition transition-opacity duration-1000"></div>
    <div class="bg2 absolute left-0 top-0 right-0 bottom-0 bg-no-repeat bg-center bg-cover transition transition-opacity duration-1000 opacity-0"></div>

    <div class="z-50 relative w-full h-full md:w-[30rem] max-w-full p-10 bg-black/75 text-gray-300 text-opacity-75 flex flex-col justify-between h-full">
        @yield('content')
    </div>
</div>

<script>
  let num = 0

  const els = [
    document.querySelector('.bg1'),
    document.querySelector('.bg2')
  ]

  let loadBg = () => fetch('https://picsum.photos/1600/900').then(response => {
    els[num].style.backgroundImage = 'url(' + response.url + ')'
    els[num].classList.remove('opacity-0')
    num = !num ? 1 : 0
    els[num].classList.add('opacity-0')
  })

  loadBg()
  setInterval(loadBg, 20000)
</script>
</body>
</html>
