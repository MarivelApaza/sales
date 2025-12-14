<x-guest-layout>
    <div class="login-container" >
        <div class="card login-card" style="max-width: 400px; width: 100%; border-radius: 18px; box-shadow: 0 12px 25px rgba(0,0,0,0.1); overflow: hidden;">

            <!-- Card Header -->
            <div class="card-header text-center" style="background: linear-gradient(135deg, #7A4A2E, #A47148); color: #fff; font-weight:700; font-size:20px; padding:20px;">
                {{ __('Iniciar Sesión') }}
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <div class="card-body" style="padding: 25px;">

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="form-group mb-3">
                        <label for="email" style="color:#5A3321; font-weight:500;">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="form-control" placeholder="Correo Electrónico"
                               style="border-radius:10px; border:2px solid #E9DFD3; padding:10px; transition:0.3s;">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="form-group mb-3">
                        <label for="password" style="color:#5A3321; font-weight:500;">Contraseña</label>
                        <input id="password" type="password" name="password" required
                               class="form-control" placeholder="Contraseña"
                               style="border-radius:10px; border:2px solid #E9DFD3; padding:10px; transition:0.3s;">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        @if (Route::has('password.request'))
                            <div class="text-end mt-1">
                                <a href="{{ route('password.request') }}" style="font-size:0.85rem; color:#7A4A2E;">¿Olvidaste tu contraseña?</a>
                            </div>
                        @endif
                    </div>

                    <!-- Remember Me -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember_me" style="border:2px solid #E9DFD3;">
                        <label class="form-check-label" for="remember_me" style="color:#5A3321; margin-left:8px;">Recordarme</label>
                    </div>

                    <!-- Submit -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-block"
                                style="background: linear-gradient(135deg,#A47148,#7A4A2E); border:none; color:#fff; font-weight:600; padding:12px; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.1); transition:0.3s;">
                            {{ __('Iniciar Sesión') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <style>
        .login-card input:focus {
            border-color: #7A4A2E !important;
            box-shadow: 0 0 8px rgba(122, 74, 46, 0.3);
            outline: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        a:hover {
            color: #A47148 !important;
        }
    </style>
</x-guest-layout>
