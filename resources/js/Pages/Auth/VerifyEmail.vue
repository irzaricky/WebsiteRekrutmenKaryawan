<script setup>
import { computed } from "vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    status: {
        type: String,
    },
    title: String,
});

const img = [{ id: "1", href: "./assets/images/for.png", link: "/" }];

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent"
);
</script>

<template>
    <Head :title="title || 'Email Verification'" />

    <div
        class="relative isolate min-h-screen flex items-center bg-white px-6 py-12 sm:py-16 lg:px-8"
    >
        <!-- Gradient background -->
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
            ></div>
        </div>

        <!-- Main content -->
        <div
            class="w-full mx-auto overflow-hidden bg-white px-6 py-8 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <!-- Logo -->
            <div class="flex justify-center items-center my-4">
                <Link
                    :href="img[0].link"
                    class="-m-1.5 p-1.5 btn btn-ghost avatar"
                    style="height: 5rem"
                >
                    <img
                        :src="img[0].href"
                        class="h-16 w-auto avatar"
                        alt="Recruiter Logo"
                    />
                </Link>
            </div>

            <!-- Email verification message -->
            <div class="text-center mb-8">
                <h2
                    class="text-2xl font-bold tracking-tight text-gray-900 mb-4"
                >
                    Verify Your Email
                </h2>
                <p class="text-sm text-gray-600">
                    Thanks for signing up! Before getting started, please verify
                    your email address by clicking on the link we just emailed
                    to you.
                </p>
            </div>

            <!-- Success message -->
            <div
                v-if="verificationLinkSent"
                class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm text-center"
            >
                A new verification link has been sent to your email address.
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Resend button -->
                <PrimaryButton
                    type="submit"
                    class="w-full justify-center"
                    :disabled="form.processing"
                >
                    {{
                        form.processing
                            ? "Sending..."
                            : "Resend Verification Email"
                    }}
                </PrimaryButton>

                <!-- Logout link -->
                <div class="text-center">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-sm text-gray-600 hover:text-gray-900 underline"
                    >
                        Log Out
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
