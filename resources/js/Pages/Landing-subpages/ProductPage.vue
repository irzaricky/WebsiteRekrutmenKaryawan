<template>
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
                Solusi Keuangan untuk Masa Depan
            </p>
        </div>
        <p
            class="mx-auto mt-6 max-w-2xl text-center text-lg leading-8 text-gray-600"
        >
            Sebagai bank sentral Republik Indonesia, Bank Indonesia (BI)
            menghadirkan berbagai produk keuangan yang mendukung stabilitas
            moneter, sistem pembayaran efisien, serta layanan inovatif.
        </p>
        <div
            class="mx-auto mt-16 grid max-w-lg grid-cols-1 items-center gap-4 sm:gap-6 lg:gap-8 gap-y-6 sm:mt-20 sm:gap-y-0 lg:max-w-6xl lg:grid-cols-3"
        >
            <div
                v-for="(tier, tierIdx) in tiers"
                :key="tier.id"
                class="bg-white/60 sm:mx-8 lg:mx-0 rounded-3xl p-8 ring-1 ring-gray-900/10 sm:p-10"
            >
                <h3
                    :id="tier.id"
                    class="text-indigo-600 text-base font-semibold leading-7"
                >
                    {{ tier.name }}
                </h3>
                <p class="mt-6 text-base leading-7 text-gray-600">
                    {{ tier.description }}
                </p>
                <ul
                    role="list"
                    class="mt-8 space-y-3 text-sm leading-6 text-gray-600"
                >
                    <li
                        v-for="feature in tier.features"
                        :key="feature"
                        class="flex gap-x-3"
                    >
                        <CheckIcon
                            class="text-indigo-600 h-6 w-5 flex-none"
                            aria-hidden="true"
                        />
                        {{ feature }}
                    </li>
                </ul>
                <a
                    :href="tier.href"
                    :aria-describedby="tier.id"
                    class="mt-8 block rounded-md bg-indigo-500 text-white shadow-sm hover:bg-indigo-400 focus-visible:outline-indigo-500 px-3.5 py-2.5 text-center text-sm font-semibold focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 sm:mt-10"
                >
                    menuju website
                </a>
            </div>
        </div>
    </div>
    <BaseFooter />
</template>

<script setup>
import { CheckIcon } from "@heroicons/vue/20/solid";
import BaseHeader from "../../components/BaseHeader.vue";
import BaseFooter from "../../components/BaseFooter.vue";

const tiers = [
    {
        name: "Sistem Pembayaran Nasional",
        id: "tier-pembayaran",
        href: "https://www.bi.go.id/",
        description:
            "Memastikan keamanan dan efisiensi dalam setiap transaksi di seluruh pelosok negeri, sehingga memberikan kepercayaan penuh kepada masyarakat dan pelaku usaha.",
        features: [
            "Keamanan transaksi yang tinggi",
            "Efisiensi biaya",
            "Dukungan 24/7",
        ],
    },
    {
        name: "Perizinan & Pengawasan Perbankan",
        id: "tier-perbankan",
        href: "https://www.bi.go.id/",
        description:
            "BI berperan aktif dalam memastikan kesehatan dan ketahanan sektor perbankan Indonesia melalui pengawasan ketat dan perizinan.",
        features: [
            "Pengawasan yang ketat",
            "Proses perizinan yang cepat",
            "Dukungan untuk bank dan lembaga keuangan",
        ],
    },
    {
        name: "Edukasi & Informasi Keuangan",
        id: "tier-keuangan",
        href: "https://www.bi.go.id/",
        description:
            "Berbagai produk informasi yang membantu masyarakat memahami manajemen keuangan dan kebijakan moneter secara mendalam.",
        features: [
            "Sumber daya edukasi",
            "Pelatihan dan workshop",
            "Informasi terkini tentang kebijakan keuangan",
        ],
    },
];
</script>
