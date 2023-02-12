<script setup>
import { reactive, onMounted } from 'vue';
import { Category } from "@/api/category";

const categories = reactive([]);

const categoryApi = new Category();

onMounted(async () => {
    const response = await categoryApi.findAll({ page: 1, itemsPerPage: 20, filters: [], orders: {} });
    console.log(response["hydra:member"]);
    categories.push(...response["hydra:member"]);
});

</script>

<template>
    <template v-for="category in categories">
        <option :value="category.id">{{ category.name }}</option>
    </template>
</template>