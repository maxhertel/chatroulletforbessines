<script setup>
import { Head, Link } from '@inertiajs/vue3';
import WorldMap from '../Components/WorldMap.vue';
defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});


</script>

<template class="h-14 bg-linear-65 from-purple-500 to-pink-500">

    <div class=" text-black dark:bg-black dark:text-white ">
        <div class="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white ">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 gap-2 py-10 lg:grid-cols-3" style="margin-bottom: 50px;">
                    <div class="flex lg:col-start-2 justify-left">
                        Chat Roulette for Business
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                        Dashboard
                        </Link>

                        <template v-else>
                            <Link :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                            Log in
                            </Link>


                        </template>
                    </nav>
                </header>

                <WorldMap/>
                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    Links
                </footer>
            </div>
        </div>
    </div>
</template>
