<template>
    <Head :title="title" />
    <BaseHeader />
    <div class="relative isolate bg-white px-6 py-24 sm:py-32 lg:px-8">
        <div
            class="absolute inset-x-0 -top-3 -z-10 transform-gpu overflow-hidden px-36 blur-3xl"
            aria-hidden="true"
        >
            <div
                class="mx-auto aspect-[1155/678] w-[72.1875rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30"
                style="
                    clip-path: polygon(
                        74.1% 44.1%,
                        100% 61.6%,
                        97.5% 26.9%,
                        85.5% 0.1%,
                        80.7% 2%,
                        72.5% 32.5%,
                        60.2% 62.4%,
                        52.4% 68.1%,
                        47.5% 58.3%,
                        45.2% 34.5%,
                        27.5% 76.7%,
                        0.1% 64.9%,
                        17.9% 100%,
                        27.6% 76.8%,
                        76.1% 97.7%,
                        74.1% 44.1%
                    );
                "
            />
        </div>
        <div class="mx-auto max-w-2xl text-center lg:max-w-4xl">
            <p
                class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl"
            >
                Fitur Utama dari Layanan Bank Indonesia
            </p>
        </div>
        <p
            class="mx-auto mt-6 max-w-2xl text-center text-lg leading-8 text-gray-600"
        >
            Sebagai lembaga terdepan dalam menjaga stabilitas ekonomi Indonesia,
            Bank Indonesia menyediakan fitur-fitur inovatif yang dirancang untuk
            kemudahan dan keandalan dalam bertransaksi serta mengelola kebijakan
            keuangan nasional
        </p>

        <div class="flex flex-wrap">
            <div
                v-for="(data, tierIdx) in data.slice(0, 3)"
                :key="data.id"
                class="lg:pt-12 pt-6 w-full md:w-4/12 px-4 text-center lg:grid-cols-3"
            >
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white w-full mb-8 shadow-lg rounded-lg"
                >
                    <div class="px-4 py-5 flex-auto">
                        <div class="avatar">
                            <div class="w-24 rounded-full">
                                <img :src="data.href" />
                            </div>
                        </div>
                        <h6 class="text-xl font-semibold text-black">
                            {{ data.name }}
                        </h6>
                        <p class="mt-2 mb-4 text-gray-600">
                            {{ data.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-center items-center">
            <div
                v-for="(data, tierIdx) in data.slice(3, 5)"
                :key="data.id"
                class="lg:pt-12 pt-6 w-full md:w-4/12 px-4 text-center lg:grid-cols-2"
            >
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white w-full mb-8 shadow-lg rounded-lg"
                >
                    <div class="px-4 py-5 flex-auto">
                        <div class="avatar">
                            <div class="w-24 rounded-full">
                                <img :src="data.href" />
                            </div>
                        </div>
                        <h6 class="text-xl font-semibold text-black">
                            {{ data.name }}
                        </h6>
                        <p class="mt-2 mb-4 text-gray-600">
                            {{ data.description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <BaseFooter />
</template>

<script setup>
import BaseHeader from "../../components/BaseHeader.vue";
import BaseFooter from "../../components/BaseFooter.vue";
import { Head } from "@inertiajs/vue3";
defineProps({
    title: {
        type: String,
    },
});

const data = [
    {
        name: "Sistem BI-FAST",
        id: "0",
        href: "./assets/images/features_page/bi_fast_icon.png",
        description:
            "Sistem pembayaran real-time yang mendukung transaksi antarbank dengan biaya rendah dan keamanan tinggi.",
    },
    {
        name: "QRIS",
        id: "1",
        href: "./assets/images/features_page/qris_icon.png",
        description:
            "Standar pembayaran digital yang memudahkan konsumen dan merchant untuk bertransaksi dengan mudah, cepat, dan aman.",
    },
    {
        name: "Kebijakan Moneter Responsif",
        id: "2",
        href: "./assets/images/features_page/rupiah_icon.png",
        description:
            "Mengelola inflasi dan menjaga stabilitas kurs rupiah dengan berbagai instrumen kebijakan yang dinamis dan responsif.",
    },
    {
        name: "Perizinan Digital",
        id: "3",
        href: "./assets/images/features_page/izin_digital_icon.png",
        description:
            "Proses perizinan yang cepat dan efisien melalui sistem elektronik untuk mendukung pertumbuhan sektor keuangan.",
    },
    {
        name: "Layanan Data & Statistik",
        id: "4",
        href: "./assets/images/features_page/statistik_icon.png",
        description:
            "Menyediakan berbagai data dan analisis ekonomi serta keuangan untuk mendukung kebijakan yang akurat dan transparan.",
    },
];
</script>
