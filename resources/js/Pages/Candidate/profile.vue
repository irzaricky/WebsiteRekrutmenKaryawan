<script setup>
import BaseHeader from "@/components/BaseHeader.vue";
import BaseFooter from "@/components/BaseFooter.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import { onMounted } from "vue";

const props = defineProps({
    title: String,
    user: Object,
    candidateDetail: Object,
    flash: Object,
});

const getFilename = (path) => {
    if (!path) return "";
    return path.split("/").pop();
};

const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toISOString().split("T")[0];
};

const form = useForm({
    nik: props.candidateDetail?.nik || "",
    birth_date: formatDate(props.candidateDetail?.birth_date) || "",
    address: props.candidateDetail?.address || "",
    education_level: props.candidateDetail?.education_level || "",
    major: props.candidateDetail?.major || "",
    institution: props.candidateDetail?.institution || "",
    graduation_year: props.candidateDetail?.graduation_year || "",
});

const submit = () => {
    form.post(route("candidate.profile.update"), {
        preserveScroll: true,
        forceFormData: true,
    });
};
</script>

<template>
    <Head :title="title" />

    <main class="min-h-screen py-4 lg:py-6">
        <!-- Gradient background -->
        <div
            class="absolute inset-x-0 top-0 -z-10 transform-gpu overflow-hidden px-36 blur-3xl"
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

        <div class="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <!-- Messages -->
            <div class="mb-10 space-y-4">
                <div
                    v-if="$page.props.flash.success"
                    class="p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg"
                >
                    {{ $page.props.flash.success }}
                </div>

                <div
                    v-if="$page.props.flash.error"
                    class="p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg"
                >
                    {{ $page.props.flash.error }}
                </div>
            </div>

            <!-- Page Title -->
            <div class="mb-10">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    Profile
                </h1>
            </div>

            <!-- Form -->
            <form
                @submit.prevent="submit"
                class="bg-white rounded-xl shadow-lg"
            >
                <div class="p-6 sm:p-8 space-y-10">
                    <!-- Personal Information -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">
                            Personal Information
                        </h2>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6"
                        >
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >NIK</label
                                >
                                <input
                                    type="text"
                                    v-model="form.nik"
                                    maxlength="16"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                />
                                <div
                                    v-if="form.errors.nik"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.nik }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Nama Lengkap</label
                                >
                                <input
                                    type="text"
                                    v-model="props.user.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                    disabled
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Tanggal Lahir</label
                                >
                                <input
                                    type="date"
                                    v-model="form.birth_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                />
                                <div
                                    v-if="form.errors.birth_date"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.birth_date }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Alamat</label
                                >
                                <textarea
                                    v-model="form.address"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                ></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Education Information -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">
                            Education Information
                        </h2>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6"
                        >
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Pendidikan Terakhir</label
                                >
                                <select
                                    v-model="form.education_level"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                >
                                    <option value="">Pilih Pendidikan</option>
                                    <option value="SMA">SMA/SMK</option>
                                    <option value="D3">D3</option>
                                    <option value="S1">S1</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Jurusan</label
                                >
                                <input
                                    type="text"
                                    v-model="form.major"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Institusi</label
                                >
                                <input
                                    type="text"
                                    v-model="form.institution"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                />
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Tahun Lulus</label
                                >
                                <input
                                    type="number"
                                    v-model="form.graduation_year"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end pt-8 border-t border-gray-200">
                        <div class="flex space-x-4">
                            <Link
                                href="/"
                                class="px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out"
                            >
                                Back
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out"
                            >
                                {{
                                    form.processing
                                        ? "Updating..."
                                        : "Update Profile"
                                }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</template>
