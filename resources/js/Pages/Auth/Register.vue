<script setup>
import BaseHeader from "@/components/BaseHeader.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: "Candidate",
    status: "active",
    nik: "",
});

const img = [{ id: "1", href: "./assets/images/for.png", link: "/" }];

defineProps({
    title: {
        type: String,
    },
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head :title="title" />
    <div class="relative isolate bg-white py-4">
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
        <div
            class="mx-auto mt-24 mb-24 w-full overflow-hidden bg-white px-6 py-4 shadow-md sm:max-w-md sm:rounded-lg"
        >
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
                    <InputLabel for="name" value="Name" />

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        style="
                            padding-top: 0.375rem;
                            padding-bottom: 0.375rem;
                            line-height: 1.25;
                        "
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                    />

                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="mt-4">
                    <InputLabel for="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        style="
                            padding-top: 0.375rem;
                            padding-bottom: 0.375rem;
                            line-height: 1.25;
                        "
                        v-model="form.email"
                        required
                        autocomplete="username"
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="mt-4">
                    <InputLabel for="nik" value="NIK" />
                    <TextInput
                        id="nik"
                        type="text"
                        class="mt-1 block w-full"
                        style="
                            padding-top: 0.375rem;
                            padding-bottom: 0.375rem;
                            line-height: 1.25;
                        "
                        v-model="form.nik"
                        maxlength="16"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.nik" />
                </div>

                <div class="mt-4">
                    <InputLabel for="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        style="
                            padding-top: 0.375rem;
                            padding-bottom: 0.375rem;
                            line-height: 1.25;
                        "
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="mt-4">
                    <InputLabel
                        for="password_confirmation"
                        value="Confirm Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        style="
                            padding-top: 0.375rem;
                            padding-bottom: 0.375rem;
                            line-height: 1.25;
                        "
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />

                    <InputError
                        class="mt-2"
                        :message="form.errors.password_confirmation"
                    />
                </div>

                <div class="mt-4 flex items-center justify-end">
                    <Link
                        :href="route('login')"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Already registered?
                    </Link>

                    <PrimaryButton
                        class="ms-4"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Register
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
