<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import axios from "axios";
import { ref } from "vue";

const props = defineProps({
    title: String,
    candidateDetails: Object,
});

const getFileUrl = (type, path) => {
    const filename = path.split("/").pop();
    return route("candidate.file", {
        type: type,
        filename: filename,
        candidate_id: props.candidateDetails.id, // Access candidateDetails through props
    });
};

// Add success/error message handling
const message = ref("");
const errorMessage = ref("");

const updateFileStatus = async (fileType, status) => {
    try {
        const response = await axios.post(
            route("dashboard.update-file-status"),
            {
                candidate_id: props.candidateDetails.id,
                file_type: fileType,
                status: status,
            }
        );

        // Show success message
        message.value = response.data.message;

        // Optional: Refresh the page or update component state
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    } catch (error) {
        console.error("Error updating file status:", error);
        errorMessage.value =
            error.response?.data?.message || "Error updating file status";
    }
};

const formatDate = (date) => {
    if (!date) return "";
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
</script>

<template>
    <Head :title="title" />
    <Sidebar>
        <!-- Add success/error messages -->
        <div
            v-if="message"
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
        >
            {{ message }}
        </div>
        <div
            v-if="errorMessage"
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
        >
            {{ errorMessage }}
        </div>

        <div class="p-4">
            <div class="flex justify-between items-center mb-2">
                <h1 class="text-2xl font-semibold">Candidate Details</h1>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-semibold">NIK:</p>
                        <p>{{ candidateDetails.candidate_detail?.nik }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Full Name:</p>
                        <p>
                            {{ candidateDetails.candidate_detail?.full_name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Address:</p>
                        <p>{{ candidateDetails.candidate_detail?.address }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Birth Date:</p>
                        <p>
                            {{
                                formatDate(
                                    candidateDetails.candidate_detail
                                        ?.birth_date
                                )
                            }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Education:</p>
                        <p>
                            {{
                                candidateDetails.candidate_detail
                                    ?.education_level
                            }}
                            - {{ candidateDetails.candidate_detail?.major }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Institution:</p>
                        <p>
                            {{ candidateDetails.candidate_detail?.institution }}
                            ({{
                                candidateDetails.candidate_detail
                                    ?.graduation_year
                            }})
                        </p>
                    </div>
                </div>

                <!-- Document Preview Section -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                    <!-- Photo Preview -->
                    <div v-if="candidateDetails.candidate_detail?.photo_path">
                        <p class="text-sm font-semibold">Photo:</p>
                        <div class="flex flex-col gap-2 mt-2">
                            <a
                                :href="
                                    getFileUrl(
                                        'photo',
                                        candidateDetails.candidate_detail
                                            .photo_path
                                    )
                                "
                                target="_blank"
                                class="text-blue-600 hover:text-blue-800"
                            >
                                View Photo
                            </a>
                            <div class="flex items-center gap-2">
                                <select
                                    :value="
                                        candidateDetails.candidate_detail
                                            ?.photo_status
                                    "
                                    @change="
                                        updateFileStatus(
                                            'photo',
                                            $event.target.value
                                        )
                                    "
                                    class="rounded border-gray-300"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="accepted">Diterima</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                                <label class="text-sm text-gray-600"
                                    >Status Foto</label
                                >
                            </div>
                        </div>
                    </div>

                    <!-- CV Preview -->
                    <div v-if="candidateDetails.candidate_detail?.cv_path">
                        <p class="text-sm font-semibold">CV:</p>
                        <a
                            :href="
                                getFileUrl(
                                    'cv',
                                    candidateDetails.candidate_detail.cv_path
                                )
                            "
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800 mt-2 inline-block"
                        >
                            View CV
                        </a>
                        <div class="flex items-center gap-2 mt-2">
                            <select
                                :value="
                                    candidateDetails.candidate_detail?.cv_status
                                "
                                @change="
                                    updateFileStatus('cv', $event.target.value)
                                "
                                class="rounded border-gray-300"
                            >
                                <option value="pending">Pending</option>
                                <option value="accepted">Diterima</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                            <label class="text-sm text-gray-600"
                                >Status CV</label
                            >
                        </div>
                    </div>

                    <!-- Certificate Preview -->
                    <div
                        v-if="
                            candidateDetails.candidate_detail?.certificate_path
                        "
                    >
                        <p class="text-sm font-semibold">Certificate:</p>
                        <a
                            :href="
                                getFileUrl(
                                    'certificate',
                                    candidateDetails.candidate_detail
                                        .certificate_path
                                )
                            "
                            target="_blank"
                            class="text-blue-600 hover:text-blue-800 mt-2 inline-block"
                        >
                            View Certificate
                        </a>
                        <div class="flex items-center gap-2 mt-2">
                            <select
                                :value="
                                    candidateDetails.candidate_detail
                                        ?.certificate_status
                                "
                                @change="
                                    updateFileStatus(
                                        'certificate',
                                        $event.target.value
                                    )
                                "
                                class="rounded border-gray-300"
                            >
                                <option value="pending">Pending</option>
                                <option value="accepted">Diterima</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                            <label class="text-sm text-gray-600"
                                >Status Ijazah</label
                            >
                        </div>
                    </div>
                </div>
                <div class="flex gap-4 mt-6">
                    <Link
                        :href="route('dashboard.pending-files')"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded"
                    >
                        Back
                    </Link>
                </div>
            </div>
        </div>
    </Sidebar>
</template>
