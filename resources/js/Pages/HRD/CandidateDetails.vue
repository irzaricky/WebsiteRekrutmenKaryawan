<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import axios from "axios";
import { ref, computed } from "vue";

const props = defineProps({
    title: String,
    candidateDetails: Object,
});

const getFileUrl = (type, path) => {
    const filename = path.split("/").pop();
    return route("candidate.file", {
        type: type,
        filename: filename,
        candidate_id: props.candidateDetails.id,
    });
};

// Add success/error message handling
const message = ref("");
const errorMessage = ref("");

// Add computed property for required ijazah
const requiredIjazah = computed(() => {
    const level = props.candidateDetails?.candidate_detail?.education_level;
    switch (level) {
        case "SMA":
            return ["smp", "sma"];
        case "D3":
            return ["smp", "sma", "d3"];
        case "S1":
            return ["smp", "sma", "s1"];
        case "S2":
            return ["smp", "sma", "s1", "s2"];
        case "S3":
            return ["smp", "sma", "s1", "s2", "s3"];
        default:
            return [];
    }
});

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
    <Sidebar :title="title">
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
            <div class="bg-white rounded-lg shadow p-6">
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-semibold">NIK:</p>
                        <p>{{ candidateDetails.candidate_detail?.nik }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Full Name:</p>
                        <p>{{ candidateDetails.name }}</p>
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

                    <!-- Ijazah Preview -->
                    <div
                        v-for="level in requiredIjazah"
                        :key="level"
                        class="mt-6"
                    >
                        <p class="text-sm font-semibold">
                            Ijazah {{ level.toUpperCase() }}:
                        </p>
                        <div
                            v-if="
                                candidateDetails.candidate_detail?.[
                                    `ijazah_${level}_path`
                                ]
                            "
                        >
                            <a
                                :href="
                                    getFileUrl(
                                        `ijazah_${level}`,
                                        candidateDetails.candidate_detail[
                                            `ijazah_${level}_path`
                                        ]
                                    )
                                "
                                target="_blank"
                                class="text-blue-600 hover:text-blue-800 mt-2 inline-block"
                            >
                                View Ijazah {{ level.toUpperCase() }}
                            </a>
                            <div class="flex items-center gap-2 mt-2">
                                <select
                                    :value="
                                        candidateDetails.candidate_detail[
                                            `ijazah_${level}_status`
                                        ]
                                    "
                                    @change="
                                        updateFileStatus(
                                            `ijazah_${level}`,
                                            $event.target.value
                                        )
                                    "
                                    class="rounded border-gray-300"
                                >
                                    <option value="pending">Pending</option>
                                    <option value="accepted">Diterima</option>
                                    <option value="rejected">Ditolak</option>
                                </select>
                                <label class="text-sm text-gray-600">
                                    Status Ijazah {{ level.toUpperCase() }}
                                </label>
                            </div>
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
