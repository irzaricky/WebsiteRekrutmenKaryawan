<template>
    <header class="absolute inset-x-0 top-0 z-50">
        <nav
            class="flex items-center justify-between p-6 lg:px-8"
            aria-label="Global"
        >
            <div class="flex lg:flex-1">
                <Link
                    :href="img[0].link"
                    class="-m-1.5 p-1.5 btn btn-ghost avatar"
                    style="height: 5rem"
                >
                    <img
                        :src="img[0].href"
                        class="h-16 w-auto avatar"
                        alt="Recruiter Logo"
                    />
                </Link>
            </div>
            <div class="flex gap-x-12">
                <Link
                    v-for="item in navigation"
                    :key="item.name"
                    :href="item.href"
                    class="text-sm font-semibold leading-6 text-gray-900 btn btn-ghost"
                >
                    {{ item.name }}
                </Link>
            </div>

            <div class="flex flex-1 lg:justify-end">
                <template v-if="isLogin">
                    <Link
                        v-if="role === 'HRD'"
                        :href="route('dashboard')"
                        class="text-sm font-semibold leading-6 text-gray-900 btn btn-ghost"
                    >
                        Dashboard
                    </Link>
                    <Link
                        v-else-if="role === 'Candidate'"
                        :href="route('profile.edit')"
                        class="text-sm font-semibold leading-6 text-gray-900 btn btn-ghost"
                    >
                        Profile
                    </Link>
                    <Link
                        :href="route('logout')"
                        class="text-sm font-semibold leading-6 text-gray-900 btn btn-ghost"
                        method="post"
                        as="button"
                    >
                        Log Out
                    </Link>
                </template>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="text-sm font-semibold leading-6 text-gray-900 btn btn-ghost"
                    >
                        Log in <span aria-hidden="true">&rarr;</span>
                    </Link>
                    <Link
                        :href="route('register')"
                        class="text-sm font-semibold leading-6 text-gray-900 btn btn-ghost"
                    >
                        Register <span aria-hidden="true">&rarr;</span>
                    </Link>
                </template>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3"; // Import Link from Inertia

const { props } = usePage();
const isLogin = props.auth.user;
const role = props.auth?.user?.role || null;

const navigation = [
    { name: "Home", href: "/" },
    { name: "Company", href: "/company" },
    { name: "Product", href: "/product" },
    { name: "Features", href: "/features" },
];

const img = [{ id: "1", href: "./assets/images/for.png", link: "/" }];
</script>
