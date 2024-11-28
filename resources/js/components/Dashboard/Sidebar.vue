<script setup>
import DashboardHeader from "./Header.vue";
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import "bootstrap-icons/font/bootstrap-icons.css";

const props = defineProps({
    title: String, // Add title prop
});

const isCollapsed = ref(false);
const user = usePage().props.auth.user; // Get logged in user data

function toggleSidebar() {
    isCollapsed.value = !isCollapsed.value;
}

const navigation = [
    { name: "Beranda", href: "/dashboard" },
    { name: "Edit Nilai candidate", href: "/dashboard/data-candidate" },
    { name: "Konfirmasi Files candidate", href: "/dashboard/pending-files" },
    { name: "Histori HRD", href: "/dashboard/history" },
    { name: "Ranking candidate", href: "/dashboard/ranking" },
];

const img = [{ id: "1", href: "/assets/images/for.png", link: "/" }];
</script>

<template>
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div
            :class="[
                'bg-gray-800 min-h-screen p-7 pt-4 relative transition-all   top-0',
                {
                    'w-64 duration-700': !isCollapsed,
                    'w-28 duration-700': isCollapsed,
                },
            ]"
        >
            <!-- Toggle Button -->
            <i
                class="bi bi-arrow-left text-black bg-white absolute -right-3.5 top-96 border border-gray-300 cursor-pointer duration-500 rounded-full w-8 h-8 flex items-center justify-center"
                :class="{ 'rotate-180 duration-500': isCollapsed }"
                @click="toggleSidebar"
            ></i>

            <!-- Sidebar Content -->
            <div class="flex items-center gap-6">
                <Link
                    :href="img[0].link"
                    class="p-1.5 btn avatar bg-white duration-500"
                    style="height: 4rem"
                >
                    <img
                        :src="img[0].href"
                        class="h-16 w-auto avatar"
                        alt="Recruiter Logo"
                    />
                </Link>
                <div v-if="!isCollapsed">
                    <span class="text-white font-bold text-2xl">Recruiter</span>
                </div>
            </div>

            <div v-if="!isCollapsed">
                <ul class="text-white mt-6">
                    <li
                        v-for="item in navigation"
                        :key="item.name"
                        class="mb-3"
                    >
                        <Link
                            :href="item.href"
                            class="flex items-center text-sm font-semibold leading-10 text-white rounded-md hover:bg-gray-700 p-2 transition duration-300"
                        >
                            {{ item.name }}
                        </Link>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1">
            <!-- Header with title -->
            <DashboardHeader :user="user" :title="title" />

            <!-- Page Content -->
            <div class="p-6 pt-3">
                <slot />
            </div>
        </div>
    </div>
</template>
