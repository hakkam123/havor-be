<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Havor Admin</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .bg-login {
      background-image: url('/images/bglogin.jpeg'); 
      background-size: cover;
      background-position: center;
    }
  </style>
</head>
<body class="min-h-screen bg-login relative">

  <div class="absolute inset-0 bg-black/30"></div>

  <div class="relative min-h-screen flex items-center justify-center px-4 z-10">
    <div class="w-full max-w-6xl grid grid-cols-1 md:grid-cols-2 gap-0">
      <div class="hidden md:flex flex-col justify-center p-12 text-white">
        <div class="mb-10">
          <div class="flex items-center gap-2 mb-2">
            <span class="font-bold text-3xl tracking-wide">INNOVATE</span>
            <span class="text-base"><i class="bi bi-cpu"></i></span>
          </div>
          <h2 class="text-5xl font-extrabold leading-tight mb-4 drop-shadow">DIGITIZE<br> ELEVATE</h2>
          <p class="text-lg font-medium mb-2 drop-shadow">Where Intelligent Technology Meets Seamless Possibility.</p>
          <p class="text-sm max-w-xs opacity-90 drop-shadow">Step into a smarter digital future — powered by innovation, driven by you.</p>
        </div>
      </div>
      <div class="flex items-center justify-center">
        <div class="backdrop-blur-lg bg-white/50 rounded-2xl shadow-xl overflow-hidden p-8 md:p-12 w-full max-w-md"> 
            <h1 class="text-2xl font-semibold text-slate-800 mb-2">Welcome Back!</h1>
          <p class="text-sm text-slate-500 mb-6"></p>

          @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-100 p-3 text-sm text-red-700">
              <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.authenticate') }}" class="space-y-4">
            @csrf

            <div>
              <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
              <input id="email" name="email" type="email" required
                     value="{{ old('email') }}"
                     class="w-full rounded-xl pl-4 pr-4 py-3 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none bg-white/80 placeholder-slate-400"
                     placeholder="Enter your email" />
            </div>

            <div>
              <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
              <div class="relative">
                <input id="password" name="password" type="password" required
                       class="w-full rounded-xl pl-4 pr-4 py-3 border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-200 outline-none bg-white/80 placeholder-slate-400"
                       placeholder="Enter password" />
                  <span id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 cursor-pointer">
                      <i class="bi bi-eye"></i>
                  </span>
              </div>
            </div>

            <div class="flex items-center justify-between text-sm mb-2 mt-12">
              <label class="inline-flex items-center gap-2 text-slate-600">
                <input type="checkbox" name="remember" class="form-checkbox h-4 w-4 text-blue-600 rounded" />
                <span>Remember me</span>
              </label>
            </div>

            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 bg-[#4178be] hover:bg-[#3569a8] active:scale-95 transition rounded-xl py-3 text-white font-semibold text-base shadow">
            <span>Sign in</span>
            </button>
          </form>

          <div class="flex items-center gap-3 my-6">
            <div class="flex-1 h-px bg-slate-200"></div>
            <div class="text-xs text-slate-400"></div>
            <div class="flex-1 h-px bg-slate-200"></div>
          </div>


          <div class="mt-4 text-sm text-center text-slate-600">
            Please sign-in to your account and start the adventure</a>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
<script>
  document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');

    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.classList.remove('bi-eye');
      icon.classList.add('bi-eye-slash');
    } else {
      passwordInput.type = 'password';
      icon.classList.remove('bi-eye-slash');
      icon.classList.add('bi-eye');
    }
  });
</script>
</html>