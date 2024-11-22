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
                    <template v-if="role === 'HRD'">
                        <Link
                            :href="route('dashboard')"
                            class="text-sm font-semibold leading-6 text-gray-900 btn btn-ghost"
                        >
                            Dashboard
                        </Link>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-sm font-semibold leading-6 text-gray-900 btn btn-ghost"
                        >
                            Log Out
                        </Link>
                    </template>

                    <template v-else-if="role === 'Candidate'">
                        <div class="relative ml-3">
                            <button 
                                @click="toggleDropdown"
                                class="flex items-center gap-x-2 text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700"
                            >
                                <span>{{ user?.name }}</span>
                                <svg 
                                    class="h-5 w-5" 
                                    :class="{ 'rotate-180': isDropdownOpen }"
                                    viewBox="0 0 20 20" 
                                    fill="currentColor"
                                >
                                    <path 
                                        fill-rule="evenodd" 
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" 
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>

                            <div
                                v-show="isDropdownOpen"
                                class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                            >
                                <Link
                                    :href="route('profile.candidate.show')"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Profile
                                </Link>
                                <Link
                                    :href="route('candidate.file-status')"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Document Status
                                </Link>
                                <hr class="my-1">
                                <Link
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                >
                                    Log Out
                                </Link>
                            </div>
                        </div>
                    </template>
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
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  isLogin: Boolean,
  role: String,
  user: Object,
})

const isDropdownOpen = ref(false)

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
}

const navigation = [
    { name: "Home", href: "/" },
    { name: "Company", href: "/company" },
    { name: "Product", href: "/product" },
    { name: "Features", href: "/features" },
];

const img = [{ id: "1", href: "./assets/images/for.png", link: "/" }];
</script>

<style scoped>
.rotate-180 {
  transform: rotate(180deg);
}
</style>
