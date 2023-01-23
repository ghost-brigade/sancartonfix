<script setup>
import {useRoute} from 'vue-router';
import HousingEditForm from "@/components/forms/HousingEditForm.vue";
const $routes = useRoute();
const { id } = $routes.params;
</script>

<template>
    <HousingEditForm :housingId="id" />
</template>
