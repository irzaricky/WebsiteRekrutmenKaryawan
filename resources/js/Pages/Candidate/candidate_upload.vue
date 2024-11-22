<script setup>
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    title: String,
    candidateDetail: Object,
    errors: Object,
});

const form = useForm({
    photo: null,
    cv: null,
    certificate: null,
});

const submit = () => {
    form.post(route("candidate.files.update"), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset("photo", "cv", "certificate");
        },
    });
};

const getFilename = (path) => {
    return path.split("/").pop();
};

const removeFile = async (type) => {
    try {
        await axios.delete(route("candidate.file.delete"), { data: { type } });
        window.location.reload();
    } catch (error) {
        console.error("Error removing file:", error);
    }
};
</script>

<template>
    <Head :title="title" />
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Upload Dokumen</h1>

        <!-- Flash Messages -->
        <div
            v-if="$page.props.flash.message"
            :class="{
                'bg-yellow-100 border-yellow-400 text-yellow-700':
                    $page.props.flash.type === 'warning',
                'bg-green-100 border-green-400 text-green-700':
                    $page.props.flash.type === 'success',
                'bg-red-100 border-red-400 text-red-700':
                    $page.props.flash.type === 'error',
            }"
            class="p-4 mb-4 rounded border"
        >
            {{ $page.props.flash.message }}
        </div>

        <!-- Validation Errors -->
        <div
            v-if="Object.keys(form.errors).length > 0"
            class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md mb-4"
        >
            <p class="font-semibold mb-2">Please fix the following errors:</p>
            <ul class="list-disc list-inside">
                <li v-for="(error, key) in form.errors" :key="key">
                    {{ error }}
                </li>
            </ul>
        </div>

        <form
            @submit.prevent="submit"
            class="space-y-6 bg-white rounded-lg shadow p-6"
            enctype="multipart/form-data"
        >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Photo Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        >Foto</label
                    >
                    <div v-if="candidateDetail?.photo_path" class="mt-2">
                        <img
                            :src="
                                route('candidate.file', {
                                    type: 'photo',
                                    filename: getFilename(
                                        candidateDetail.photo_path
                                    ),
                                })
                            "
                            class="h-32 w-32 object-cover rounded"
                        />
                        <button
                            @click="removeFile('photo')"
                            class="text-red-600 text-sm mt-1 hover:text-red-800"
                        >
                            Hapus Foto
                        </button>
                    </div>
                    <input
                        type="file"
                        @input="form.photo = $event.target.files[0]"
                        accept="image/*"
                        class="mt-1 block w-full"
                    />
                </div>

                <!-- CV Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        >CV (PDF)</label
                    >
                    <div v-if="candidateDetail?.cv_path" class="mt-2">
                        <a
                            :href="
                                route('candidate.file', {
                                    type: 'cv',
                                    filename: getFilename(
                                        candidateDetail.cv_path
                                    ),
                                })
                            "
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800"
                        >
                            Lihat CV
                        </a>
                        <button
                            @click="removeFile('cv')"
                            class="text-red-600 text-sm ml-2 hover:text-red-800"
                        >
                            Hapus CV
                        </button>
                    </div>
                    <input
                        type="file"
                        @input="form.cv = $event.target.files[0]"
                        accept=".pdf"
                        class="mt-1 block w-full"
                    />
                </div>

                <!-- Certificate Upload -->
                <div>
                    <label class="block text-sm font-medium text-gray-700"
                        >Ijazah (PDF)</label
                    >
                    <div v-if="candidateDetail?.certificate_path" class="mt-2">
                        <a
                            :href="
                                route('candidate.file', {
                                    type: 'certificate',
                                    filename: getFilename(
                                        candidateDetail.certificate_path
                                    ),
                                })
                            "
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800"
                        >
                            Lihat Ijazah
                        </a>
                        <button
                            @click="removeFile('certificate')"
                            class="text-red-600 text-sm ml-2 hover:text-red-800"
                        >
                            Hapus Ijazah
                        </button>
                    </div>
                    <input
                        type="file"
                        @input="form.certificate = $event.target.files[0]"
                        accept=".pdf"
                        class="mt-1 block w-full"
                    />
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    :class="{
                        'opacity-50 cursor-not-allowed': form.processing,
                    }"
                    class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                >
                    {{ form.processing ? "Uploading..." : "Upload Files" }}
                </button>
            </div>
        </form>
    </div>
</template>
