<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-7">
        <p class="mb-2 text-xs font-extrabold uppercase text-[#b9dc3d]">AvoCato Admin</p>
        <h1 class="text-3xl font-extrabold text-white">Вхід до адмінки</h1>
        <p class="mt-3 text-sm leading-6 text-[#aaa]">Керуйте продуктами, фільтрами та замовленнями.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label class="!text-[#d8d8d2]" for="email" value="Email" />
            <x-text-input id="email" class="mt-2 block w-full !rounded-xl !border-[#303030] !bg-[#0b0b0b] !px-4 !py-3 !text-white !shadow-none focus:!border-[#b9dc3d] focus:!ring-[#b9dc3d]" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <x-input-label class="!text-[#d8d8d2]" for="password" value="Пароль" />

            <x-text-input id="password" class="mt-2 block w-full !rounded-xl !border-[#303030] !bg-[#0b0b0b] !px-4 !py-3 !text-white !shadow-none focus:!border-[#b9dc3d] focus:!ring-[#b9dc3d]"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="mt-5 block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-[#3a3a3a] bg-[#0b0b0b] text-[#b9dc3d] shadow-sm focus:ring-[#b9dc3d] focus:ring-offset-[#111]" name="remember">
                <span class="ms-2 text-sm text-[#aaa]">Запам’ятати мене</span>
            </label>
        </div>

        <div class="mt-7 flex items-center justify-between gap-4">
            @if (Route::has('password.request'))
                <a class="rounded-md text-sm font-semibold text-[#aaa] underline underline-offset-4 hover:text-[#b9dc3d] focus:outline-none focus:ring-2 focus:ring-[#b9dc3d] focus:ring-offset-2 focus:ring-offset-[#111]" href="{{ route('password.request') }}">
                    Забули пароль?
                </a>
            @endif

            <x-primary-button class="!rounded-full !bg-[#b9dc3d] !px-6 !py-3 !text-sm !font-extrabold !normal-case !text-[#101010] !shadow-none hover:!bg-[#d5f76a] focus:!bg-[#d5f76a] focus:!ring-[#b9dc3d] focus:!ring-offset-[#111]">
                Увійти
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
