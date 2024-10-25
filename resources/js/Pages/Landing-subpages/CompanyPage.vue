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

        <div class="my-64">
            <div class="mx-auto max-w-2xl text-center lg:max-w-4xl">
                <p
                    class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl"
                >
                    Tentang Bank Indonesia
                </p>
            </div>
            <p
                class="mx-auto mt-6 max-w-2xl text-center text-lg leading-8 text-gray-600"
            >
                Penjaga Stabilitas Ekonomi Nasional Didirikan sebagai bank
                sentral Republik Indonesia, Bank Indonesia memiliki misi utama
                untuk mencapai dan memelihara kestabilan nilai rupiah. Tugas
                kami tidak hanya terbatas pada pengelolaan moneter, tetapi juga
                mencakup sistem pembayaran, pengawasan sektor keuangan, serta
                menjaga stabilitas ekonomi nasional.
            </p>
        </div>
    </div>
    <BaseFooter />
</template>

<script setup>
import BaseHeader from "../../components/BaseHeader.vue";
import BaseFooter from "../../components/BaseFooter.vue";

const data = [
    {
        name: "Moneter",
        id: "0",
        href: "https://www.bi.go.id/",
        priceMonthly: "$69",
        description:
            "Memastikan keamanan dan efisiensi dalam setiap transaksi di seluruh pelosok negeri, sehingga memberikan kepercayaan penuh kepada masyarakat dan pelaku usaha.",
        features: [
            "Keamanan transaksi yang tinggi",
            "Efisiensi biaya",
            "Dukungan 24/7",
        ],
    },
];
</script>
