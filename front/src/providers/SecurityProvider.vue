<script setup>
import { ref, reactive, provide, onMounted } from "vue";
import LoadingElement from "@/components/staples/LoadingElement.vue";
import { SECURITY_currentUser } from "./ProviderKeys";
import { useRoute } from "vue-router";
import { Security } from "@/api/security";

const $route = useRoute();
const loading = ref(true);

const currentUser = reactive({});
const setCurrentUser = (data) => {
    Object.assign(currentUser, data);
}

provide(SECURITY_currentUser, {
    currentUser,
    setCurrentUser
});

const security = new Security();
onMounted(async () => {
    const token = localStorage.getItem('token');
    if (token) {
        const user = await security.profile();
        setCurrentUser(user);
        loading.value = false;
    } else {
        loading.value = false;
    }
});
</script>

<template>
    <LoadingElement v-if="loading" />
    <slot v-else></slot>
</template>