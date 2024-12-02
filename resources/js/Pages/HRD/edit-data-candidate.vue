<script setup lang="ts">
import Sidebar from "../../components/Dashboard/Sidebar.vue";
import { Head, useForm, Link } from "@inertiajs/vue3"; // Add Link import
const props = defineProps({
    title: {
        type: String,
    },
    candidate: {
        type: Object,
        required: true,
    },
    testResults: {
        type: Array,
        required: true,
    },
    tests: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    test_results: props.testResults.reduce((acc: any, result: any) => {
        acc[result.test_id] = result.score;
        return acc;
    }, {}),
});

const submitForm = () => {
    form.put(route("dashboard.data-candidate-put", props.candidate.id), {
        test_results: form.test_results,
    });
};
</script>
<template>
    <Head :title="title" />
    <Sidebar :title="title">
        <form
            @submit.prevent="submitForm"
            class="w-full max-w mx-auto my-20 bg-gray-200 p-16 rounded-[50px]"
        >
            <!-- Data Kandidat (Tidak bisa diubah) -->
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <label
                        class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                    >
                        Nama
                    </label>
                    <input
                        type="text"
                        :value="candidate.name"
                        disabled
                        class="appearance-none block w-full bg-gray-100 text-gray-700 border rounded py-3 px-4 mb-3 leading-tight"
                    />
                </div>
                <div class="w-full md:w-1/2 px-3">
                    <label
                        class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                    >
                        Email
                    </label>
                    <input
                        type="email"
                        :value="candidate.email"
                        disabled
                        class="appearance-none block w-full bg-gray-100 text-gray-700 border rounded py-3 px-4 leading-tight"
                    />
                </div>
            </div>
            <!-- Nilai Test (Bisa diubah) -->
            <div class="flex flex-wrap -mx-3 mb-6">
                <div
                    v-for="test in tests"
                    :key="test.id"
                    class="w-full md:w-1/2 px-3 mb-6"
                >
                    <label
                        :for="'test_' + test.id"
                        class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                    >
                        {{ test.name }}
                    </label>
                    <input
                        :id="'test_' + test.id"
                        type="number"
                        v-model="form.test_results[test.id]"
                        class="block w-full bg-gray-300 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:bg-white focus:border-gray-500"
                    />
                </div>
            </div>

            <!-- Button group at bottom of form -->
            <div class="flex gap-4 mt-6">
                <Link
                    :href="route('dashboard.data-candidate')"
                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2.5 rounded-md text-sm"
                >
                    Back
                </Link>
                <button
                    type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2.5 rounded-md text-sm"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </Sidebar>
</template>
