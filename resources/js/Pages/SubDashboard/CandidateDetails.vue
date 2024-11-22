<script setup>
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, Link } from "@inertiajs/vue3";

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
</script>

<template>
    <Head :title="title" />
    <Sidebar>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
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
                            {{ candidateDetails.candidate_detail?.birth_date }}
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
                        <img
                            :src="
                                getFileUrl(
                                    'photo',
                                    candidateDetails.candidate_detail.photo_path
                                )
                            "
                            class="h-32 w-32 object-cover rounded mt-2"
                        />
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
                    </div>
                </div>
                <div class="flex gap-4 mt-6">
                    <Link
                        :href="route('dashboard.data-candidate')"
                        class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded"
                    >
                        Back
                    </Link>
                </div>
            </div>
        </div>
    </Sidebar>
</template>
