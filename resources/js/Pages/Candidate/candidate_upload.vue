<script setup>
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, useForm } from "@inertiajs/vue3";
import axios from "axios";

const props = defineProps({
    title: String,
    candidateDetail: Object,
});

const form = useForm({
    nik: props.candidateDetail?.nik || "",
    full_name: props.candidateDetail?.full_name || "",
    address: props.candidateDetail?.address || "",
    birth_date: props.candidateDetail?.birth_date || "",
    education_level: props.candidateDetail?.education_level || "",
    major: props.candidateDetail?.major || "",
    institution: props.candidateDetail?.institution || "",
    graduation_year: props.candidateDetail?.graduation_year || "",
    photo: null,
    cv: null,
    certificate: null,
});

const submit = () => {
    form.post(route("candidate.upload"), {
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
        // Refresh data
        window.location.reload();
    } catch (error) {
        console.error("Error removing file:", error);
    }
};
</script>

<template>
    <Head :title="title" />
    <Sidebar>
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-bold mb-6">Data Kandidat</h1>

            <form
                @submit.prevent="submit"
                class="space-y-6 bg-white rounded-lg shadow p-6"
            >
                <!-- Personal Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Nama Lengkap</label
                        >
                        <input
                            type="text"
                            v-model="form.full_name"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
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
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                            min="1900"
                            :max="new Date().getFullYear()"
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
                        <!-- Preview foto yang sudah diupload -->
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

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >CV (PDF)</label
                        >
                        <!-- Preview CV yang sudah diupload -->
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

                    <div>
                        <label class="block text-sm font-medium text-gray-700"
                            >Ijazah (PDF)</label
                        >
                        <!-- Preview ijazah yang sudah diupload -->
                        <div
                            v-if="candidateDetail?.certificate_path"
                            class="mt-2"
                        >
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
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    >
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </Sidebar>
</template>
