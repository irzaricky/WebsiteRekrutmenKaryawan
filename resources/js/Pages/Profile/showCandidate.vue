<script setup>
import { Head, useForm, Link } from "@inertiajs/vue3";
import { onMounted } from "vue";

const props = defineProps({
    title: String,
    user: Object,
    candidateDetail: Object,
    flash: Object, // Add this to receive flash messages
});

// Format the date to YYYY-MM-DD
const formatDate = (dateString) => {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toISOString().split("T")[0];
};

const form = useForm({
    nik: props.candidateDetail?.nik || "",
    full_name: props.user?.name || "",
    birth_date: formatDate(props.candidateDetail?.birth_date) || "", // Format the date
    address: props.candidateDetail?.address || "",
    education_level: props.candidateDetail?.education_level || "",
    major: props.candidateDetail?.major || "",
    institution: props.candidateDetail?.institution || "",
    graduation_year: props.candidateDetail?.graduation_year || "",
    photo: null,
    cv: null,
    certificate: null,
});

const submit = () => {
    form.post(route("profile.candidate.update"), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset("photo", "cv", "certificate");
        },
    });
};
</script>

<template>
    <Head :title="title" />

    <div class="container mx-auto p-6">
        <!-- Success Message -->
        <div
            v-if="$page.props.flash.success"
            class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded"
        >
            {{ $page.props.flash.success }}
        </div>

        <!-- Error Message -->
        <div
            v-if="$page.props.flash.error"
            class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded"
        >
            {{ $page.props.flash.error }}
        </div>

        <div class="flex items-center gap-4 mb-6">
            <h1 class="text-2xl font-bold">Profile</h1>
        </div>

        <!-- Add form enctype for file uploads -->
        <form
            @submit.prevent="submit"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            <div class="bg-white rounded-lg shadow p-6">
                <!-- Personal Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
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
                        <label class="block text-sm font-medium text-gray-700"
                            >Nama Lengkap</label
                        >
                        <input
                            type="text"
                            v-model="form.full_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            disabled
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
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
                        <label class="block text-sm font-medium text-gray-700"
                            >Alamat</label
                        >
                        <textarea
                            v-model="form.address"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        ></textarea>
                    </div>
                </div>

                <!-- Education Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
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
                        <label class="block text-sm font-medium text-gray-700"
                            >Jurusan</label
                        >
                        <input
                            type="text"
                            v-model="form.major"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Institusi</label
                        >
                        <input
                            type="text"
                            v-model="form.institution"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Tahun Lulus</label
                        >
                        <input
                            type="number"
                            v-model="form.graduation_year"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        />
                    </div>
                </div>

                <!-- Document Uploads -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Foto</label
                        >
                        <input
                            type="file"
                            @input="form.photo = $event.target.files[0]"
                            accept="image/*"
                            class="mt-1 block w-full"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >CV (PDF)</label
                        >
                        <input
                            type="file"
                            @input="form.cv = $event.target.files[0]"
                            accept=".pdf"
                            class="mt-1 block w-full"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Ijazah (PDF)</label
                        >
                        <input
                            type="file"
                            @input="form.certificate = $event.target.files[0]"
                            accept=".pdf"
                            class="mt-1 block w-full"
                        />
                    </div>
                </div>

                <!-- Add error display -->
                <div v-if="form.errors" class="text-red-500 mt-2">
                    <div v-for="(error, key) in form.errors" :key="key">
                        {{ error }}
                    </div>
                </div>

                <div class="flex justify-end mt-6 space-x-4">
                    <Link
                        href="/"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out"
                    >
                        Back
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-md transition-colors duration-150 ease-in-out"
                    >
                        {{ form.processing ? "Updating..." : "Update Profile" }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>
