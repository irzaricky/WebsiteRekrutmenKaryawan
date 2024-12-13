<script setup>
import BaseHeader from "@/components/BaseHeader.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/components/Checkbox.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import ReCaptcha from "@/Components/ReCaptcha.vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    title: {
        type: String,
    },
});

const img = [{ id: "1", href: "./assets/images/for.png", link: "/" }];

const form = useForm({
    email: "",
    password: "",
    remember: false,
    recaptcha: "",
});

const handleRecaptchaVerify = (token) => {
    form.recaptcha = token;
};

const handleRecaptchaExpire = () => {
    form.recaptcha = "";
};

const submit = () => {
    form.post(route("login"), {
        onFinish: () => {
            form.reset("password");
            window.grecaptcha?.reset();
        },
    });
};
</script>

<template>
    <Head :title="title" />
    <!-- Add min-h-screen and flex classes to main container -->
    <div
        class="relative isolate min-h-screen flex items-center bg-white px-6 py-12 sm:py-16 lg:px-8"
    >
        <!-- Keep gradient background -->
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
            />
        </div>
        <!-- Modify login container -->
        <div
            class="w-full mx-auto overflow-hidden bg-white px-6 py-8 shadow-md sm:max-w-md sm:rounded-lg"
        >
            <!-- Rest of the form content remains the same -->
            <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                {{ status }}
            </div>
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
            <form @submit.prevent="submit">
                <div>
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <Link
                        href="/register"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Belum register?
                    </Link>
                </div>

                <div class="mt-4">
                    <ReCaptcha
                        site-key="6LcrhJMqAAAAAPczKz8OfJ3RUdAO9x8jxhL5qI-T"
                        @verify="handleRecaptchaVerify"
                        @expire="handleRecaptchaExpire"
                    />
                    <InputError class="mt-2" :message="form.errors.recaptcha" />
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <label class="flex items-center">
                        <Checkbox
                            name="remember"
                            v-model:checked="form.remember"
                        />
                        <span class="ms-2 text-sm text-gray-600"
                            >Remember me</span
                        >
                    </label>
                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Log in
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
