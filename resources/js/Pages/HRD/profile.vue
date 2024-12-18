<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";
import Sidebar from "../../components/Dashboard/Sidebar.vue";

const props = defineProps({
    title: String,
    user: Object,
    hrdDetail: Object,
    flash: Object,
});

// Helper function to format date
const formatDateForInput = (dateString) => {
    if (!dateString) return "";
    return dateString.split('T')[0];
};

const form = useForm({
    name: props.user?.name || "",
    email: props.user?.email || "",
    role: props.user?.role || "HRD",
    nik: props.hrdDetail?.nik || "",
    address: props.hrdDetail?.address || "",
    birth_date: formatDateForInput(props.hrdDetail?.birth_date) || "",
    profile_image: null
});

// Add preview handling
const imagePreview = ref(props.hrdDetail?.profile_image_url || null);
const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.profile_image = file;
        // Create preview URL
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route("hrd.profile.update"), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset('profile_image');
        },
    });
};
</script>

<template>
    <Head :title="title" />

    <Sidebar :title="title">
        <div class="p-4">
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

            <!-- Form -->
            <form
                @submit.prevent="submit"
                class="bg-white rounded-xl shadow-lg"
            >
                <div class="p-6 sm:p-8 space-y-10">
                    <!-- Profile Image Upload -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-6">
                            <!-- Profile Image Preview -->
                            <div class="shrink-0">
                                <img 
                                    :src="imagePreview || (props.hrdDetail?.profile_image_url || '/default-avatar.png')"
                                    class="h-24 w-24 object-cover rounded-full"
                                    alt="Profile photo"
                                />
                            </div>
                            
                            <!-- Upload Button -->
                            <label class="block">
                                <span class="block text-sm font-medium text-gray-700 mb-2">
                                    Change Profile Photo
                                </span>
                                <input 
                                    type="file"
                                    @change="handleImageUpload"
                                    class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-full file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-blue-50 file:text-blue-700
                                        hover:file:bg-blue-100"
                                    accept="image/*"
                                />
                                <div v-if="form.errors.profile_image" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.profile_image }}
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">
                            Personal Information
                        </h2>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6"
                        >
                            <!-- Name -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Full Name
                                </label>
                                <input
                                    type="text"
                                    v-model="form.name"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                />
                                <div
                                    v-if="form.errors.name"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Email
                                </label>
                                <input
                                    type="email"
                                    v-model="form.email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                />
                                <div
                                    v-if="form.errors.email"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- NIK -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    NIK
                                </label>
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

                            <!-- Birth Date -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Birth Date
                                </label>
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

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Address
                                </label>
                                <input
                                    v-model="form.address"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                ></input>
                                <div
                                    v-if="form.errors.address"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.address }}
                                </div>
                            </div>

                            <!-- Role (Disabled) -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Role
                                </label>
                                <input
                                    type="text"
                                    v-model="form.role"
                                    disabled
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end pt-8 border-t border-gray-200">
                        <div class="flex space-x-4">
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
    </Sidebar>
</template>
