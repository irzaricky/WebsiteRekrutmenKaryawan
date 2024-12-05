<script setup>
import { onMounted, ref } from "vue";

const props = defineProps({
    siteKey: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(["verify", "expire"]);
const divRef = ref(null);

onMounted(() => {
    const recaptchaScript = document.createElement("script");
    recaptchaScript.setAttribute(
        "src",
        "https://www.google.com/recaptcha/api.js"
    );
    document.head.appendChild(recaptchaScript);

    window.onRecaptchaVerify = (token) => {
        emit("verify", token);
    };
    window.onRecaptchaExpire = () => {
        emit("expire");
    };
});
</script>

<template>
    <div
        ref="divRef"
        class="g-recaptcha"
        :data-sitekey="siteKey"
        data-callback="onRecaptchaVerify"
        data-expired-callback="onRecaptchaExpire"
    ></div>
</template>
